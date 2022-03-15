<?php

namespace App\Http\Controllers;

use App\Entities\WorkingEvents;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class WorkingEventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|Response|View
     */
    public function index()
    {
        return view('working_events.index');
    }
}
