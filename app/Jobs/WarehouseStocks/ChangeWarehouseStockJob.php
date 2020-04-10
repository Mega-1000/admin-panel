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

        foreach($order->items as $item) {
            $productName = $item->product->symbol;
            preg_match('/([^-]+)/', $productName, $matches);
            $product = Product::withTrashed()->where('symbol', $matches[1])->first();
            if($product !== null) {
                $productStock = $productStockRepository->findWhere(['product_id' => $product->id])->first();
            } else {
                $productStock = $productStockRepository->findWhere(['product_id' => $item->product->id])->first();
            }

            $productStockPosition = $productStockPositionRepository->findWhere(['product_stock_id' => $productStock->id])->first();
            if(empty($productStockPosition)) {
                return response()->json(['error' => 'position', 'product' => $product ? $product->id : $item->product->id, 'productName' => $product ? $product->symbol : $item->product->symbol]);
            }

            $productStockLog = $productStockLogRepository->findWhere([
                'product_stock_id' => $productStock->id,
                'order_id' => $this->orderId,
                'action' => 'DELETE',
            ])->first();

            if(!empty($productStockLog)) {
                return response()->json(['error' => 'exists']);
            }

        }

        foreach($order->items as $item) {
            $productName = $item->product->symbol;
            preg_match('/([^-]+)/', $productName, $matches);
            $product = Product::withTrashed()->where('symbol', $matches[1])->first();
            if($product !== null) {
                $productStock = $productStockRepository->findWhere(['product_id' => $product->id])->first();
            } else {
                $productStock = $productStockRepository->findWhere(['product_id' => $item->product->id])->first();
            }
            $productStockRepository->update([
                'quantity' => $productStock->quantity - $item->quantity,
            ], $productStock->id);


            $productStockPosition = $productStockPositionRepository->findWhere(['product_stock_id' => $productStock->id])->first();

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
