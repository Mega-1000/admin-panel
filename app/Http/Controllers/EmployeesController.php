<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Repositories\EmployeeRepository;
use Yajra\DataTables\Facades\DataTables;
use App\Entities\EmployeeRole;
use App\Entities\Warehouse;
use App\Entities\Employee;
use App\Entities\PostalCodeLatLon;

/**
 * Class EmployeesController.
 *
 * @package namespace App\Http\Controllers;
 */
class EmployeesController extends Controller
{
    /**
     * @var EmployeeRepository
     */
    protected $repository;

    /**
     * EmployeesController constructor.
     *
     * @param EmployeeRepository $repository
     */
    public function __construct(EmployeeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($id)
    {
        $roles = EmployeeRole::all();
        $warehouses = Warehouse::where('firm_id', $id)->get();
        return view('firms.employees.create', compact('id'))->withRoles($roles)->withWarehouses($warehouses); 
    }

    /**
     * @param EmployeeCreateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EmployeeCreateRequest $request, $id)
    {
        $postal = PostalCodeLatLon::where('postal_code', $request->input('postal_code'))->first(); 
        $employee = new Employee;
        $employee->firm_id = $id;
        $employee->email = $request->input('email');
        $employee->firstname = $request->input('firstname');
        $employee->lastname = $request->input('lastname');
        $employee->phone= $request->input('phone');
        $employee->comments =  $request->input('comments');
        $employee->additional_comments = $request->input('additional_comments');
        $employee->postal_code = $request->input('postal_code');
        $employee->status = $request->input('status');
        if (!empty($postal)) {
            $employee->latitude = $postal->latitude;
            $employee->longitude = $postal->longitude;
        }
        $employee->person_number = $request->input('person_number');
        $employee->save();
        for ($i = $request->input('rolecount'); $i>0 ; $i--){
            if(!empty($request->input('role'.$i))) {
                $employee->employeeroles()->attach([$request->input('role'.$i)]);
            }
        }
        for ($i = $request->input('magazinecount'); $i>0 ; $i--){
            if(!empty($request->input('magazine'.$i))) {
                $employee->warehouses()->attach([$request->input('magazine'.$i)]);
            }
        }

        return redirect()->route('firms.edit', ['firm_id' => $id])->with([
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
        $employee = Employee::find($id);
        $roles = EmployeeRole::all();
        $firm_id = $employee->firm_id;
        $warehouses = Warehouse::where('firm_id', $firm_id)->get();
        $attachedRoles = $employee->employeeroles;
        $attachedWarehouses = $employee->warehouses;

        return view('firms.employees.edit', compact('employee'))->withRoles($roles)->withWarehouses($warehouses)
            ->withAttachedRoles($attachedRoles)->withAttachedWarehouses($attachedWarehouses);
    }


    /**
     * @param EmployeeUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EmployeeUpdateRequest $request, $id)
    {
        $employee = Employee::find($id);
        $postal = PostalCodeLatLon::where('postal_code', $request->input('postal_code'))->first(); 
         
        if(empty($employee)){
            abort(404);
        }
        $employee->email = $request->input('email');
        $employee->firstname = $request->input('firstname');
        $employee->lastname = $request->input('lastname');
        $employee->phone= $request->input('phone');
        $employee->comments =  $request->input('comments');
        $employee->additional_comments = $request->input('additional_comments');
        $employee->postal_code = $request->input('postal_code');
        $employee->status = $request->input('status');
        if (!empty($postal)) {
            $employee->latitude = $postal->latitude;
            $employee->longitude = $postal->longitude;
        }
        $employee->person_number = $request->input('person_number');
        $employee->save();
        $employee->employeeroles()->detach();
        for ($i = $request->input('rolecount'); $i>0 ; $i--){
            if(!empty($request->input('role'.$i))) {
                $employee->employeeroles()->attach([$request->input('role'.$i)]);
            }
        }
        $employee->warehouses()->detach();
        for ($i = $request->input('magazinecount'); $i>0 ; $i--){
            if(!empty($request->input('warehouse'.$i))) {
                $employee->warehouses()->attach([$request->input('warehouse'.$i)]);
            }
        }
        $firm_id = $employee->firm_id;
        return redirect()->route('firms.edit', ['firm_id' => $firm_id]);
    }


    public function destroy($id)
    {
        $this->repository->delete($id);

        return redirect()->back()->with([
            'message' => __('employees.message.delete'),
            'alert-type' => 'info'
        ])->withInput(['tab' => 'employees']);
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
        $collection = $this->repository->findByField('firm_id', $id);

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
            'message' => __('employees.message.change_status'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'employees']);
    }
}
