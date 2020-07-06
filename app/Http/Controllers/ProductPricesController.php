<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use Illuminate\Http\Request;

class ProductPricesController extends Controller
{
    public function getAllegroPrices(Request $request, $id = 'test')
    {
        $order = Order::find($id);
        if (empty($order)) {
            return response(['error' => 'Zlecenie nie istnieje']);
        }
        $newPrices = $order->items->map(function ($item) {
            return ['id' => $item->id, 'price' => $item->product->price->allegro_selling_gross_commercial_price];
        });
        return response(['content' => $newPrices]);
    }
}
