<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Employee;
use App\Entities\Firm;
use App\Entities\FirmSource;
use App\Entities\OrderSource;
use App\Http\Requests\FirmCreateRequest;
use App\Http\Requests\FirmUpdateRequest;
use App\Jobs\SendMailToFirmsToUpdateTheDataJob;
use App\Repositories\FirmAddressRepository;
use App\Repositories\FirmRepository;
use App\Repositories\WarehouseRepository;
use App\Services\FirmService;
use Exception;
use FontLib\TrueType\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class FirmsController.
 *
 * @package namespace App\Http\Controllers;
 */
class FirmsController extends Controller
{
    /**
     * FirmsController constructor.
     *
     * @param FirmRepository $repository
     * @param FirmAddressRepository $firmAddressRepository
     * @param WarehouseRepository $warehouseRepository
     */
    public function __construct(
        protected FirmRepository        $repository,
        protected FirmAddressRepository $firmAddressRepository,
        protected WarehouseRepository   $warehouseRepository
    ) {}


    /**
     * @return Factory|View
     */
    public function index(): Factory|View
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('firms'));
        foreach ($visibilities as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }

        return view('firms.index', compact('visibilities'));
    }

    /**
     * @param FirmCreateRequest $request
     * @return RedirectResponse
     */
    public function store(FirmCreateRequest $request): RedirectResponse
    {
        $firm = new Firm;
        $firm->name = $request->name;
        $firm->short_name = $request->short_name;
        $firm->symbol = $request->symbol;
        $firm->delivery_warehouse = $request->delivery_warehouse;
        $firm->email = $request->email;
        $firm->secondary_email = $request->secondary_email;
        $firm->complaint_email = $request->complaint_email;
        $firm->nip = $request->nip;
        $firm->account_number = $request->account_number;
        $firm->status = $request->status;
        $firm->phone = $request->phone;
        $firm->notices = $request->notices;
        $firm->secondary_phone = $request->secondary_phone;
        $firm->secondary_notices = $request->secondary_notices;

        if (empty($request->short_name)) {
            $firm->short_name = substr($request->name, 0, 50);
        }
        $firm->save();

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

        if ($request->get('firm_source')) {
            foreach ($request->firm_source as $source_id) {
                FirmSource::firstOrNew(
                    ['firm_id' => $firm->id, 'order_source_id' => $source_id],
                    ['firm_id' => $firm->id, 'order_source_id' => $source_id]
                );
            }
        }

        return redirect()->route('firms.edit', ['firm' => $firm->id])->with([
            'message' => __('firms.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @return Factory|View
     */
    public function create(): Factory|View
    {
        $warehouses = $this->warehouseRepository->all();

        return view('firms.create', compact('warehouses'));
    }

    /**
     * @param $id
     * @return Factory|View
     */
    public function edit($id): Factory|View
    {
        $firm = $this->repository->find($id);

        $firmAddress = $this->firmAddressRepository->findByField('firm_id', $firm->id);
        $warehouses = $this->warehouseRepository->all();
        $employees = Employee::where('firm_id', $id)->get();

        $orderSources = OrderSource::notInUse($firm->id)->get();

        foreach ($employees as $employee) {
            $roles = $employee->employeeRoles;
            $employee->role = '';
            foreach ($roles as $role) {
                $rname = $role->name;
                $employee->role .= '' . $rname . ' ';
            }
        }

        $visibilitiesWarehouse = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('warehouses'));
        foreach ($visibilitiesWarehouse as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        $visibilitiesEmployee = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('employees'));
        foreach ($visibilitiesEmployee as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        return view('firms.edit',
            compact('visibilitiesWarehouse', 'visibilitiesEmployee', 'firm', 'firmAddress', 'warehouses', 'orderSources'))
            ->withEmployees($employees);
    }

    /**
     * @param $id
     * @return RedirectResponse
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
     * @return JsonResponse
     * @throws Exception
     */
    public function datatable(): JsonResponse
    {
        $collection = $this->prepareCollection();

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection(): Collection
    {
        return $this->repository->all();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function changeStatus($id): RedirectResponse
    {
        $firm = Firm::findOrFail($id);

        $dataToStore = [];
        $dataToStore['status'] = $firm['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        $this->repository->update($dataToStore, $firm->id);

        return redirect()->back()->with([
            'message' => __('firms.message.change_status'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param FirmUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(FirmUpdateRequest $request, $id): RedirectResponse
    {
        $firm = Firm::findOrFail($id);

        $firm->name = $request->name;
        $firm->short_name = $request->short_name;
        $firm->symbol = $request->symbol;
        $firm->delivery_warehouse = $request->delivery_warehouse;
        $firm->email = $request->email;
        $firm->secondary_email = $request->secondary_email;
        $firm->complaint_email = $request->complaint_email;
        $firm->nip = $request->nip;
        $firm->account_number = $request->account_number;
        $firm->status = $request->status;
        $firm->phone = $request->phone;
        $firm->notices = $request->notices;
        $firm->secondary_phone = $request->secondary_phone;
        $firm->secondary_notices = $request->secondary_notices;

        if (empty($request->short_name)) {
            $firm->short_name = substr($request->name, 0, 50);
        }

        $firm->save();

        $firmService = new FirmService($firm);
        $firmService->addNewEmployeeForComplaint();

        $this->firmAddressRepository->update($request->all(), $firm->address->id);

        if (!$request->has('firm_source') || !$request->get('firm_source')) {
            $firm->firmSources()->delete();
        }

        if ($request->get('firm_source')) {
            $firm->firmSources()->where('firm_id', $firm->id)->whereNotIn('order_source_id', $request->firm_source)->delete();

            foreach ($request->firm_source as $source_id) {
                $firmSource = FirmSource::withTrashed()->firstOrNew(
                    ['firm_id' => $firm->id, 'order_source_id' => $source_id],
                    ['firm_id' => $firm->id, 'order_source_id' => $source_id]
                );
                if ($firmSource->trashed()) {
                    $firmSource->restore();
                } else {
                    $firmSource->save();
                }
            }
        }

        return redirect()->back()->with([
            'message' => __('firms.message.update'),
            'alert-type' => 'success'
        ]);
    }

    public function sendRequestToUpdateFirmData($id): RedirectResponse
    {
        $firm = Firm::findOrFail($id);

        if ($firm->email !== null) {
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
