<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductStockPacketService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProductStockPacketsController extends Controller
{
    /**
     * @var ProductStockPacketService
     */
    protected $productStockPacketService;

    public function __construct(ProductStockPacketService $productStockPacketService) {
        $this->productStockPacketService = $productStockPacketService;
    }

    public function assign(int $packetId, int $orderItemId): JsonResponse
    {
        try {
            $packet = $this->productStockPacketService->reducePacketQuantityAfterAssignToOrderItem($packetId);
            $orderItemName = $this->productStockPacketService->assignPacket($orderItemId, $packetId);

            Log::info('Packet ' . $packetId . ' was assigned to order item ' . $orderItemId);

            return response()->json(['order_item_name' => $orderItemName->product->name, 'packet_name' => $packet->packet_name]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not found']);
        }
    }

    public function retain(int $orderItemId): JsonResponse
    {
        $data = $this->productStockPacketService->unassignPacket($orderItemId);

        return response()->json($data);
    }
}
