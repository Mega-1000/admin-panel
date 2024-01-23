<?php

namespace App\Http\Middleware;

use Closure;
use App\Entities\OrderInvoiceValue;
use Illuminate\Database\Query\Builder;

class FilterOrderInvoiceValue
{
    public function handle($request, Closure $next)
    {
        if ($request->get('invoice-kind') === 'faktury sprzedazy') {
            OrderInvoiceValue::addGlobalScope('selling', function (Builder $builder) {
                $builder->where('type', 'selling');
            });
        } else {
            OrderInvoiceValue::addGlobalScope('buying', function (Builder $builder) {
                $builder->where('type', 'buying');
            });
        }

        return $next($request);
    }
}
