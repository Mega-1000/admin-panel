<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Repositories\LabelRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\ShipmentGroupRepository;
use App\Repositories\TagRepository;
use App\ShipmentGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

// TODO Upoerząkować temat z PDFami

class ShipmentGroupController extends Controller
{

    /**
     * @var ShipmentGroupRepository
     */
    protected $repository;

    /**
     * @var OrderPackageRepository
     */
    protected $orderPackageRepository;

    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /** @var LabelRepository */
    protected $labelRepository;

    /**
     * @param ShipmentGroupRepository $repository
     * @param TagRepository $tagRepository
     * @param LabelRepository $labelRepository
     */
    public function __construct(
        ShipmentGroupRepository $repository,
        TagRepository           $tagRepository,
        LabelRepository         $labelRepository,
        OrderPackageRepository  $orderPackageRepository
    )
    {
        $this->repository = $repository;
        $this->tagRepository = $tagRepository;
        $this->labelRepository = $labelRepository;
        $this->orderPackageRepository = $orderPackageRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('shipment_groups'));
        foreach ($visibilities as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }

        return view('shipment_groups.index', compact('visibilities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->repository->create([
            'name' => $request->input('name'),
            'color' => '#' . $request->input('color'),
            'status' => $request->input('status'),
            'message' => $request->input('message')
        ]);

        return redirect()->route('shipment_groups.edit');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|Application|Response|View
     */
    public function create()
    {
        return view('shipment_groups.create');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View
     */
    public function show(Request $request, int $id)
    {
        $shipmentGroup = $this->repository->find($id);

        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('order_packages'));
        foreach ($visibilities as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        return view('shipment_groups.show', compact('shipmentGroup', 'visibilities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ShipmentGroup $shipmentGroup
     *
     * @return void
     */
    public function edit(ShipmentGroup $shipmentGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ShipmentGroup $shipmentGroup
     *
     * @return void
     */
    public function update(Request $request, ShipmentGroup $shipmentGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ShipmentGroup $shipmentGroup
     *
     * @return void
     */
    public function destroy(ShipmentGroup $shipmentGroup)
    {
        //
    }

    /**
     * @return JsonResponse
     */
    public function datatable()
    {
        $collection = $this->prepareCollection();

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection()
    {
        $collection = $this->repository->all();

        return $collection;
    }


    /**
     * @return JsonResponse
     */
    public function packageDatatable(Request $request, int $id)
    {
        $collection = $this->orderPackageRepository->findWhere(
            [
                'shipment_group_id' => $id,
                ['order_packages.status', '!=', 'CANCELLED'],
                ['order_packages.status', '!=', 'WAITING_FOR_CANCELLED'],
                ['order_packages.status', '!=', 'REJECT_CANCELLED'],
                ['order_packages.letter_number', '!=', null]
            ]
        );

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $packageId
     *
     * @return RedirectResponse
     */
    public function removePackage(Request $request, int $id, int $packageId)
    {
        $orderPackage = $this->orderPackageRepository->find($packageId);
        $orderPackage->shipment_group_id = null;
        $orderPackage->save();
        return redirect()->route('shipment-groups.show',
            [
                'id' => $id,
            ]
        )->with(
            [
                'message' => 'Paczka została usunięta z grupy przesyłek',
                'alert-type' => 'info'
            ]
        );
    }

    public function print(int $id)
    {
        $shipmentGroup = $this->repository->find($id);
        $collection = $this->orderPackageRepository->findWhere(
            [
                'shipment_group_id' => $id,
                ['order_packages.status', '!=', 'CANCELLED'],
                ['order_packages.status', '!=', 'WAITING_FOR_CANCELLED'],
                ['order_packages.status', '!=', 'REJECT_CANCELLED'],
                ['order_packages.letter_number', '!=', null]
            ]
        );
        try {
            $pdfFilename = 'group-close-protocol-' . $shipmentGroup->getLabel() . '-' . Carbon::today()->toDateString() . '.pdf';

            $pdf = Pdf::loadView('pdf.close-group-protocol', [
                'packages' => $collection,
                'date' => Carbon::today(),
                'shipmentGroup' => $shipmentGroup,
                'groupName' => strtoupper($shipmentGroup->getLabel()),
                'mode' => 'utf-8'
            ])->setPaper('a4', 'landscape');
            if (!file_exists(storage_path('app/public/protocols'))) {
                mkdir(storage_path('app/public/protocols'));
            }
            $path = storage_path('app/public/protocols/' . $pdfFilename);
            $pdf->save($path);
        } catch (Exception $e) {
            return redirect()->back()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
        return $pdf->download($pdfFilename);
    }
}
