<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\PackingType;

class PackingTypesController extends Controller
{
    public function index()
    {
        $packingTypes = PackingType::all();
        return view('packing_types.index',compact('packingTypes'))
        ->withpackingTypes($packingTypes);
    }

    public function create()
    {
         return view('packing_types.create');
    }

    public function store(Request $request)
    {
        $this->savePackingType($request);

        return redirect()->route('packing_type.index')->with([
            'message' => __('order_packages.message.packing_store'),
            'alert-type' => 'success'
        ]);
    }

    public function edit($id)
    {
        $packingType = PackingType::find($id);

        return view('packing_types.create', compact('packingType'));
    }
 
    public function update(Request $request, $id)
    {
        $this->savePackingType($request, $id);

        return redirect()->route('packing_type.index')->with([
            'message' => __('order_packages.message.packing_update'),
            'alert-type' => 'success'
        ]);
    }
    
    private function savePackingType(Request $request, $id = null) {

       if (is_null($id)) {
       $packingType = new PackingType;
       } else {
           $packingType= PackingType::find($id);
       }
       $packingType->name = $request->name;
       $packingType->symbol = $request->symbol;
       $packingType->save();

    }

    public function destroy($id)
    {
        $packingType = PackingType::find($id); 
        $packingType->delete();

        return redirect()->route('packing_type.index');
    }
}
