<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\ProductAnalyzer;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class PriceAnalyzerController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProductAnalyzerController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('labels'));
        foreach ($visibilities as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
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
        $collection = ProductAnalyzer::whereHas('product')->with('product')->get();

        return $collection;
    }
}
