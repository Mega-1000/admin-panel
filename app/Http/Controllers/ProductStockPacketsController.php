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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class ProductStocksController
 * @package App\Http\Controllers
 */
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

    /**
     * ProductStocksController constructor.
     * @param ProductStockPacketRepository $repository
     * @param ProductStockRepository $productStockRepository
     * @param ProductStockLogRepository $productStockLogRepository
     * @param OrderItemRepository $orderItemRepository
     * @param ProductStockPacketService $productStockPacketService
     * @param ProductStockService $productStockService
     * @param ProductStockLogService $productStockLogService
     * @param ProductStockPositionService $productStockPositionService
     */
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


        $packetQuantity = $request->input('packet_quantity') * $request->input('packet_product_quantity');

        $productStock = $this->productStockRepository->find($productStockId);

        $this->productStockPacketService->create(
            $validated['packet_quantity'],
            $validated['packet_name'],
            $validated['packet_product_quantity'],
            $productStockId
        );


        $productStockFirstPosition = $productStock->position->first();

        $this->productStockPositionService->update($productStockFirstPosition->position_quantity, $packetQuantity, $productStockFirstPosition->id, 0);

        $this->productStockLogService->create($productStock->id, $productStockFirstPosition->id, $packetQuantity, 'DELETE');

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success',
        ]);
    }

    public function update(ProductStockPacketUpdateRequest $request, int $productStockId, int $packetId): RedirectResponse
    {
        $validated = $request->validated();

        $productStockPacket = $this->repository->find($packetId);

        $previousPacketQuantity = $productStockPacket->packet_quantity * $productStockPacket->packet_product_quantity;
        $packetQuantity = $validated['packet_quantity'] * $validated['packet_product_quantity'];

        $currentPacketQuantityDifference = $previousPacketQuantity - $packetQuantity;

        $this->productStockPacketService->update(
            $validated['packet_quantity'],
            $validated['packet_name'],
            $validated['packet_product_quantity'],
            $productStockId,
            $packetId
        );


        $productStock = $this->productStockRepository->find($productStockId);

        $productStockFirstPosition = $productStock->position->first();

        $this->productStockService->update($productStockFirstPosition->position_quantity, $packetQuantity, $productStockFirstPosition->id);

        $this->productStockPositionService->update($productStockFirstPosition->position_quantity, $currentPacketQuantityDifference, $productStockFirstPosition->id, 1);

        $this->productStockService->update($productStock->quantity, $currentPacketQuantityDifference, $productStock->id);

        $action = ($currentPacketQuantityDifference < 0) ? 'DELETE' : 'ADD';

        $this->productStockLogService->create($productStock->id, $productStockFirstPosition->id, $packetQuantity, $action);

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success',
        ]);
    }

    public function assign(ProductStockPacketAssignRequest $request, int $orderItemId): RedirectResponse
    {
        $validated = $request->validated();

        $productStockPacket = $this->repository->find($validated['packet']);

        $productStockPacket->update([
            'packet_quantity' => $productStockPacket->packet_quantity - 1,
        ]);

        $this->orderItemRepository->find($orderItemId)->update([
            'product_stock_packet_id' => $validated['packet']
        ]);

        return redirect()->back()->with([
            'message' => __('product_stock_packets.packet_assign'),
            'alert-type' => 'success',
        ]);
    }

    public function retain(int $orderItemId): RedirectResponse
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);
        $productStockPacket = $this->repository->find($orderItem->product_stock_packet_id);

        $productStockPacket->update([
            'packet_quantity' => $productStockPacket->packet_quantity + 1
        ]);

        $orderItem->update([
            'product_stock_packet_id' => null
        ]);

        return redirect()->back()->with([
            'message' => __('product_stock_packets.packet_assign'),
            'alert-type' => 'success',
        ]);
    }
}
