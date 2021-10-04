<?php

namespace App\Http\Controllers;

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
        return view('product_analyzer.index');
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
        $collection = ProductAnalyzer::with('product')->all();

        return $collection;
    }
}
