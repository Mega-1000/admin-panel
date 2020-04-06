<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\ContainerType;

class ContainerTypesController extends Controller
{
    public function index()
    {
        $containerTypes = ContainerType::all();
        return view('container_types.index',compact('containerTypes'))
        ->withcontainerTypes($containerTypes);
    }

    public function create()
    {
         return view('container_types.create');
    }

    public function store(Request $request)
    {
        $this->saveContainerType($request);

        return redirect()->route('container_type.index')->with([
            'message' => __('order_packages.message.container_store'),
            'alert-type' => 'success'
        ]);
    }

    public function edit($id)
    {
        $containerType = ContainerType::find($id);

        return view('container_types.create', compact('containerType'));
    }
 
    public function update(Request $request, $id)
    {
        $this->saveContainerType($request, $id);

        return redirect()->route('container_type.index')->with([
            'message' => __('order_packages.message.container_update'),
            'alert-type' => 'success'
        ]);
    }
    
    private function saveContainerType(Request $request, $id = null)
    {
        if (is_null($id)) {
            $containerType = new ContainerType;
        } else {
            $containerType = ContainerType::find($id);
        }
        $containerType->name = $request->name;
        $containerType->symbol = $request->symbol;
        $containerType->save();
    }

    public function destroy($id)
    {
        $containerType = ContainerType::find($id); 
        $containerType->delete();

        return redirect()->route('container_type.index');
    }
}
