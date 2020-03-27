<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Jobs\SendMailToFirmsToUpdateTheDataJob;
use App\Repositories\WarehouseRepository;
use App\Http\Requests\FirmCreateRequest;
use App\Http\Requests\FirmUpdateRequest;
use App\Repositories\FirmAddressRepository;
use App\Repositories\FirmRepository;
use Yajra\DataTables\Facades\DataTables;
use App\Entities\Employee;

/**
 * Class FirmsController.
 *
 * @package namespace App\Http\Controllers;
 */
class FirmsController extends Controller
{
    /**
     * @var FirmRepository
     */
    protected $repository;

    /**
     * @var FirmAddressRepository
     */
    protected $firmAddressRepository;

    /**
     * @var WarehouseRepository
     */
    protected $warehouseRepository;

    /**
     * FirmsController constructor.
     *
     * @param FirmRepository $repository
     */
    public function __construct(
        FirmRepository $repository,
        FirmAddressRepository $firmAddressRepository,
        WarehouseRepository $warehouseRepository
    ) {
        $this->repository = $repository;
        $this->firmAddressRepository = $firmAddressRepository;
        $this->warehouseRepository = $warehouseRepository;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('firms'));
        foreach ($visibilities as $key => $row) {
            $visibilities[$key]->show = json_decode($row->show, true);
            $visibilities[$key]->hidden = json_decode($row->hidden, true);
        }

        return view('firms.index', compact('visibilities'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $warehouses = $this->warehouseRepository->all();
        return view('firms.create', compact('warehouses'));
    }

    /**
     * @param FirmCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FirmCreateRequest $request)
    {
        $firm = $this->repository->create($request->all());

        $this->firmAddressRepository->create([
            'firm_id' => $firm->id,
            'city' => $request->input('city'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'delivery_warehouse' => $request->input('delivery_warehouse'),
            'flat_number' => $request->input('flat_number'),
            'address' => $request->input('address'),
            'address2' => $request->input('address2'),
            'postal_code' => $request->input('postal_code'),
        ]);

        return redirect()->route('firms.edit', ['id' => $firm->id])->with([
            'message' => __('firms.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $firm = $this->repository->find($id);
        $firmAddress = $this->firmAddressRepository->findByField('firm_id', $firm->id);
        $warehouses = $this->warehouseRepository->all();
        $employees = Employee::where('firm_id', $id)->get();
        foreach ($employees as $employee) {
           $roles = $employee->employeeroles;
           $employee->role = '';
           foreach ($roles as $role) {
               $rname = $role->name;
               $employee->role .= ''.$rname.' ';
           }
        }

        $visibilitiesWarehouse = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('warehouses'));
        foreach ($visibilitiesWarehouse as $key => $row) {
            $visibilitiesWarehouse[$key]->show = json_decode($row->show, true);
            $visibilitiesWarehouse[$key]->hidden = json_decode($row->hidden, true);
        }
        $visibilitiesEmployee = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('employees'));
        foreach ($visibilitiesEmployee as $key => $row) {
            $visibilitiesEmployee[$key]->show = json_decode($row->show, true);
            $visibilitiesEmployee[$key]->hidden = json_decode($row->hidden, true);
        }
        return view('firms.edit',
            compact('visibilitiesWarehouse', 'visibilitiesEmployee', 'firm', 'firmAddress', 'warehouses'))->withEmployees($employees);
    }

    /**
     * @param FirmUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(FirmUpdateRequest $request, $id)
    {
        $firm = $this->repository->find($id);

        if (empty($firm)) {
            abort(404);
        }

        $this->repository->update($request->all(), $firm->id);
        $this->firmAddressRepository->update($request->all(), $firm->address->id);

        return redirect()->back()->with([
            'message' => __('firms.message.update'),
            'alert-type' => 'success'
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $firm = $this->repository->find($id);

        if (empty($firm)) {
            abort(404);
        }

        $firm->delete($firm->id);

        return redirect()->route('firms.index')->with([
            'message' => __('firms.message.delete'),
            'alert-type' => 'info'
        ]);
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        $firm = $this->repository->find($id);

        if (empty($firm)) {
            abort(404);
        }
        $dataToStore = [];
        $dataToStore['status'] = $firm['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        $this->repository->update($dataToStore, $firm->id);

        return redirect()->back()->with([
            'message' => __('firms.message.change_status'),
            'alert-type' => 'success'
        ]);
    }

    public function sendRequestToUpdateFirmData($id)
    {
        $firm = $this->repository->find($id);

        if(empty($firm)){
            abort(404);
        }

        if($firm->email !== null) {
            $firm->send_request_to_update_data = true;
            $firm->update();
            dispatch_now(new SendMailToFirmsToUpdateTheDataJob($id, $firm->email));
            return redirect()->back()->with([
                'message' => __('firms.message.send_request_to_update_data'),
                'alert-type' => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                'message' => __('firms.message.send_request_to_update_data_error'),
                'alert-type' => 'error'
            ]);
        }
    }
}
