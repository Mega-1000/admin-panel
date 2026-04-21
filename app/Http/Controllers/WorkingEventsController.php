<?php

namespace App\Http\Controllers;

use App\Entities\WorkingEvents;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        if (in_array($user->email, ['info@mega1000.pl', 'admin@admin.com'])) {
            return view('working_events.index');
        } else {
            return redirect()->route('orders.index')->with(['message' => 'Brak uprawnień do akcji',
                'alert-type' => 'success']);
        }
    }
}
