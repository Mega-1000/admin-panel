<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseCreateRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Repositories\WarehouseAddressRepository;
use App\Repositories\WarehousePropertyRepository;
use App\Repositories\WarehouseRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Class WarehousesController.
 *
 * @package namespace App\Http\Controllers;
 */
class WarehousesController extends Controller
{
    /**
     * @var WarehouseRepository
     */
    protected $repository;

    /**
     * @var WarehouseAddressRepository
     */
    protected $warehouseAddressRepository;

    /**
     * @var WarehousePropertyRepository
     */
    protected $warehousePropertyRepository;

    /**
     * WarehousesController constructor.
     * @param WarehouseRepository $repository
     * @param WarehouseAddressRepository $warehouseAddressRepository
     * @param WarehousePropertyRepository $warehousePropertyRepository
     */
    public function __construct(WarehouseRepository $repository, WarehouseAddressRepository $warehouseAddressRepository, WarehousePropertyRepository $warehousePropertyRepository)
    {
        $this->repository = $repository;
        $this->warehouseAddressRepository = $warehouseAddressRepository;
        $this->warehousePropertyRepository = $warehousePropertyRepository;
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($id)
    {
        return view('firms.warehouses.create', compact('id'));
    }

    /**
     * @param WarehouseCreateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(WarehouseCreateRequest $request, $id)
    {
        $openDays = $this->generateOpenDays($request);
        $warehouse = $this->repository->create([
            'firm_id' => $id,
            'symbol' => $request->input('symbol'),
            'status' => $request->input('status'),
            'radius' => $request->input('radius'),
        ]);

        $this->warehouseAddressRepository->create([
            'warehouse_id' => $warehouse->id,
            'address' => $request->input('address'),
            'warehouse_number' => $request->input('warehouse_number'),
            'postal_code' => $request->input('postal_code'),
            'city' => $request->input('city'),
        ]);

        $this->warehousePropertyRepository->create([
            'warehouse_id' => $warehouse->id,
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'phone' => $request->input('phone'),
            'comments' => $request->input('comments'),
            'additional_comments' => $request->input('additional_comments'),
            'open_days' => json_encode($openDays),
            'email' => $request->input('email'),
        ]);

        return redirect()->route('firms.edit', ['firm_id' => $id])->with([
            'message' => __('warehouses.message.store'),
            'alert-type' => 'success'
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $warehouse = $this->repository->find($id);
        $warehouseAddress = $warehouse->address;
        $warehouseProperty = $warehouse->property;
        $openDays = json_decode($warehouseProperty->open_days, true);

        return view('firms.warehouses.edit', compact('warehouse', 'warehouseAddress', 'warehouseProperty', 'openDays'));
    }


    /**
     * @param WarehouseUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(WarehouseUpdateRequest $request, $id)
    {
        $warehouse = $this->repository->find($id);
        $openDays = $this->generateOpenDays($request);

        if (empty($warehouse)) {
            abort(404);
        }

        $this->repository->update($request->all(), $warehouse->id);
        $this->warehouseAddressRepository->update($request->all(), $warehouse->address->id);
        $this->warehousePropertyRepository->update([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'phone' => $request->input('phone'),
            'comments' => $request->input('comments'),
            'additional_comments' => $request->input('additional_comments'),
            'open_days' => json_encode($openDays),
            'email' => $request->input('email')
        ], $warehouse->property->id);

        return redirect()->back()->with([
            'message' => __('warehouses.message.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return redirect()->back()->with([
            'message' => __('warehouses.message.delete'),
            'alert-type' => 'info'
        ])->withInput(['tab' => 'warehouses']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable($id)
    {
        $collection = $this->prepareCollection($id);

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection($id)
    {
        $collection = $this->repository->with('address')->findByField('firm_id', $id);

        return $collection;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        $employee = $this->repository->find($id);

        if (empty($employee)) {
            abort(404);
        }
        $dataToStore = [];
        $dataToStore['status'] = $employee['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        $this->repository->update($dataToStore, $employee->id);

        return redirect()->back()->with([
            'message' => __('warehouses.message.change_status'),
            'alert-type' => 'success',
        ])->withInput(['tab' => 'warehouses']);
    }

    public function generateOpenDays($request)
    {
        $openDays = [
            'monday' => [
                'from' => $request->open_days_monday_from,
                'to' => $request->open_days_monday_to
            ],
            'tuesday' => [
                'from' => $request->open_days_tuesday_from,
                'to' => $request->open_days_tuesday_to
            ],
            'wednesday' => [
                'from' => $request->open_days_wednesday_from,
                'to' => $request->open_days_wednesday_to
            ],
            'thursday' => [
                'from' => $request->open_days_thursday_from,
                'to' => $request->open_days_thursday_to
            ],
            'friday' => [
                'from' => $request->open_days_friday_from,
                'to' => $request->open_days_friday_to
            ],
            'saturday' => [
                'from' => $request->open_days_saturday_from,
                'to' => $request->open_days_saturday_to
            ],
            'sunday' => [
                'from' => $request->open_days_sunday_from,
                'to' => $request->open_days_sunday_to
            ]
        ];
        return $openDays;
    }


    public function editBySymbol($symbol)
    {
        $warehouse = $this->repository->findByField('symbol', $symbol);

        if (empty($warehouse)) {
            abort(404);
        }

        return redirect()->route('warehouses.edit', ['id' => $warehouse->first->id->id]);
    }
}
