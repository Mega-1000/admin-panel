<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\ProductAnalyzer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class PriceAnalyzerController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProductAnalyzerController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
	    $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('labels'));
	    foreach($visibilities as $key => $row)
	    {
		    $visibilities[$key]->show = json_decode($row->show,true);
		    $visibilities[$key]->hidden = json_decode($row->hidden,true);
	    }
	    
        return view('product_analyzer.index', compact('visibilities'));
    }
	
	public function datatable(Request $request)
	{
		$collection = $this->prepareCollection();
		
        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection()
    {
        $collection = ProductAnalyzer::with('product')->get();

        return $collection;
    }
}
