<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Module;
use App\Http\Requests\ModuleCreateRequest;
use App\Http\Requests\ModuleUpdateRequest;
use App\Http\Requests\VisibilitiesCreateRequest;
use App\Http\Requests\VisibilitiesUpdateRequest;
use Illuminate\Support\Arr;
use Yajra\DataTables\Facades\DataTables;

class ColumnVisibilitiesController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * widok perwszej tabeli w zakładce odpowiadającej za obsługę wykluczonych kolumn w danym widoku
     * widok ten zawiera listę zakładek dla których będą wykluczane poszczególne pola
     *
     */
    public $isNumberColumns = false;
    public $langArray = [
        1 => 'orders',
        2 => 'customers',
        3 => 'order_payments',
        4 => 'order_packages',
        5 => 'order_tasks',
        6 => 'statuses',
        7 => 'product_stocks',
        8 => 'product_stock_logs',
        9 => 'product_stock_positions',
        10 => 'firms',
        11 => 'employees',
        12 => 'warehouses',
        13 => 'labels',
        14 => 'label_groups',
        15 => 'users',
        16 => 'products'
    ];
    public function moduleIndex()
    {

        return view('columns_visibilities.modules.index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function moduleDatatable()
    {
        return DataTables::collection(Module::get())->make(true);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function moduleCreate()
    {
        return view('columns_visibilities.modules.create');

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function moduleEdit($id)
    {
        $module = Module::find($id);
        return view('columns_visibilities.modules.edit', compact('module'));

    }

    /**
     * @param ModuleUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moduleUpdate(ModuleUpdateRequest $request, $id)
    {
        Module::find($id)->update($request->all());

        return redirect()->back()->with([
            'message' => __('column_visibilities.modules.update'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param ModuleCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moduleStore(ModuleCreateRequest $request)
    {
        $module = Module::create($request->all());

        return redirect()->route('columnVisibilities.modules.edit', ['id' => $module->id])->with([
            'message' => __('column_visibilities.modules.store'),
            'alert-type' => 'success',
        ]);
    }
    public function moduleDestroy($id)
    {
        Module::find($id)->delete();
        return redirect()->back()->with([
            'message' => 'moduł został usunięty',
            'alert-type' => 'success',
        ]);

    }
    /**
     * @param $module_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rolesIndex($module_id)
    {
        $moduleName = Module::select('name')->where('id', $module_id)->first()->name;

        return view('columns_visibilities.roles.index', compact('module_id', 'moduleName'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function rolesDatatable()
    {

        return DataTables::collection(\DB::table('roles')->get())->make(true);

    }

    /**
     * @param $module_id
     * @param $role_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visibilitiesIndex($module_id, $role_id)
    {
        $moduleName = Module::select('name')->where('id', $module_id)->first()->name;
        $roleName = \DB::table('roles')->select('display_name')->where('id', $role_id)->first()->display_name;

        return view('columns_visibilities.visibilities.index', compact('roleName', 'moduleName', 'role_id', 'module_id'));
    }

    /**
     * @param $module_id
     * @param $role_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function visibilitiesDatatable($module_id, $role_id)
    {

        return DataTables::collection(ColumnVisibility::where([['module_id', $module_id], ['role_id', $role_id]])->get())->make(true);

    }

    /**
     * @param $module_id
     * @return array
     */
    public function getColumns($module_id)
    {
        $className = Module::select('model_path')->where('id', $module_id)->first()->model_path ?? 'App\Entities\\' . studly_case(str_singular($tableName));
        if ($className !== "none") {
            $model = new $className;
            if (isset($model->numberColumnVisibilities)) {
                $this->isNumberColumns = true;
                return $model->numberColumnVisibilities;
            }
        }
        $tableName = Module::select('table_name')->where('id', $module_id)->first()->table_name;

        $columns = Arr::flatten(json_decode(
            \DB::table('INFORMATION_SCHEMA.COLUMNS')->select('COLUMN_NAME')->where('TABLE_NAME', $tableName)->get(),
            true));
        if ($className !== "none") {
            if (isset($model->customColumnsVisibilities)) {
                $columns = array_merge($columns, array_diff($model->customColumnsVisibilities, $columns));
            }
        }
        return $columns;
    }

    /**
     * @param $id
     * @param $module_id
     * @param $role_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visibilitiesEdit($module_id, $role_id, $id)
    {
        $visibilities = ColumnVisibility::find($id);
        $columnName = [];
        foreach (json_decode($visibilities->show, true) as $row) {
            $columnName[$row] = 'on';
        }

        $visibilities->columnName = $columnName;

        $columns = $this->getColumns($module_id);
        $moduleName = Module::select('name')->where('id', $module_id)->first()->name;
        $roleName = \DB::table('roles')->select('display_name')->where('id', $role_id)->first()->display_name;
        $lang = $this->langArray[$module_id];
        $isNumberColumns = $this->isNumberColumns;
        return view('columns_visibilities.visibilities.edit', compact('isNumberColumns', 'lang', 'columns', 'roleName', 'moduleName', 'visibilities', 'role_id', 'module_id'));
    }

    public function visibilitiesUpdate(VisibilitiesUpdateRequest $request, $module_id, $role_id, $id)
    {

        $data = $request->all();
        $data['name'] = str_replace('-', '', str_slug($data['display_name']));
        $data['show'] = json_encode(array_keys($data['columnName']));
        $data['hidden'] = json_encode(array_values(array_diff($this->getColumns($module_id), array_keys($data['columnName']))));
        ColumnVisibility::find($id)->update($data);

        return redirect()->back()->with([
            'message' => __('column_visibilities.visibilities.update'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param $module_id
     * @param $role_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visibilitiesCreate($module_id, $role_id)
    {
        $columns = $this->getColumns($module_id);

        $moduleName = Module::select('name')->where('id', $module_id)->first()->name;
        $roleName = \DB::table('roles')->select('display_name')->where('id', $role_id)->first()->display_name;
        $lang = $this->langArray[$module_id];
        $isNumberColumns = $this->isNumberColumns;
        return view('columns_visibilities.visibilities.create', compact('isNumberColumns', 'lang', 'columns', 'role_id', 'module_id', 'moduleName', 'roleName'));

    }

    /**
     * @param VisibilitiesCreateRequest $request
     * @param $module_id
     * @param $role_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function visibilitiesStore(VisibilitiesCreateRequest $request, $module_id, $role_id)
    {
        $data = $request->all();
        $data['name'] = str_replace('-', '', str_slug($data['display_name']));
        $data['show'] = json_encode(array_keys($data['columnName']));
        $data['hidden'] = json_encode(array_values(array_diff($this->getColumns($module_id), array_keys($data['columnName']))));
        $data['module_id'] = $module_id;
        $data['role_id'] = $role_id;
        $visibilities = ColumnVisibility::create($data);

        return redirect()->route('columnVisibilities.modules.roles.visibilities.edit', ['module_id' => $module_id, 'role_id' => $role_id, 'id' => $visibilities->id])->with([
            'message' => __('column_visibilities.visibilities.store'),
            'alert-type' => 'success',
        ]);
    }

    public function visibilitiesDestroy($module_id, $role_id, $id)
    {
        ColumnVisibility::find($id)->delete();
        return redirect()->back()->with([
            'message' => 'widoczność została usunięta',
            'alert-type' => 'success',
        ]);

    }

}
