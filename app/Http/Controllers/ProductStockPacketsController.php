<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductStockPacketCreateRequest;
use App\Http\Requests\ProductStockPacketUpdateRequest;
use App\Repositories\OrderItemRepository;
use App\Repositories\ProductStockLogRepository;
use App\Repositories\ProductStockPacketRepository;
use App\Repositories\ProductStockRepository;
use App\Services\ProductStockLogService;
use App\Services\ProductStockPacketService;
use App\Services\ProductStockPositionService;
use App\Services\ProductStockService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ProductStockPacketsController extends Controller
{
    /**
     * @var ProductStockRepository
     */
    protected $repository;

    /**
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * @var ProductStockLogRepository
     */
    protected $productStockLogRepository;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var ProductStockPacketService
     */
    protected $productStockPacketService;

    /**
     * @var ProductStockService
     */
    protected $productStockService;

    /**
     * @var ProductStockLogService
     */
    protected $productStockLogService;

    /**
     * @var ProductStockPositionService
     */
    protected $productStockPositionService;

    public function __construct(
        ProductStockPacketRepository $repository,
        ProductStockRepository $productStockRepository,
        ProductStockLogRepository $productStockLogRepository,
        OrderItemRepository $orderItemRepository,
        ProductStockPacketService $productStockPacketService,
        ProductStockService $productStockService,
        ProductStockLogService $productStockLogService,
        ProductStockPositionService $productStockPositionService
    ) {
        $this->repository = $repository;
        $this->productStockRepository = $productStockRepository;
        $this->productStockLogRepository = $productStockLogRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productStockPacketService = $productStockPacketService;
        $this->productStockService = $productStockService;
        $this->productStockLogService = $productStockLogService;
        $this->productStockPositionService = $productStockPositionService;
    }

    public function index(int $id): View
    {
        $productStock = $this->productStockRepository->find($id);

        return view('product_stocks.packets.index', compact('productStock'));
    }

    public function edit(int $id, int $packetId): View
    {
        $productStock = $this->productStockRepository->find($id);
        $productStockPacket = $this->repository->find($packetId);

        return view('product_stocks.packets.edit', compact('productStockPacket', 'productStock'));
    }

    public function create(int $id): View
    {
        $productStock = $this->productStockRepository->find($id);

        return view('product_stocks.packets.create', compact('productStock'));
    }

    public function store(ProductStockPacketCreateRequest $request, int $productStockId): RedirectResponse
    {
        $validated = $request->validated();

        $this->productStockPacketService->createProductPacket(
            $validated['packet_quantity'],
            $validated['packet_name'],
            $validated['packet_product_quantity'],
            $productStockId
        );

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success',
        ]);
    }

    public function update(ProductStockPacketUpdateRequest $request, int $productStockId, int $packetId): RedirectResponse
    {
        $validated = $request->validated();

        $this->productStockPacketService->updatePacketQuantity(
            $validated['packet_quantity'],
            $validated['packet_name'],
            $validated['packet_product_quantity'],
            $productStockId,
            $packetId
        );

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success',
        ]);
    }

    public function delete(int $id, int $packetId)
    {
        $this->productStockPacketService->deletePacket($packetId);

        return redirect()->back()->with([
            'message' => __('product_stock_packets.messages.delete'),
            'alert-type' => 'info'
        ]);
    }
}
