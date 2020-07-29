<?php

namespace App\Jobs\Orders;

use App\Entities\Product;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\Job;
use App\Repositories\OrderRepository;
use App\Repositories\ProductStockLogRepository;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;

class ChangeWarehouseStockJob extends Job
{
    /**
     * @var $orderId
     */
    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;

    }

    public function handle(
        OrderRepository $orderRepository,
        ProductStockRepository $productStockRepository,
        ProductStockPositionRepository $productStockPositionRepository,
        ProductStockLogRepository $productStockLogRepository,
        ProductRepository $productRepository
    ) {
        $orderId = json_decode($this->orderId);
        $this->orderId = $orderId->id;

        $order = $orderRepository->find($this->orderId);

        $errors = [];

        foreach($order->items as $item) {
            $product = $item->product;
            if($product !== null) {
                $productStockPosition = $product->stock->position->first();
                if(empty($productStockPosition)) {
                    $errors[] = ['error' => 'position', 'product' => $product->id, 'productName' => $product->symbol, 'productStock' => $product->stock];
                    continue;
                }

                if($productStockPosition->position_quantity <= $item->quantity) {
                    $errors[] = ['error' => 'quantity', 'product' => $product->id, 'productName' => $product->symbol, 'productStock' => $product->stock, 'position' => $productStockPosition];
                    continue;
                }

                $product->stock->logs()->where('order_id', $this->orderId)->where('action', 'DELETE')->first();

                if(!empty($productStockLog)) {
                    $errors[] = ['error' => 'exists', 'product' => $product->id, 'productName' => $product->symbol, 'order_id' => $order->id];
                    continue;
                }
            }
        }
        if(count($errors) > 0) {
            return response()->json($errors);
        }

        foreach($order->items as $item) {
            $product = $item->product;
            if($product === null) {
                return response()->json(['error' => 'Product does not exist.']);
            }
            $product->stock->logs()->where('order_id', $this->orderId)->where('action', 'DELETE')->first();

            if(!empty($productStockLog)) {
                return response()->json(['error' => 'exists']);
            }
            $productStock = $product->stock;
            $productStockRepository->update([
                'quantity' => $productStock->quantity - $item->quantity,
            ], $productStock->id);


            $productStockPosition = $product->stock->position->first();
            $productStockPositionRepository->update([
                'position_quantity' => $productStockPosition->position_quantity - $item->quantity
            ], $productStockPosition->id);

            $productStockLogRepository->create([
                'product_stock_id' => $productStock->id,
                'product_stock_position_id' => $productStockPosition->id,
                'action' => 'DELETE',
                'quantity' => $item->quantity,
                'order_id' => $this->orderId,
                'user_id' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }


    }
}
