<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Repositories\EmployeeRepository;
use Yajra\DataTables\Facades\DataTables;

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
        return view('firms.employees.create', compact('id'));
    }

    /**
     * @param EmployeeCreateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EmployeeCreateRequest $request, $id)
    {
        $this->repository->create([
            'firm_id' => $id,
            'warehouse_id' => null,
            'email' => $request->input('email'),
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'phone' => $request->input('phone'),
            'job_position' => $request->input('job_position'),
            'comments' => $request->input('comments'),
            'additional_comments' => $request->input('additional_comments'),
            'postal_code' => $request->input('postal_code'),
            'status' => $request->input('status')
        ]);

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
        $employee = $this->repository->find($id);

        return view('firms.employees.edit', compact('employee'));
    }


    /**
     * @param EmployeeUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EmployeeUpdateRequest $request, $id)
    {
        $employee = $this->repository->find($id);

        if(empty($employee)){
            abort(404);
        }

        $this->repository->update($request->all(), $id);

        return redirect()->back()->with([
            'message' => __('employees.message.update'),
            'alert-type' => 'success'
        ]);
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
