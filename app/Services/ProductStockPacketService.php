<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductStockLogActionEnum;
use App\Repositories\OrderItemRepository;
use App\Repositories\ProductStockPacketRepository;
use App\Repositories\ProductStockRepository;
use Illuminate\Support\Facades\Auth;

class ProductStockPacketService
{
    private const DEFAULT_PRODUCT_STOCK_PACKET_QUANTITY = 1;
    private const SUBTRACTION_SIGN = 0;
    private const ADDITION_SIGN = 1;

    protected $productStockPacketRepository;

    protected $orderItemRepository;

    protected $productStockRepository;

    protected $productStockPositionService;

    protected $productStockLogService;

    protected $productStockService;

    public function __construct(
        ProductStockPacketRepository $productStockPacketRepository,
        OrderItemRepository $orderItemRepository,
        ProductStockRepository $productStockRepository,
        ProductStockPositionService $productStockPositionService,
        ProductStockLogService $productStockLogService,
        ProductStockService $productStockService
    )
    {
        $this->productStockPacketRepository = $productStockPacketRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productStockPositionService = $productStockPositionService;
        $this->productStockLogService = $productStockLogService;
        $this->productStockService = $productStockService;
    }

    public function findPacket(int $packetId)
    {
        return $this->productStockPacketRepository->find($packetId);
    }

    public function createProductPacket(
        string $packetQuantity,
        string $packetName,
        string $packetProductQuantity,
        int $productStockId
    ): void {
        $packetQuantitySummary = $this->getProductsQuantityInCreatedPackets(
            intval($packetQuantity),
            intval($packetProductQuantity)
        );

        $productStock = $this->productStockRepository->find($productStockId);

        $this->productStockPacketRepository->create([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
            'packet_product_quantity' => $packetProductQuantity,
            'product_stock_id' => $productStockId,
        ]);

        $productStockFirstPosition = $productStock->position->first();

        $packetDifference = -abs($packetQuantitySummary);

        $this->updateGlobalAndPositionStockQuantity($productStockFirstPosition, $packetDifference, self::SUBTRACTION_SIGN, $packetQuantitySummary, $productStock);
    }

    /**
     * @return mixed
     */
    public function reducePacketQuantityAfterAssignToOrderItem(int $packetId)
    {
        $productStockPacket = $this->findPacket($packetId);

        return $productStockPacket->update([
            'packet_quantity' => $productStockPacket->packet_quantity - self::DEFAULT_PRODUCT_STOCK_PACKET_QUANTITY,
        ]);
    }

    public function updatePacketQuantity(
        string $packetQuantity,
        string $packetName,
        string $packetProductQuantity,
        int $productStockId,
        int $packetId
    ): void {
        $packetQuantity = intval($packetQuantity);
        $packetProductQuantity = intval($packetProductQuantity);

        $productStockPacket = $this->findPacket($packetId);

        $currentPacketQuantityDifference = $this->getPacketQuantityDifferenceAfterUpdate(
            $productStockPacket->packet_quantity,
            $productStockPacket->packet_product_quantity,
            $packetQuantity,
            $packetProductQuantity
        );

        $productStock = $this->productStockService->findProductStock($productStockId);

        $productStockFirstPosition = $productStock->position->first();
        $currentPacketQuantity = $this->getProductsQuantityInCreatedPackets($packetQuantity, $packetProductQuantity);

        $this->updateGlobalAndPositionStockQuantity($productStockFirstPosition, $currentPacketQuantityDifference, self::SUBTRACTION_SIGN, $currentPacketQuantity, $productStock);

        $this->updateProductStockPacket($packetQuantity, $packetName, $packetProductQuantity, $productStockId, $packetId);
    }

    private function updateProductStockPacket(int $packetQuantity, string $packetName, int $packetProductQuantity, int $productStockId, int $packetId)
    {
        $this->productStockPacketRepository->update([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
            'packet_product_quantity' => $packetProductQuantity,
            'product_stock_id' => $productStockId,
        ], $packetId);
    }

    private function updateGlobalAndPositionStockQuantity($productStockFirstPosition, int $currentPacketQuantityDifference, int $sign, int $currentPacketQuantity, $productStock)
    {
        $this->productStockPositionService->updateProductPositionQuantity(
            $productStockFirstPosition->position_quantity,
            $currentPacketQuantityDifference,
            $productStockFirstPosition->id,
            $sign
        );

        $this->productStockService->updateProductStockQuantity(
            $productStock->quantity,
            $currentPacketQuantityDifference,
            $productStock->id,
            $sign
        );

        $action = ($currentPacketQuantityDifference < 0) ? ProductStockLogActionEnum::DELETE : ProductStockLogActionEnum::ADD;

        $this->productStockLogService->storeProductQuantityChangeLog(
            $productStock->id,
            $productStockFirstPosition->id,
            $currentPacketQuantity,
            $action,
            Auth::user()->id
        );
    }

    private function getProductsQuantityInCreatedPackets(int $packetQuantity, int $productQuantityInPacket): int
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

    /**
     * @return mixed
     */
    public function assignPacket(int $orderItemId, int $packetId)
    {
        return $this->assignPacketToOrderItem($orderItemId, $packetId);
    }

    /**
     * @return mixed
     */
    private function assignPacketToOrderItem(int $orderItemId, int $packetId)
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);

        $orderItem->update([
            'product_stock_packet_id' => $packetId,
        ]);

        return $orderItem;
    }

    public function unassignPacket(int $orderItemId): array
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);

        $productStockPacket = $this->findPacket($orderItem->product_stock_packet_id);

        $productStockPacket->update([
            'packet_quantity' => $productStockPacket->packet_quantity + self::DEFAULT_PRODUCT_STOCK_PACKET_QUANTITY
        ]);

        $this->unassignPacketFromOrderItem($orderItem);

        return ['order_item_name' => $orderItem->product->name, 'packet_name' => $productStockPacket->packet_name];
    }

    public function unassignPacketFromOrderItem($orderItem): void {
        $orderItem->update([
            'product_stock_packet_id' => null
        ]);
    }

    /**
     * @return mixed
     */
    public function deletePacket(int $packetId)
    {
        $packet = $this->findPacket($packetId);

        $currentPacketQuantity = $this->getProductsQuantityInCreatedPackets(
            $packet->packet_quantity,
            $packet->packet_product_quantity
        );

        $productStock = $this->productStockService->findProductStock($packet->product_stock_id);

        $productStockFirstPosition = $productStock->position->first();

        $this->updateGlobalAndPositionStockQuantity($productStockFirstPosition, $currentPacketQuantity, self::ADDITION_SIGN, $currentPacketQuantity, $productStock);

        return $packet->delete();
    }
}
