<?php

namespace App\Http\Controllers;

use App\Entities\Task;
use App\Entities\Warehouse;
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
        $selectId = null;
        if (isset($request->id)) {
            $string = explode('-', $request->id);
            if (isset($string[1])) {
                if($string[0] == 'taskOrder') {
                    $tasks = Task::where('order_id', (int)$string[1]);
                    if ($tasks->count() == 0) {
                        return redirect()->back()->with([
                            'message' => 'Zadanie dla zamówienia o id: ' . $string[1] . ' nie istnieje.',
                            'alert-type' => 'error'
                        ]);
                    }
                    $task = $tasks->first();
                    $parent = $task->parent()->first();
                    if ($parent) {
                        $task = $parent;
                    }
                    $selectId = $task->id;
                    $dateView = new Carbon($task->taskTime->date_start);
                    $activeDay = $dateView->toDateTimeString();
                    $taskDiffInMins = $dateView->diffInMinutes(new Carbon($task->taskTime->date_end));

                    $viewType = 'resourceTimelineDay';
                }
            }
        }
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.timetable.index', compact(['warehouses', 'viewType', 'activeDay', 'selectId', 'taskDiffInMins']));
    }

    public function getStorekeepers($id)
    {
        $warehouse = Warehouse::find($id);

        if (empty($warehouse)) {
            abort(404);
        }
        $array = [];
        foreach ($warehouse->users as $user) {
            $date = Carbon::today();
            $works = $user->userWorks()->where('date_of_work', '=', $date->toDateString())->first();
            if (!$works) {
                continue;
            }
            $array[] = [
                'id' => $user->id,
                'title' => $user->firstname . ' ' . $user->lastname,
                'businessHours' => [
                    'startTime' => $works->start,
                    'endTime' => $works->end,
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
