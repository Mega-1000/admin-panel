<?php

namespace App\Http\Controllers;

use App\Entities\EmployeeRole;
use Illuminate\Http\Request;

class EmployeeRoleController extends Controller
{
    public function index()
    {
        $roles = EmployeeRole::all();
        return view('employee_roles.index',compact('roles'))
        ->withpackageTemplates($roles);
    }
    
     public function create()
    {
        return view('employee_roles.create');
    }

    /**
     * @param EmployeeCreateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $role = new EmployeeRole();
        $role->name = $request->name;
        $role->save();

        return redirect()->route('employee_role.index')->with([
            'message' => __('firms.message.role_store'),
            'alert-type' => 'success'
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $role = EmployeeRole::find($id);

        return view('employee_roles.edit', compact('role'));
    }


    /**
     * @param EmployeeUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $role = EmployeeRole::find($id);

        if(empty($role)){
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
    

}
