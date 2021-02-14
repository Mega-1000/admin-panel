<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductStockPacketCreateRequest;
use App\Http\Requests\ProductStockPacketUpdateRequest;
use App\Repositories\OrderItemRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockLogRepository;
use App\Repositories\ProductStockPacketItemRepository;
use App\Repositories\ProductStockPacketRepository;
use App\Repositories\ProductStockRepository;
use App\Services\ProductStockLogService;
use App\Services\ProductStockPacketService;
use App\Services\ProductStockPositionService;
use App\Services\ProductStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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

    protected $productRepository;

    protected $productStockPacketItemRepository;

    public function __construct(
        ProductStockPacketRepository $repository,
        ProductStockRepository $productStockRepository,
        ProductStockLogRepository $productStockLogRepository,
        OrderItemRepository $orderItemRepository,
        ProductStockPacketService $productStockPacketService,
        ProductStockService $productStockService,
        ProductStockLogService $productStockLogService,
        ProductStockPositionService $productStockPositionService,
        ProductRepository $productRepository,
        ProductStockPacketItemRepository $productStockPacketItemRepository
    ) {
        $this->repository = $repository;
        $this->productStockRepository = $productStockRepository;
        $this->productStockLogRepository = $productStockLogRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productStockPacketService = $productStockPacketService;
        $this->productStockService = $productStockService;
        $this->productStockLogService = $productStockLogService;
        $this->productStockPositionService = $productStockPositionService;
        $this->productRepository = $productRepository;
        $this->productStockPacketItemRepository = $productStockPacketItemRepository;
    }

    public function index(): View
    {
        $productStockPackets = $this->repository->all();

        return view('product_stocks.packets.index', compact('productStockPackets'));
    }

    public function edit(int $packetId): View
    {
        $productStockPacket = $this->repository->find($packetId);
        $products = $this->productRepository->findWhere(['deleted_at' => null]);

        return view('product_stocks.packets.edit', compact('productStockPacket', 'products'));
    }

    public function create(): View
    {
        $products = $this->productRepository->findWhere(['deleted_at' => null]);
        return view('product_stocks.packets.create', compact('products'));
    }

    public function store(ProductStockPacketCreateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->productStockPacketService->createProductPacket(
            $validated['packetsQuantity'],
            $validated['packetName'],
            $validated['products']
        );

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success',
        ]);
    }

    public function update(ProductStockPacketUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->productStockPacketService->updatePacketQuantity(
            $validated['packetName'],
            $validated['packetsQuantity'],
            $validated['products'],
            $validated['id']
        );

        return response()->json(['status' => true, 'message' => __('product_stock_packets.messages.update')]);
    }

    public function delete(int $id, int $packetId): RedirectResponse
    {
        $this->productStockPacketService->deletePacket($packetId);

        return redirect()->back()->with([
            'id' => $id,
            'message' => __('product_stock_packets.messages.delete'),
            'alert-type' => 'info'
        ]);
    }
}
