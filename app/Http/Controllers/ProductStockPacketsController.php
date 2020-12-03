<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductStockPacketAssignRequest;
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
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

        $packetQuantity = $this->productStockPacketService->getProductsQuantityInCreatedPackets(
            $validated['packet_quantity'], 
            $validated['packet_product_quantity']
        );

        $productStock = $this->productStockRepository->find($productStockId);

        $this->productStockPacketService->createProductPacket(
            $validated['packet_quantity'],
            $validated['packet_name'],
            $validated['packet_product_quantity'],
            $productStockId
        );

        $productStockFirstPosition = $productStock->position->first();

        $this->productStockPositionService->updateProductPositionQuantity(
            $productStockFirstPosition->position_quantity, 
            $packetQuantity, $productStockFirstPosition->id, 
            0
        );

        $this->productStockLogService->storeProductQuantityChangeLog(
            $productStock->id, 
            $productStockFirstPosition->id, 
            $packetQuantity, 
            'DELETE', 
            Auth::user()->id
        );

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success',
        ]);
    }

    public function update(ProductStockPacketUpdateRequest $request, int $productStockId, int $packetId): RedirectResponse
    {
        $validated = $request->validated();

        $productStockPacket = $this->productStockPacketService->findPacket($packetId);

        $currentPacketQuantityDifference = $this->productStockPacketService->getPacketQuantityDifferenceAfterUpdate(
            $productStockPacket->packet_quantity,
            $productStockPacket->packet_product_quantity,
            $validated['packet_quantity'],
            $validated['packet_product_quantity']
        );

        $this->productStockPacketService->updatePacketQuantity(
            $validated['packet_quantity'],
            $validated['packet_name'],
            $validated['packet_product_quantity'],
            $productStockId,
            $packetId
        );

        $productStock = $this->productStockService->findProductStock($productStockId);

        $productStockFirstPosition = $productStock->position->first();
        $currentPacketQuantity = $this->productStockPacketService->getProductsQuantityInCreatedPackets(
            $validated['packet_quantity'], 
            $validated['packet_product_quantity']
        );

        $this->productStockService->updateProductStockQuantity(
            $productStockFirstPosition->position_quantity, 
            $currentPacketQuantity, 
            $productStockFirstPosition->id
        );

        $this->productStockPositionService->updateProductPositionQuantity($productStockFirstPosition->position_quantity, $currentPacketQuantityDifference, $productStockFirstPosition->id, 1);

        $this->productStockService->updateProductStockQuantity($productStock->quantity, $currentPacketQuantityDifference, $productStock->id);

        $action = ($currentPacketQuantityDifference < 0) ? 'DELETE' : 'ADD';

        $this->productStockLogService->storeProductQuantityChangeLog($productStock->id, $productStockFirstPosition->id, $currentPacketQuantity, $action, Auth::user()->id);

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success',
        ]);
    }

    public function assign(ProductStockPacketAssignRequest $request, int $orderItemId): RedirectResponse
    {
        $validated = $request->validated();

        $this->productStockPacketService->reducePacketQuantityAfterAssignToOrderItem($validated['packet']);

        $this->productStockPacketService->assignPacketToOrderItem($orderItemId, $validated['packet']);

        return redirect()->back()->with([
            'message' => __('product_stock_packets.packet_assign'),
            'alert-type' => 'success',
        ]);
    }

    public function retain(int $orderItemId): RedirectResponse
    {
        $this->productStockPacketService->unassignPacketFromOrderItem($orderItemId);

        return redirect()->back()->with([
            'message' => __('product_stock_packets.packet_assign'),
            'alert-type' => 'success',
        ]);
    }
}
