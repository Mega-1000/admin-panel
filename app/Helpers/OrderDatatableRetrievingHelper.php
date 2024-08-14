<?php

namespace App\Helpers;

use App\Entities\Order;
use App\OrderDatatableColumn;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class OrderDatatableRetrievingHelper
{
    public static function applyOneLevelNestedFilters(array $labelParts, OrderDatatableColumn $column, Builder $q): Builder
    {
        return $q->whereHas($labelParts[0], function ($q) use ($labelParts, $column) {
            $q->where($labelParts[1], 'like', '%' . $column->filter . '%');
        });
    }

    public static function applyTwoLevelNestedFilters(array $labelParts, OrderDatatableColumn $column, Builder $q): Builder
    {
        return $q->whereHas($labelParts[0], function ($q) use ($labelParts, $column) {
            $q->whereHas($labelParts[1], function ($q) use ($labelParts, $column) {
                $q->where($labelParts[3], 'like', '%' . $column->filter . '%');
            });
        });
    }

    public static function getOrderQueryWithRelations(): Builder
    {
        $q = Order::query();

        return $q->with([
            'labels.labelGroup',
            'invoiceValues',
            'payments',
            'items',
            'allegroGeneralExpenses',
            'otherPackages',
            'customer.addresses',
            'files',
            'packages.realCostsForCompany',
            'warehouse',
            'chat.messages',
            'task.user',
            'task.taskTime',
            'addresses',
            'invoices',
            'dates',
            'chat.auctions',
        ]);
    }

    public static function applyGeneralFilters(Builder $q, string $authenticatedUserGridSettings): Builder
    {
        $decodedGridSettings = json_decode($authenticatedUserGridSettings);
        $data = $decodedGridSettings;
        if ($decodedGridSettings !== null && is_object($data) && property_exists($data, 'order_package_filter_number') && $data->order_package_filter_number) {
            $q->whereHas('packages', function (Builder $query) use ($data) {
                $query->where('letter_number', 'like', '%' . $data->order_package_filter_number. '%');
            });
        }

        if ($decodedGridSettings !== null && is_object($data) && property_exists($data, 'is_sorting_by_preferred_invoice_date') && $data->is_sorting_by_preferred_invoice_date) {
            $q->where('preferred_invoice_date', '!=', null);
            $q->orderByRaw('DATE(preferred_invoice_date) DESC');
        }

        if ($decodedGridSettings !== null && is_object($data) && property_exists($data, 'only_styro') && $data->only_styro) {
            $q->whereHas('items', function ($query) {    $query->whereHas('product', function ($subQuery) {        $subQuery->where('variation_group', 'styropiany');
            });});
        }

        if ($decodedGridSettings !== null && is_object($data) && property_exists($data, 'only_styro') && $data->only_styro) {
            $q->whereHas('payments');
        }

        return $q;
    }

    public static function applyNestedFilters(Collection $columns, Builder $q): Builder
    {
        foreach ($columns as $column) {
            if (!self::isNestedFilter($column)) {
                if ($column->label === 'customer.addresses.0.phone') {
                    $column->filter = str_replace(' ', '', $column->filter);
                    $column->filter = str_replace('-', '', $column->filter);
                }

                $q->where($column->label, 'like', '%' . $column->filter . '%');
                continue;
            }

            $q = self::applyNestedFilter($q, $column);
        }

        return $q;
    }

    /**
     * @param Builder $q
     * @param mixed $column
     * @return Builder
     */
    private static function applyNestedFilter(Builder $q, mixed $column): Builder
    {
        $labelParts = explode('.', $column->label);

        return array_key_exists(2, $labelParts) && is_numeric($labelParts[2]) ?
            OrderDatatableRetrievingHelper::applyTwoLevelNestedFilters($labelParts, $column, $q)
            : OrderDatatableRetrievingHelper::applyOneLevelNestedFilters($labelParts, $column, $q);
    }


    /**
     * @param mixed $column
     * @return bool
     */
    private static function isNestedFilter(mixed $column): bool
    {
        return str_contains($column->label, '.');
    }
}
