<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Repositories\LabelRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\ShipmentGroupRepository;
use App\Repositories\StatusRepository;
use App\Repositories\TagRepository;
use App\ShipmentGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
     * @param TagRepository           $tagRepository
     * @param LabelRepository         $labelRepository
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('shipment_groups'));
        foreach ($visibilities as $key => $row) {
            $visibilities[$key]->show = json_decode($row->show, true);
            $visibilities[$key]->hidden = json_decode($row->hidden, true);
        }

        return view('shipment_groups.index', compact('visibilities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('shipment_groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shipmentGroup = $this->repository->create([
            'name' => $request->input('name'),
            'color' => '#' . $request->input('color'),
            'status' => $request->input('status'),
            'message' => $request->input('message')
        ]);

        return redirect()->route('shipment_groups.edit');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ShipmentGroup $shipmentGroup
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request, int $id)
    {
        $shipmentGroup = $this->repository->find($id);

        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('order_packages'));
        foreach ($visibilities as $key => $row) {
            $visibilities[$key]->show = json_decode($row->show, true);
            $visibilities[$key]->hidden = json_decode($row->hidden, true);
        }
        return view('shipment_groups.show', compact('shipmentGroup', 'visibilities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ShipmentGroup $shipmentGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ShipmentGroup $shipmentGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ShipmentGroup       $shipmentGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShipmentGroup $shipmentGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ShipmentGroup $shipmentGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShipmentGroup $shipmentGroup)
    {
        //
    }

    /**
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function packageDatatable(Request $request, int $id)
    {
        $collection = $this->orderPackageRepository->findWhere(['shipment_group_id' => $id]);

        return DataTables::collection($collection)->make(true);
    }

    public function removePackage(Request $request, int $id, int $packageId)
    {
        $this->orderPackageRepository->find($packageId)->update(
            [
                'shipment_group_id' => null
            ]
        );
    }
}
