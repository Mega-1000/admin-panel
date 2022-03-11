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

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\WorkingEvents  $workingEvents
     * @return Response
     */
    public function show(WorkingEvents $workingEvents)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Entities\WorkingEvents  $workingEvents
     * @return Response
     */
    public function edit(WorkingEvents $workingEvents)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\WorkingEvents  $workingEvents
     * @return Response
     */
    public function update(Request $request, WorkingEvents $workingEvents)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\WorkingEvents  $workingEvents
     * @return Response
     */
    public function destroy(WorkingEvents $workingEvents)
    {
        //
    }
}
