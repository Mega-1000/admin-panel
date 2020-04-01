<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\PackageTemplate;
use App\Entities\ContentType;
use App\Entities\PackingType;

class PackageTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templatesUnsorted = \App\Entities\PackageTemplate::all();
        $templates = $templatesUnsorted->sortBy('list_order');
        return view('package_templates.index',compact('templates'))
        ->withpackageTemplates($templates);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $contentTypes = ContentType::all();
        $packingTypes = PackingType::all();
        return view('package_templates.create')->withcontentTypes($contentTypes)->withpackingTypes($packingTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->saveTemplate($request);
        return redirect()->route('package_templates.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $packageTemplate = PackageTemplate::find($id);
        $contentTypes = ContentType::all();
        $packingTypes = PackingType::all();
        return view('package_templates.edit')->withOld($packageTemplate)->withcontentTypes($contentTypes)->withpackingTypes($packingTypes);
    }

    private function saveTemplate($request, $id = null)
    {
        $this->validate($request, array(
            'name'=>'required|max:255',
            'notice_max_lenght'=>'integer|required',
            'symbol' => 'required',
            'max_weight' => 'numeric|required',
            'volume' => 'integer|required'
        ));
        if (!empty($request->accept_time)) {
            $this->validate($request, array(
                'max_time' => 'required'
            ));
        }
        if (!empty($request->max_time)) {
            $this->validate($request, array(
                'accept_time' => 'required'
            ));
        }
        if (empty($id)) {
            $template = new PackageTemplate;
        } else {
            $template = PackageTemplate::find($id);
        }
        $template->name = $request->name;
        $template->symbol = $request->symbol;
        $template->sello_delivery_id = $request->sello_delivery_id;
        $template->sello_deliverer_id = $request->sello_deliverer_id;
        $template->info = $request->info;
        $template->sizeA = $request->sizeA;
        $template->sizeB = $request->sizeB;
        $template->sizeC = $request->sizeC;
        $template->accept_time = $request->accept_time;
        $template->accept_time_info = $request->accept_time_info;
        $template->max_time = $request->max_time;
        $template->max_time_info = $request->max_time_info;
        $template->service_courier_name = $request->service_courier_name;
        $template->delivery_courier_name = $request->delivery_courier_name;
        $template->weight = $request->weight;
        $template->container_type = $request->container_type;
        $template->shape = $request->shape;
        $template->notice_max_lenght = $request->notice_max_lenght;
        $template->content = $request->content;
        $template->cod_cost = $request->cod_cost;
        $template->approx_cost_client = $request->approx_cost_client;
        $template->approx_cost_firm = $request->approx_cost_firm;
        $template->max_weight = $request->max_weight;
        $template->volume = $request->volume;
        $template->list_order = $request->list_order;
        $template->displayed_name = $request->displayed_name;

        $template->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->saveTemplate($request, $id);
        return redirect()->route('package_templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $packageTemplate = PackageTemplate::find($id);
        $packageTemplate->delete();
        return redirect()->route('package_templates.index');
    }
}
