<?php

namespace App\Http\Controllers;

use App\Repositories\TaskRepository;
use App\Repositories\WarehouseRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimetablesController extends Controller
{
    protected $warehouseRepository;

    protected $taskRepository;

    public function __construct(WarehouseRepository $warehouseRepository, TaskRepository $taskRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
        $this->taskRepository = $taskRepository;
    }

    public function index(Request $request)
    {
        $activeDay = null;
        $viewType = null;
        if (isset($request->id)) {
            $string = explode('-', $request->id);
            if (isset($string[1])) {
                if($string[0] == 'taskOrder') {
                    $tasks = $this->taskRepository->findByField('order_id', (int)$string[1]);
                    if ($tasks->isEmpty()) {
                        return redirect()->back()->with([
                            'message' => 'Zadanie dla zamówienia o id: ' . $string[1] . ' nie istnieje.',
                            'alert-type' => 'error'
                        ]);
                    }
                    $dateView = new Carbon($tasks->first->id->taskTime->date_start);
                    $activeDay = $dateView->toDateTimeString();

                    $viewType = 'resourceTimelineDay';
                }
            }
        }
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.timetable.index', compact(['warehouses', 'viewType', 'activeDay']));
    }

    public function getStorekeepers($id)
    {
        $warehouse = $this->warehouseRepository->find($id);

        if (empty($warehouse)) {
            abort(404);
        }
        $array = [];
        foreach ($warehouse->users as $user) {
            $date = Carbon::today();
            $array[] = [
                'id' => $user->id,
                'title' => $user->firstname . ' ' . $user->lastname,
                'businessHours' => [
                    'startTime' => $user->userWorks->where('date_of_work', '=', $date->toDateString())->first()->start,
                    'endTime' => $user->userWorks->where('date_of_work', '=', $date->toDateString())->first()->end,
                    'daysOfWeek' => [0, 1, 2, 3, 4, 5, 6]
                ],
            ];
        }

        return response()->json($array, 200);
    }

    public function getStorekeepersToModal($id)
    {
        $date = Carbon::today();
        $warehouse = $this->warehouseRepository->with([
            'users',
            'users.userWorks' => function ($query) use ($date) {
                $query->where('date_of_work', '=', $date->toDateString());
            }
        ])->find($id);

        if (empty($warehouse)) {
            abort(404);
        }

        return response()->json($warehouse->users, 200);
    }
}
