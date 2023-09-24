<?php

namespace App\Http\Controllers;

use App\Entities\Task;
use App\Entities\Warehouse;
use App\Repositories\TaskRepository;
use App\Repositories\WarehouseRepository;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TimetablesController extends Controller
{
    public function __construct(
        protected readonly WarehouseRepository $warehouseRepository,
        protected readonly TaskRepository      $taskRepository,
        protected readonly TaskService         $taskService
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $activeDay = null;
        $viewType = null;
        $selectId = $taskDiffInMins = $taskHour = null;
        if (isset($request->id)) {
            $string = explode('-', $request->id);
            if (isset($string[1])) {
                if ($string[0] == 'taskOrder') {
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
                    $taskHour = $dateView->hour;
                    $viewType = 'resourceTimelineDay';
                }
            }
        }
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.timetable.index', compact(['warehouses', 'viewType', 'activeDay', 'selectId', 'taskDiffInMins', 'taskHour']));
    }

    public function getStorekeepers($id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);

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

        return response()->json($array);
    }

    public function getStorekeepersToModal($id): JsonResponse
    {
        $date = Carbon::today();
        $warehouse = $this->warehouseRepository->with([
            'users',
            'users.userWorks' => function ($query) use ($date) {
                $query->where('date_of_work', '=', $date->toDateString());
            }
        ])->find($id);


        return response()->json($warehouse->users);
    }
}
