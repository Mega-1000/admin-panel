<?php

namespace App\Http\Controllers;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class TrackerLogsController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tracker_logs.index');
    }
}
