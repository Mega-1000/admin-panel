<?php

namespace App\Http\Controllers;

use App\Entities\ProductStockPosition;
use Illuminate\View\View;

class ProductStockPositionDamagedController extends Controller
{
    public function __invoke(int $id): View
    {
        return view('product_stocks.positions.damaged', [
            'position' => ProductStockPosition::find($id),
        ]);
    }
}
