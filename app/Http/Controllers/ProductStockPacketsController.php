<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\ProductStock;
use App\Entities\ProductStockLog;
use App\Http\Requests\ProductStockPacketCreateRequest;
use App\Http\Requests\ProductStockPacketUpdateRequest;
use App\Http\Requests\ProductStockUpdateRequest;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockLogRepository;
use App\Repositories\ProductStockPacketRepository;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * ProductStocksController constructor.
     * @param ProductStockPacketRepository $repository
     * @param ProductStockRepository $productStockRepository
     */
    public function __construct(
        ProductStockPacketRepository $repository,
        ProductStockRepository $productStockRepository
    ) {
        $this->repository = $repository;
        $this->productStockRepository = $productStockRepository;
    }

    public function index(int $id)
    {
        $productStock = $this->productStockRepository->find($id);

        return view('product_stocks.packets.index', compact('productStock'));
    }

    public function edit(int $id, int $packetId): \Illuminate\View\View
    {
        $productStock = $this->productStockRepository->find($id);
        $productStockPacket = $this->repository->find($packetId);

        return view('product_stocks.packets.edit', compact('productStockPacket', 'productStock'));
    }

    public function create(int $id): \Illuminate\View\View
    {
        $productStock = $this->productStockRepository->find($id);

        return view('product_stocks.packets.create', compact('productStock'));
    }

    public function store(ProductStockPacketCreateRequest $request, int $productStockId): RedirectResponse
    {
        $validated = $request->validated();

        $packetQuantity = $request->input('packet_quantity') * $request->input('packet_product_quantity');

        $productStock = $this->productStockRepository->find($productStockId);

        $this->repository->create([
            'packet_quantity' => $request->input('packet_quantity'),
            'packet_name' => $request->input('packet_name'),
            'packet_product_quantity' => $request->input('packet_product_quantity'),
            'product_stock_id' => $productStockId
        ]);

        $productStock->update([
           'quantity' => $productStock->quantity
        ]);

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success'
        ]);
    }

    public function update(ProductStockPacketUpdateRequest $request, int $productStockId, int $packetId): RedirectResponse
    {
        $validated = $request->validated();

        $this->repository->update([
            'packet_quantity' => $request->input('packet_quantity'),
            'packet_name' => $request->input('packet_name'),
            'packet_product_quantity' => $request->input('packet_product_quantity'),
            'product_stock_id' => $productStockId
        ], $packetId);

        return redirect()->back()->with([
            'message' => __('product_stocks.message.packet_store'),
            'alert-type' => 'success'
        ]);
    }
}
