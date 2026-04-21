<?php namespace App\Services;

use App\Entities\OrderReturn;
use App\Entities\ProductStockPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Entities\ProductStockLog;

class OrderReturnService
{
    public function saveFile($photo): string
    {
        $path = 'orderReturn/' . date('F') . date('Y');
        return $photo->store('public/' . $path);
    }

    public function updateFile($id, $photo): string
    {
        $orderReturn = OrderReturn::find($id);
        if ($photo && $orderReturn->photo && Storage::disk('public')->exists(str_replace('public/','',$orderReturn->photo))) {
            Storage::delete($orderReturn->photo);
        }
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
        $orderReturn->quantity_undamaged = ($request['undamaged'] ? $request['undamaged'] : 0);
        $orderReturn->quantity_damaged = ($request['damaged'] ? $request['damaged'] : 0);
        $orderReturn->description = $request['description'] ?: '';
        if($request['photoPath']){
            $orderReturn->photo = $request['photoPath'] ?: '';
        }
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


    public function updateStockPositionDamaged(?OrderReturn $orderReturn, int $positionId, int $damaged, int $productStockId, int $orderId): null
    {
        $prevDamaged = $orderReturn?->quantity_damaged ?? 0;
        $damaged = $damaged - $prevDamaged;

        if($damaged > 0) {
            ProductStockLog::create([
                'product_stock_id' => $productStockId,
                'product_stock_position_id' => $positionId,
                'order_id' => $orderId,
                'action' => 'DAMAGED',
                'quantity' => $damaged,
                'user_id' => auth()->user()->id,
            ]);
        }
        
        return null;
    }

    public function updateStockPosition(?OrderReturn $orderReturn, int $positionId, int $undamaged, int $productStockId, int $orderId): null
    {
        $prevUndamaged = $orderReturn?->quantity_undamaged ?? 0;
        $undamaged = $undamaged - $prevUndamaged;
        
        $stock = ProductStockPosition::find($positionId);
        $quantity = $stock->position_quantity;
        $stock->position_quantity = $quantity + $undamaged;
        $stock->save();

        if($undamaged > 0) {
            ProductStockLog::create([
                'product_stock_id' => $productStockId,
                'product_stock_position_id' => $positionId,
                'order_id' => $orderId,
                'action' => 'ADD',
                'quantity' => $undamaged,
                'user_id' => auth()->user()->id,
            ]);
        }
        
        return null;
    }
}
