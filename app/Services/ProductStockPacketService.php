<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductStockLogActionEnum;
use App\Repositories\OrderItemRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockPacketItemRepository;
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

    protected $productStockPacketItemRepository;

    protected $productRepository;

    public function __construct(
        ProductStockPacketRepository $productStockPacketRepository,
        OrderItemRepository $orderItemRepository,
        ProductStockRepository $productStockRepository,
        ProductStockPositionService $productStockPositionService,
        ProductStockLogService $productStockLogService,
        ProductStockService $productStockService,
        ProductStockPacketItemRepository $productStockPacketItemRepository,
        ProductRepository $productRepository
    )
    {
        $this->productStockPacketRepository = $productStockPacketRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productStockPositionService = $productStockPositionService;
        $this->productStockLogService = $productStockLogService;
        $this->productStockService = $productStockService;
        $this->productStockPacketItemRepository = $productStockPacketItemRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @return mixed
     */
    public function findPacket(int $packetId)
    {
        return $this->productStockPacketRepository->find($packetId);
    }

    public function createProductPacket(
        string $packetQuantity,
        string $packetName,
        array $products
    ): void {
        $packet = $this->productStockPacketRepository->create([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
        ]);

        foreach($products[0] as $productId => $productQuantity) {
            $this->productStockPacketItemRepository->create([
                'product_id' => $productId,
                'product_stock_packet_id' => $packet->id,
                'quantity' => $productQuantity,
            ]);

            $product = $this->productRepository->find($productId);
            $this->updateGlobalAndPositionStockQuantity($product->stock->position->first(), -abs($productQuantity), self::SUBTRACTION_SIGN, (int)$productQuantity, $product->stock);
        }
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
        string $packetName,
        string $packetQuantity,
        array $products,
        string $packetId
    ): void {

        $packet = $this->productStockPacketRepository->update([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
        ], $packetId);

        foreach($products as $product) {
            if($packetItem = $this->productStockPacketItemRepository->findWhere(
                ['product_id' => $product['id'], 'product_stock_packet_id' => $packetId]
            )->first()) {
                $packetItem->update([
                    'quantity' => $product['quantity']
                ]);
            } else {
                $this->productStockPacketItemRepository->create([
                    'product_id' => $product['id'],
                    'product_stock_packet_id' => $packet->id,
                    'quantity' => $product['quantity'],
                ]);
            }

            $product = $this->productRepository->find($product['id']);
            $this->updateGlobalAndPositionStockQuantity($product->stock->position->first(), -abs($product['quantity']), self::SUBTRACTION_SIGN, (int)$product['id'], $product->stock);
        }
    }

    private function updateProductStockPacket(int $packetQuantity, string $packetName, int $packetProductQuantity, int $productStockId, int $packetId): void
    {
        $this->productStockPacketRepository->update([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
            'packet_product_quantity' => $packetProductQuantity,
            'product_stock_id' => $productStockId,
        ], $packetId);
    }

    private function updateGlobalAndPositionStockQuantity(
        $productStockFirstPosition,
        int $currentPacketQuantityDifference,
        int $sign,
        int $currentPacketQuantity,
        $productStock
    ): void
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

        dd($currentPacketQuantityDifference);

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
