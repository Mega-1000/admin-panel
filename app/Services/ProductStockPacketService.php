<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductStockLogActionEnum;
use App\Repositories\OrderItemRepository;
use App\Repositories\ProductStockPacketRepository;
use App\Repositories\ProductStockRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ProductStockPacketService
{
    private const DEFAULT_PRODUCT_STOCK_PACKET_QUANTITY = 1;
    private const SUBTRACTION_SIGN = 0;
    const ADDITION_SIGN = 1;

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

        $this->productStockPositionService->updateProductPositionQuantity(
            $productStockFirstPosition->position_quantity,
            $packetQuantitySummary, $productStockFirstPosition->id,
            self::SUBTRACTION_SIGN
        );

        $this->productStockLogService->storeProductQuantityChangeLog(
            $productStock->id,
            $productStockFirstPosition->id,
            $packetQuantitySummary,
            ProductStockLogActionEnum::DELETE,
            Auth::user()->id
        );
    }

    public function reducePacketQuantityAfterAssignToOrderItem(int $packetId)
    {
        $productStockPacket = $this->findPacket($packetId);

        if(empty($productStockPacket)) {
            throw new ModelNotFoundException();
        }

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
        $currentPacketQuantity = $this->getProductsQuantityInCreatedPackets(
            $packetQuantity,
            $packetProductQuantity
        );

        $this->productStockService->updateProductStockQuantity(
            $productStockFirstPosition->position_quantity,
            $currentPacketQuantity,
            $productStockFirstPosition->id
        );

        $this->productStockPositionService->updateProductPositionQuantity(
            $productStockFirstPosition->position_quantity,
            $currentPacketQuantityDifference,
            $productStockFirstPosition->id,
            self::ADDITION_SIGN
        );

        $this->productStockService->updateProductStockQuantity(
            $productStock->quantity,
            $currentPacketQuantityDifference,
            $productStock->id
        );

        $action = ($currentPacketQuantityDifference < 0) ? ProductStockLogActionEnum::DELETE : ProductStockLogActionEnum::ADD;

        $this->productStockLogService->storeProductQuantityChangeLog(
            $productStock->id,
            $productStockFirstPosition->id,
            $currentPacketQuantity,
            $action,
            Auth::user()->id
        );

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

    public function assignPacketToOrderItem(int $orderItemId, int $packetId): string
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);

        if(empty($orderItem)) {
            throw new ModelNotFoundException();
        }

        $orderItem->update([
            'product_stock_packet_id' => $packetId,
        ]);

        return $orderItem->product->name;
    }

    public function unassignPacketFromOrderItem(int $orderItemId): array
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);

        if(empty($orderItem)) {
           throw new ModelNotFoundException();
        }

        $productStockPacket = $this->findPacket($orderItem->product_stock_packet_id);

        $productStockPacket->update([
            'packet_quantity' => $productStockPacket->packet_quantity + self::DEFAULT_PRODUCT_STOCK_PACKET_QUANTITY
        ]);

        $orderItem->update([
            'product_stock_packet_id' => null
        ]);

        return ['order_item_name' => $orderItem->product->name, 'packet_name' => $productStockPacket->packet_name];
    }
}
