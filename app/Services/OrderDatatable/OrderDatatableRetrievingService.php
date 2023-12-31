<?php

namespace App\Services\OrderDatatable;

use App\Enums\OrderDatatableColumnsEnum;
use App\Helpers\OrderDatatableNonstandardFiltersHelper;
use App\Helpers\OrderDatatableRetrievingHelper;
use App\OrderDatatableColumn;
use App\Repositories\OrderDatatableColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderDatatableRetrievingService
{
    public static array $orders = [];

    /**
     * Fetch orders for datatable for current user save it in $orders property witch is static
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fetchOrders(): array
    {
        $q = OrderDatatableRetrievingHelper::getOrderQueryWithRelations();
        $columns = OrderDatatableColumns::getAllStandardColumns();

        $q = OrderDatatableRetrievingHelper::applyNestedFilters($columns, $q);

        $q = OrderDatatableNonstandardFiltersHelper::applyNonstandardFilters($q);
        $q = OrderDatatableRetrievingHelper::applyGeneralFilters($q);

        return $this->assignOrdersToClassProperty($q);
    }

    /**
     * Get orders for datatable for current user
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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

            $order['values_data'] = [
                'sum_of_gross_values' => $sum_of_gross_values,
                'products_value_gross' => $products_value_gross,
                'shipment_price_for_client' => $order['shipment_price_for_client'] ?? 0,
                'additional_cash_on_delivery_cost' => $order['additional_cash_on_delivery_cost'] ?? 0,
                'additional_service_cost' => $order['additional_service_cost'] ?? 0
            ];
        }
    }

    /**
     * Execute database query and assign orders to class property
     *
     * @param Builder $q
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function assignOrdersToClassProperty(Builder $q): array
    {
        try {
            self::$orders = $q->orderBy('created_at', 'desc')->paginate(session()->get('pageLength', 10))->toArray();

            $this->prepareAdditionalDataForOrders();
        } catch (QueryException $e) {
            try {
                self::$orders = $q->paginate(10)->toArray();
            } catch (QueryException $e) {
                OrderDatatableColumn::all()->each(fn($column) => $column->delete());
                self::$orders = $q->orderBy('created_at', 'desc')->paginate(session()->get('pageLength', 10))->toArray();
            }
        }

        return self::$orders;
    }
}
