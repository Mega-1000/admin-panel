<?php 

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductStockPacketRepository;

class ProductStockPacketService
{
    protected $repository;

    public function __construct(ProductStockPacketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(
        int $packetQuantity, 
        string $packetName, 
        int $packetProductQuantity, 
        int $productStockId
    ): void {
        $this->repository->create([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
            'packet_product_quantity' => $packetProductQuantity,
            'product_stock_id' => $productStockId,
        ]);
    }

    public function update(
        int $packetQuantity, 
        string $packetName, 
        int $packetProductQuantity, 
        int $productStockId, 
        int $packetId
    ): void {
        $this->repository->update([
            'packet_quantity' => $packetQuantity,
            'packet_name' => $packetName,
            'packet_product_quantity' => $packetProductQuantity,
            'product_stock_id' => $productStockId,
        ], $packetId);
    }
}
