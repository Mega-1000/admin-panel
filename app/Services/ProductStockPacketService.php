<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderItemRepository;
use App\Repositories\ProductStockPacketRepository;
use Illuminate\Support\Facades\Log;

class ProductStockPacketService
{
    protected $productStockPacketRepository;

    protected $orderItemRepository;

    public function __construct(
        ProductStockPacketRepository $productStockPacketRepository, 
        OrderItemRepository $orderItemRepository
    )
    {
        $this->productStockPacketRepository = $productStockPacketRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function findPacket($packetId)
    {
        return $this->productStockPacketRepository->find($packetId);
    }

    public function createProductPacket(
        int $packetQuantity,
        string $packetName,
        int $packetProductQuantity,
        int $productStockId
    ): void {
        $this->productStockPacketRepository->create([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
            'packet_product_quantity' => $packetProductQuantity,
            'product_stock_id' => $productStockId,
        ]);
    }

    public function reducePacketQuantityAfterAssignToOrderItem($packetId): void
    {
        $productStockPacket = $this->findPacket($packetId);
        if(empty($productStockPacket)) {
            Log::error('Cannot find ProductStockPacket using id: ' . $packetId);
            abort(404);
        }
        $productStockPacket->update([
            'packet_quantity' => $productStockPacket->packet_quantity - 1,
        ]);
    }

    public function updatePacketQuantity(
        int $packetQuantity,
        string $packetName,
        int $packetProductQuantity,
        int $productStockId,
        int $packetId
    ): void {
        $this->productStockPacketRepository->update([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
            'packet_product_quantity' => $packetProductQuantity,
            'product_stock_id' => $productStockId,
        ], $packetId);
    }

    public function getProductsQuantityInCreatedPackets(int $packetQuantity, int $productQuantityInPacket): int
    {
        return $packetQuantity * $productQuantityInPacket;
    }

    public function getPacketQuantityDifferenceAfterUpdate(
        int $packetQuantityBeforeUpdate,
        int $productQuantityInPacketBeforeUpdate,
        int $currentPacketQuantity,
        int $currentProductQuantityInPacket
    ): int
    {
        return $this->getProductsQuantityInCreatedPackets($packetQuantityBeforeUpdate, $productQuantityInPacketBeforeUpdate) - $this->getProductsQuantityInCreatedPackets($currentPacketQuantity, $currentProductQuantityInPacket);
    }

    public function assignPacketToOrderItem($orderItemId, $packetId): void
    {
        $this->orderItemRepository->find($orderItemId)->update([
            'product_stock_packet_id' => $packetId
        ]);
    }

    public function unassignPacketFromOrderItem($orderItemId): void
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);

        if(empty($orderItem)) {
            Log::error('Cannot find order item using id: ' . $orderItemId);
            abort(404);
        }

        $productStockPacket = $this->findPacket($orderItem->product_stock_packet_id);

        $productStockPacket->update([
            'packet_quantity' => $productStockPacket->packet_quantity + 1
        ]);

        $orderItem->update([
            'product_stock_packet_id' => null
        ]);
    }
}
