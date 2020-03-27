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
        $role->symbol = $request->symbol;
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

        return view('employee_roles.create', compact('role'));
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

        $role->name = $request->name;
        $role->symbol = $request->name;
        $role->save();

        return redirect()->route('employee_role.index')->with([
            'message' => __('firms.message.role_update'),
            'alert-type' => 'success'
        ]);
    }


    public function destroy($id)
    {
        $role = EmployeeRole::find($id); 
        $role->delete();

        return redirect()->route('employee_role.index');
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable($id)
    {
        $collection = $this->prepareCollection($id);

        return DataTables::collection($collection)->make(true);
    }
    
}   
