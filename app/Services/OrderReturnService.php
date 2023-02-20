<?php namespace App\Services;

use App\Entities\OrderReturn;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OrderReturnService
{
    public function saveFile($photo): string
    {
        $path = 'orderReturn/' . date('F') . date('Y');
        return $photo->store('public/' . $path);
    }

    public function saveReturn(array $request): null
    {
        $orderReturn = new OrderReturn();
        if($request['id']){
            $orderReturn = OrderReturn::find($request['id']);
        }

        $orderReturn->order_id = $request['order_id'];
        $orderReturn->product_id = $request['product_id'];
        $orderReturn->product_stock_position_id = $request['position_id'];
        $orderReturn->user_id = Auth::user()->id;
        $orderReturn->quantity_undamaged = ($request['undamaged']?$request['undamaged']:0);
        $orderReturn->quantity_damaged = ($request['damaged']?$request['damaged']:0);
        $orderReturn->description = $request['description'];
        $orderReturn->photo = $request['photoPath'];
        $orderReturn->save();
        
        unset($orderReturn);
        return null;
    }

    public function saveStockPosition(int $stockID, array $request): int
    {
        $stockPosition = new ProductStockPosition();
        $stockPosition->product_stock_id = $stockID;
        $stockPosition->lane = $request['lane'];
        $stockPosition->bookstand = $request['bookstand'];
        $stockPosition->shelf = $request['shelf'];
        $stockPosition->position = $request['position'];
        $stockPosition->position_quantity = 0;
        $stockPosition->save();

        return $stockPosition->id;
    }


    public function updateStockPosition(int $positionId, int $undamaged): null
    {
        $stock = ProductStockPosition::find($positionId);
        $quantity = $stock->position_quantity;
        $stock->position_quantity = $quantity + $undamaged;
        $stock->save();
        return null;
    }
}