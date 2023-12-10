<?php

namespace App\Services\OrderDatatable;

use App\Entities\Order;
use App\Enums\OrderDatatableColumnsEnum;
use App\Helpers\OrderDatatableNonstandardFiltersHelper;
use App\OrderDatatableColumn;
use App\Repositories\OrderDatatableColumns;
use Illuminate\Database\Eloquent\Builder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderDatatableRetrievingService
{
    public static array $orders = [];

    /**
     * Fetch orders for datatable for current user save it in $orders property witch is static
     *
     * @return void
     */
    public function fetchOrders(): void
    {
        $q = Order::query();
        $q->with([
            'labels',
            'labels.labelGroup',
            'invoiceValues',
            'payments',
            'items',
            'allegroGeneralExpenses',
            'otherPackages',
            'customer',
            'customer.addresses',
            'files',
            'packages.realCostsForCompany',
        ]);

        $columns = OrderDatatableColumn::where('filter', '!=', '')->get();
        $columns = $columns->filter(function ($column) {
            return !in_array($column->label, array_keys(OrderDatatableColumnsEnum::NON_STANDARD_FILTERS_CLASSES));
        });

        foreach ($columns as $column) {
            if (!$this->isNestedFilter($column)) {
                $q->where($column->label, 'like', '%' . $column->filter . '%');
                continue;
            }

            $q = $this->applyNestedFilter($q, $column);
        }

        foreach (OrderDatatableNonstandardFiltersHelper::composeClasses() as $columnName => $nonStandardColumnFilterClass) {
            $q = $nonStandardColumnFilterClass->applyFilter($q, $columnName);
        }

        if (($data = json_decode(auth()->user()->grid_settings)) !== null && is_object($data) && $data->order_package_filter_number) {
            $q->whereHas('packages', function (Builder $query) use ($data) {
                $query->where('letter_number', 'like', '%' . $data->order_package_filter_number. '%');
            });
        }

        try {
            self::$orders = $q->orderBy('created_at', 'desc')->paginate(session()->get('pageLength', 10))->toArray();

            $this->prepareAdditionalDataForOrders();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            self::$orders = $q->paginate(10)->toArray();
        }
    }

    /**
     * Get orders for datatable for current user
     *
     * @return array
     */
    public function getOrders(): array
    {
        if (empty(static::$orders)) {
            $this->fetchOrders();
        }


        return self::$orders;
    }

    /**
     * Get columns for datatable for current user
     *
     * @return array[]
     */
    public static function getColumnNames(): array
    {
        $dtColumns = OrderDatatableColumn::where('hidden', false)->get()->toArray();

        if (count($dtColumns) === 0) {
            $dtColumns = OrderDatatableColumnsEnum::DEFAULT_COLUMNS;

            OrderDatatableColumns::reCreateForUser($dtColumns, auth()->id());
        }

        return $dtColumns;
    }

    /**
     * @param mixed $column
     * @return bool
     */
    private function isNestedFilter(mixed $column): bool
    {
        return str_contains($column->label, '.');
    }

    /**
     * @param Builder $q
     * @param mixed $column
     * @return Builder
     */
    private function applyNestedFilter(Builder $q, mixed $column): Builder
    {
        $labelParts = explode('.', $column->label);

        if (is_numeric($labelParts[2])) {
            $q->whereHas($labelParts[0], function ($q) use ($labelParts, $column) {
                $q->whereHas($labelParts[1], function ($q) use ($labelParts, $column) {
                    $q->where($labelParts[3], 'like', '%' . $column->filter . '%');
                });
            });
            return $q;
        }

        return $q;
    }

    /**
     * @return void
     */
    private function prepareAdditionalDataForOrders(): void
    {
        foreach (self::$orders['data'] as &$order) {
            $additional_service = $order['additional_service_cost'] ?? 0;
            $additional_cod_cost = $order['additional_cash_on_delivery_cost'] ?? 0;
            $shipment_price_client = $order['shipment_price_for_client'] ?? 0;
            $totalProductPrice = 0;

            foreach ($order['items'] as $item) {
                $price = $item['gross_selling_price_commercial_unit'] ?: $item['net_selling_price_commercial_unit'] ?: 0;
                $quantity = $item['quantity'] ?? 0;
                $totalProductPrice += $price * $quantity;
            }

            $products_value_gross = round($totalProductPrice, 2);
            $sum_of_gross_values = round($totalProductPrice + $additional_service + $additional_cod_cost + $shipment_price_client, 2);

            $order['values_data'] = array(
                'sum_of_gross_values' => $sum_of_gross_values,
                'products_value_gross' => $products_value_gross,
                'shipment_price_for_client' => $order['shipment_price_for_client'] ?? 0,
                'additional_cash_on_delivery_cost' => $order['additional_cash_on_delivery_cost'] ?? 0,
                'additional_service_cost' => $order['additional_service_cost'] ?? 0
            );
        }
    }
}
