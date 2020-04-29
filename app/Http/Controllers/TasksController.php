<?php

namespace App\Http\Controllers;

use App\Entities\Task;
use App\Helpers\OrderCalcHelper;
use App\Helpers\TaskTimeHelper;
use App\Jobs\AddLabelJob;
use App\Jobs\RemoveLabelJob;
use App\Repositories\OrderRepository;
use App\Repositories\TaskTimeRepository;
use App\Repositories\UserRepository;
use App\Repositories\WarehouseRepository;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Repositories\TaskRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Class TasksController.
 *
 * @package namespace App\Http\Controllers;
 */
class TasksController extends Controller
{
    /**
     * @var TaskRepository
     */
    protected $repository;

    protected $userRepository;

    protected $orderRepository;

    protected $warehouseRepository;

    protected $taskTimeRepository;

    public function __construct(
        TaskRepository $repository,
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        WarehouseRepository $warehouseRepository,
        TaskTimeRepository $taskTimeRepository
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->taskTimeRepository = $taskTimeRepository;
    }


    public function index()
    {
        return view('planning.tasks.index');
    }

    public function create()
    {
        $users = $this->userRepository->findWhere([['warehouse_id', '!=', null]]);
        $orders = $this->orderRepository->all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.tasks.create', compact('users', 'orders', 'warehouses'));
    }

    public function store(TaskCreateRequest $request)
    {
        $dataToStore = [
            'start' => $request->date_start,
            'end' => $request->date_end,
            'id' => $request->user_id,
            'user_id' => $request->user_id
        ];
        $allow = TaskTimeHelper::allowTaskMove($dataToStore);
        if ($allow === true) {
            $task = $this->repository->create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'created_by' => Auth::user()->id,
                'color' => $request->color,
                'warehouse_id' => $request->warehouse_id,
                'order_id' => $request->order_id !== null ? $request->order_id : null,
                'status' => 'WAITING_FOR_ACCEPT'
            ]);
            if ($request->order_id !== null) {
                $orderItemKMD = 0;
                $totalPrice = $task->order->total_price;
                $profit = 0;
                foreach ($task->order->items as $item) {
                    if ($item->product->symbol == 'KMD') {
                        $orderItemKMD = $item->quantity;
                    }
                    $itemsArray[] = $item->product_id;
                    $profit += (((float)$item->net_selling_price_commercial_unit * (int)$item->quantity) - ((float)$item->net_purchase_price_commercial_unit * (int)$item->quantity)) * 1.23;
                }
                if ($task->order->status_id === 4) {
                    $consultantVal = OrderCalcHelper::calcConsultantValue($orderItemKMD,
                        number_format($profit, 2, '.', ''));
                } else {
                    $consultantVal = $request->consultant_value;
                }
                $task->order->update([
                    'shipment_date' => $request->date_start,
                    'consultant_value' => $consultantVal,
                    'warehouse_value' => $request->warehouse_value,
                    'total_price' => $totalPrice
                ]);
                $dataToSave = $request->all();
                $arr = [
                    'consultant_notice' => $task->order->consultant_notice,
                    'warehouse_notice' => $task->order->warehouse_notice,
                ];
                $task->taskSalaryDetail()->create(array_merge($dataToStore, $arr));
                $prev = [];
                dispatch_now(new AddLabelJob($request->order_id, [47], $prev));
            }
            $this->taskTimeRepository->create([
                'task_id' => $task->id,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
            ]);
            $task->taskSalaryDetail()->create($request->all());

            return redirect()->route('planning.tasks.index')->with([
                'message' => __('tasks.messages.store'),
                'alert-type' => 'success'
            ]);
        } else {
            return redirect()->route('planning.tasks.index')->with([
                'message' => __('tasks.messages.store_error'),
                'alert-type' => 'error'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = $this->repository->find($id);
        $users = $this->userRepository->findWhere([['warehouse_id', '!=', null]]);
        $orders = $this->orderRepository->all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.tasks.edit', compact('task', 'warehouses', 'users', 'orders'));
    }

    public function update(TaskUpdateRequest $request, $id)
    {
        $task = $this->repository->find($id);

        if (empty($task)) {
            abort(404);
        }

        $dataToStore = [
            'start' => $request->date_start,
            'end' => $request->date_end,
            'id' => $id,
            'user_id' => $task->user_id
        ];
        $allow = TaskTimeHelper::allowTaskMove($dataToStore);
        if ($allow === true) {
            $dataToStore = $request->all();
            if ($request->color == '008000' || $request->color == '32CD32') {
                $dataToStore['status'] = 'FINISHED';
            }
            $task->update($dataToStore);
            $task->taskTime->update($request->all());
            $task->taskSalaryDetail->update($request->all());
            if ($request->order_id !== null) {
                $orderItemKMD = 0;
                $totalPrice = $task->order->total_price;
                $profit = 0;
                foreach ($task->order->items as $item) {
                    if ($item->product->symbol == 'KMD') {
                        $orderItemKMD = $item->quantity;
                    }
                    $itemsArray[] = $item->product_id;
                    $profit += (((float)$item->net_selling_price_commercial_unit * (int)$item->quantity) - ((float)$item->net_purchase_price_commercial_unit * (int)$item->quantity)) * 1.23;
                }
                if ($task->order->status_id === 4) {
                    $consultantVal = OrderCalcHelper::calcConsultantValue($orderItemKMD,
                        number_format($profit, 2, '.', ''));
                    $totalPrice += $consultantVal;
                } else {
                    $consultantVal = $request->consultant_value;
                    $totalPrice += $task->order->total_price + (float)$request->consultant_value;
                }
                $task->order->update([
                    'shipment_date' => $request->date_start,
                    'consultant_notice' => $request->consultant_notice,
                    'consultant_value' => $consultantVal,
                    'warehouse_notice' => $request->warehouse_notice,
                    'warehouse_value' => $request->warehouse_value,
                    'total_price' => $totalPrice
                ]);
                $task->taskSalaryDetail()->create($request->all());
                $prev = [];
                dispatch_now(new AddLabelJob($request->order_id, [47], $prev));
            }
            return redirect()->route('planning.tasks.edit', ['id' => $task->id])->with([
                'message' => __('tasks.messages.update'),
                'alert-type' => 'success'
            ]);
        } else {
            return redirect()->route('planning.tasks.edit', ['id' => $task->id])->with([
                'message' => __('tasks.messages.update_error'),
                'alert-type' => 'error'
            ]);
        }
    }


    public function destroy($id)
    {
        $task = $this->repository->find($id);

        if (empty($task)) {
            abort(404);
        }

        $task->delete($task->id);

        return redirect()->route('planning.tasks.index')->with([
            'message' => __('tasks.messages.delete'),
            'alert-type' => 'info'
        ]);
    }

    public function addNewTask(Request $request)
    {
        $dataToStore = [
            'start' => $request->start,
            'end' => $request->end,
            'id' => $request->id,
            'user_id' => $request->user_id
        ];

        $allow = TaskTimeHelper::allowTaskMove($dataToStore);
        if ($allow === true) {
            $dataToStore = $request->all();
            $task = $this->repository->create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'created_by' => Auth::user()->id,
                'color' => substr($request->color, 1),
                'warehouse_id' => $request->warehouse_id,
                'status' => 'WAITING_FOR_ACCEPT',
                'order_id' => $request->order_id !== null ? $request->order_id : null
            ]);
            $dateStart = new Carbon($request->start);
            $dateEnd = new Carbon($request->end);
            if ($request->order_id !== null) {
                $orderItemKMD = 0;
                $totalPrice = $task->order->total_price;
                $profit = 0;
                foreach ($task->order->items as $item) {
                    if ($item->product->symbol == 'KMD') {
                        $orderItemKMD = $item->quantity;
                    }
                    $itemsArray[] = $item->product_id;
                    $profit += (((float)$item->net_selling_price_commercial_unit * (int)$item->quantity) - ((float)$item->net_purchase_price_commercial_unit * (int)$item->quantity)) * 1.23;
                }
                if ($task->order->status_id === 4) {
                    $consultantVal = OrderCalcHelper::calcConsultantValue($orderItemKMD,
                        number_format($profit, 2, '.', ''));
                    $totalPrice += $consultantVal;
                } else {
                    $consultantVal = $request->consultant_value;
                    $totalPrice += $task->order->total_price + (float)$request->consultant_value;
                }
                $task->order->update([
                    'production_date' => $request->start,
                    'consultant_notice' => $request->consultant_notice,
                    'consultant_value' => $consultantVal,
                    'warehouse_notice' => $request->warehouse_notice,
                    'warehouse_value' => $request->warehouse_value,
                    'total_price' => $totalPrice,
                    'warehouse_id' => $task->warehouse_id
                ]);
                $dateTime = new Carbon($request->start);
                $title = $task->order_id . ' - ' . $dateTime->format('d-m') . ' - ' . $task->order->warehouse_value;
                $task->update([
                    'name' => $title
                ]);
                $prev = [];
                dispatch_now(new AddLabelJob($request->order_id, [47], $prev));
            }
            $task->taskSalaryDetail()->create($request->all());
            $this->taskTimeRepository->create([
                'task_id' => $task->id,
                'date_start' => $dateStart,
                'date_end' => $dateEnd,
            ]);
            if ($task->order_id != null) {
                $customId = 'taskOrder-' . $task->order_id;
            } else {
                $customId = 'task-' . $task->id;
            }
            return redirect()->route('planning.timetable.index', [
                'id' => $customId,
                'view_type' => $request->view_type,
                'active_start' => $request->active_start,
            ])->with([
                'message' => __('tasks.messages.store'),
                'alert-type' => 'success',
            ]);
        } else {
            return redirect()->route('planning.timetable.index')->with([
                'message' => __('tasks.messages.store_error'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function datatable()
    {
        $collection = $this->prepareCollection();

        return DataTables::collection($collection)->make(true);
    }

    public function prepareCollection()
    {
        $collection = $this->repository->with(['user', 'warehouse', 'taskTime'])->all();

        return $collection;
    }

    public function getTasks(Request $request, $id)
    {
        $tasks = $this->repository->with(['taskTime', 'taskSalaryDetail'])->whereHas('taskTime',
            function ($query) use ($request) {
                $query->where('date_start', '>=', $request->start);
                $query->where('date_end', '<=', $request->end);
            })->findWhere([['warehouse_id', '=', $id]]);

        $array = [];
        foreach ($tasks as $task) {
            $start = new Carbon($task->taskTime->date_start);
            $end = new Carbon($task->taskTime->date_end);
            if ($task->taskSalaryDetail != null) {
                $text = 'ID Zadania: ' . $task->id . ', Nazwa zadania: ' . $task->name . ', Wykonuje: ' . $task->user->name . ', Rozpoczęcie: ' . $start->toDateTimeString() . ', Zakończenie: ' . $end->toDateTimeString() . ', Koszt obsługi konsultanta: ' . $task->taskSalaryDetail->consultant_value . ', Uwagi konsultanta: ' . $task->taskSalaryDetail->consultant_notice . ', Koszt obsługi magazynu: ' . $task->taskSalaryDetail->warehouse_value . ', Uwagi magazynu: ' . $task->taskSalaryDetail->warehouse_notice;
                if ($task->order != null) {
                    $drnp = new Carbon($task->order->shipment_date);
                    $text .= ', Data rozpoczęcia nadawania przesyłki: ' . $drnp->toDateString();
                }
            } else {
                $text = 'ID Zadania: ' . $task->id . ', Nazwa zadania: ' . $task->name . ', Wykonuje: ' . $task->user->name . ', Rozpoczęcie: ' . $start->toDateTimeString() . ', Zakończenie: ' . $end->toDateTimeString();
            }
            $array[] = [
                'id' => $task->id,
                'resourceId' => $task->user_id,
                'title' => $task->name,
                'start' => $start->format('Y-m-d\TH:i'),
                'end' => $end->format('Y-m-d\TH:i'),
                'color' => '#' . $task->color,
                'text' => $text,
                'customOrderId' => $task->order_id != null ? 'taskOrder-' . $task->order_id : null,
                'customTaskId' => 'task-' . $task->id
            ];
        }

        return response()->json($array);
    }

    public function updateTaskTime(Request $request, $id)
    {
        $task = $this->repository->find($id);

        if (empty($task)) {
            abort(404);
        }
        $dataToStore = [
            'start' => $request->start,
            'end' => $request->end,
            'id' => $id,
            'user_id' => $task->user_id
        ];
        if ($task->order_id != null) {
            $customId = 'taskOrder-' . $task->order_id;
        } else {
            $customId = 'task-' . $task->id;
        }
        $allow = TaskTimeHelper::allowTaskMove($dataToStore);
        if ($allow === true) {
            if ($request->order_id !== null) {
                $task->order->update(['production_date' => $request->date_start]);
            }
            $task->taskTime->update([
                'date_start' => $request->start,
                'date_end' => $request->end
            ]);

            return redirect()->route('planning.timetable.index', [
                'id' => $customId,
                'view_type' => $request->view_type,
                'active_start' => $request->active_start,
            ])->with([
                'message' => __('tasks.messages.update'),
                'alert-type' => 'success'
            ]);
        } else {
            $tasks = $this->repository->with([
                'taskTime'
            ])->whereHas('taskTime', function ($query) use ($request) {
                $dateStartToday = new Carbon($request->start);
                $dateStartToday->setTime(0, 0, 0)->toDateTimeString();
                $dateEndToday = new Carbon($request->start);
                $dateEndToday->addDay()->setTime(0, 0, 0)->toDateTimeString();
                $query->where('date_start', '>=', $dateStartToday)->where('date_start', '<=',
                    $dateEndToday)->where('date_end', '>=', $dateStartToday)->where('date_end', '<=', $dateEndToday);
            })->findWhere([['user_id', '=', $task->user_id]]);
            $different = 0;
            $differentStart = $task->taskTime->date_start;
            $differentEnd = $task->taskTime->date_end;
            foreach ($tasks as $item) {
                if ($item->taskTime == null) {
                    continue;
                }
                if ($item->id == $id) {
                    $different = (strtotime($task->taskTime->date_start) - strtotime($request->start)) / 60;
                    $item->taskTime->update([
                        'date_start' => $request->start,
                        'date_end' => $request->end
                    ]);

                } else {
                    if ($different === 0) {
                        $differentS = (strtotime($differentStart) - strtotime($request->start)) / 60;
                        $differentE = (strtotime($differentEnd) - strtotime($request->end)) / 60;
                        $dateStart = new Carbon($item->taskTime->date_start);
                        $dateEnd = new Carbon($item->taskTime->date_end);

                        if ($differentS > 0) {
                            $dateS = $dateStart->subMinutes($differentS)->toDateTimeString();
                            $dateE = $dateEnd->subMinutes($differentS)->toDateTimeString();
                        } elseif ($differentE < 0) {
                            $dateS = $dateStart->addMinutes(abs($differentE))->toDateTimeString();
                            $dateE = $dateEnd->addMinutes(abs($differentE))->toDateTimeString();
                        }
                        $item->taskTime->update(['date_start' => $dateS, 'date_end' => $dateE]);
                    }
                }
            }

            return redirect()->route('planning.timetable.index', [
                'id' => $customId,
                'view_type' => $request->view_type,
                'active_start' => $request->active_start,
            ])->with([
                'message' => __('tasks.messages.update'),
                'alert-type' => 'success'
            ]);
        }
    }

    public function moveTask(Request $request, $id)
    {
        if ($request->move == true) {
            $task = $this->repository->find($id);
            if ($task->order_id != null) {
                $customId = 'taskOrder-' . $task->order_id;
            } else {
                $customId = 'task-' . $task->id;
            }
            if (empty($task)) {
                abort(404);
            }
            $start = new Carbon($request->start);
            $end = new Carbon($request->end);
            $dataToStore = [
                'start' => $start->addHour()->toDateTimeString(),
                'end' => $end->addHour()->toDateTimeString(),
                'id' => $id,
                'user_id' => $request->new_resource !== null ? $request->new_resource : $task->user_id
            ];
            $allow = TaskTimeHelper::allowTaskMove($dataToStore);
            $dataToSave = null;
            if ($allow === true) {
                if ($request->new_resource !== null) {
                    $dataToSave = ['user_id' => $request->new_resource];
                    $dataToSave = array_merge($dataToSave);
                    if ($request->old_resource == 37 && $task->order_id != null) {
                        $prev = [];
                        dispatch_now(new RemoveLabelJob($task->order_id, [47], $prev));
                    }
                }
                $task->update($dataToSave != null ? $dataToSave : $dataToStore);

                if ($task->order_id !== null) {
                    $shipmentDate = new Carbon($request->start);
                    $task->order->update(['production_date' => $request->start, 'shipment_date' => $shipmentDate->toDateString()]);
                }

                $task->taskTime->update([
                    'date_start' => $dataToStore['start'],
                    'date_end' => $dataToStore['end']
                ]);
                return redirect()->route('planning.timetable.index', [
                    'id' => $customId,
                    'view_type' => $request->view_type,
                    'active_start' => $request->active_start,
                ])->with([
                    'message' => __('tasks.messages.update'),
                    'alert-type' => 'success'
                ]);
            } else {
                return redirect()->route('planning.timetable.index', [
                    'id' => $customId,
                    'view_type' => $request->view_type,
                    'active_start' => $request->active_start,
                ])->with([
                    'message' => __('tasks.messages.update_error'),
                    'alert-type' => 'error'
                ]);
            }
        } else {
            $task = $this->repository->find($id);
            if ($task->order_id != null) {
                $customId = 'taskOrder-' . $task->order_id;
            } else {
                $customId = 'task-' . $task->id;
            }
            $tasks = $this->repository->with('taskTime')->whereHas('taskTime', function ($query) use ($request) {
                $dateStartToday = new Carbon($request->start);
                $dateStartToday->setTime(0, 0, 0)->toDateTimeString();
                $dateEndToday = new Carbon($request->start);
                $dateEndToday->addDay()->setTime(0, 0, 0)->toDateTimeString();
                $query->where('date_start', '>=', $dateStartToday)->where('date_start', '<=',
                    $dateEndToday)->where('date_end', '>=', $dateStartToday)->where('date_end', '<=', $dateEndToday);
            })->findWhere([['user_id', '=', $task->user_id]]);

            $different = 0;
            foreach ($tasks as $item) {
                if ($task->taskTime == null) {
                    continue;
                }
                if ($item->id == $id) {
                    $different = (strtotime($task->taskTime->date_start) - strtotime($request->start)) / 60;
                    if ($request->new_resource !== null) {
                        $item->update(['user_id' => $request->new_resource]);
                    }
                    $item->taskTime->update([
                        'date_start' => $request->start,
                        'date_end' => $request->end
                    ]);
                    if ($item->order_id !== null) {
                        $shipmentDate = new Carbon($request->start);
                        $item->order->update(['production_date' => $request->start, 'shipment_date' => $shipmentDate->toDateString()]);
                    }
                } else {
                    if ($different !== 0) {
                        $different = abs($different);
                    } else {
                        $different = (strtotime($task->taskTime->date_start) - strtotime($request->start)) / 60;
                    }
                    $dateStart = new Carbon($item->taskTime->date_start);
                    $dateEnd = new Carbon($item->taskTime->date_end);
                    if ($request->moveAllLeft == true) {
                        $dateS = $dateStart->subMinutes($different)->toDateTimeString();
                        $dateE = $dateEnd->subMinutes($different)->toDateTimeString();
                    } else {
                        $dateS = $dateStart->addMinutes($different)->toDateTimeString();
                        $dateE = $dateEnd->addMinutes($different)->toDateTimeString();
                    }

                    $item->taskTime->update([
                        'date_start' => $dateS,
                        'date_end' => $dateE
                    ]);
                    if ($item->order_id !== null) {
                        $shipmentDate = new Carbon($dateS);
                        $item->order->update(['production_date' => $dateS, 'shipment_date' => $shipmentDate->toDateString()]);
                    }
                }
            }
            return redirect()->route('planning.timetable.index', [
                'id' => $customId,
                'view_type' => $request->view_type,
                'active_start' => $request->active_start,
            ])->with([
                'message' => __('tasks.messages.update'),
                'alert-type' => 'success'
            ]);
        }
    }

    public function allowTaskMoveGet(Request $request)
    {
        $tasks = $this->repository->with(['taskTime'])->whereHas('taskTime', function ($query) use ($request) {
            $dateStart = new Carbon($request->start);
            $dateEnd = new Carbon($request->end);
            $query->whereRaw('((`date_start` BETWEEN "' . $dateStart->addMinute()->toDateTimeString() . '" AND "' . $dateEnd->subMinute()->toDateTimeString() . '" OR `date_end` BETWEEN "' . $dateStart->addMinute()->toDateTimeString() . '" AND "' . $dateEnd->subMinute()->toDateTimeString() . '") OR ("' . $dateStart->addMinute()->toDateTimeString() . '" BETWEEN `date_start` AND `date_end` OR "' . $dateEnd->subMinute()->toDateTimeString() . '" BETWEEN `date_start` AND `date_end` ))');
        })->findWhere([
            ['id', '!=', $request->id !== null ? $request->id : null],
            ['user_id', '=', $request->user_id]
        ])->all();
        $user = $this->userRepository->find($request->user_id);
        $today = Carbon::today();
        $userWorks = $user->userWorks->where('date_of_work', '=', $today->toDateString());
        $taskStart = new Carbon($request->start);
        $taskEnd = new Carbon($request->end);
        $dateStartUser = new Carbon($userWorks->first->id->date_of_work . ' ' . $userWorks->first->id->start);
        $dateEndUser = new Carbon($userWorks->first->id->date_of_work . ' ' . $userWorks->first->id->end);
        if (strtotime($dateStartUser->toDateTimeString()) <= strtotime($taskStart->toDateTimeString()) && strtotime($taskEnd->toDateTimeString()) <= strtotime($dateEndUser->toDateTimeString())) {
            if (count($tasks) > 0) {
                return response()->json(false);
            } else {
                return response()->json(true);
            }
        }

    }

    public function getTasksForUser($id, $userId)
    {
        $date = Carbon::today();
        $tasks = $this->repository->with(['taskTime'])->whereHas('taskTime', function ($query) use ($date) {
            $query->where('date_start', '>=', $date->toDateTimeString());
        })->findWhere([['warehouse_id', '=', $id], ['user_id', '=', $userId], ['status', '=', 'WAITING_FOR_ACCEPT']]);
        $array = [];
        foreach ($tasks as $task) {
            $start = new Carbon($task->taskTime->date_start);
            $end = new Carbon($task->taskTime->date_end);
            $array[] = [
                'id' => $task->id,
                'resourceId' => $task->user_id,
                'title' => $task->name,
                'start' => $start->addHours(2)->format('Y-m-d\TH:i'),
                'end' => $end->addHours(2)->format('Y-m-d\TH:i'),
                'color' => '#' . $task->color,
                'status' => $task->status
            ];

        }

        return response()->json($array);

    }

    public function acceptTask(Request $request)
    {
        $task = $this->repository->find($request->id);

        if (empty($task)) {
            abort(404);
        }

        $dataToStore = [
            'start' => $request->start,
            'end' => $request->end,
            'id' => $request->id,
            'user_id' => $task->user_id,
        ];
        $allow = TaskTimeHelper::allowTaskMove($dataToStore);

        if ($allow === true) {
            $task->update($request->all());
            $task->taskTime->update($request->all());

            $array = [
                'status' => 'TO_DO',
                'id' => $task->id,
            ];
            return response()->json($array);
        } else {
            $array = [
                'status' => 'WAITING_FOR_ACCEPT',
                'id' => $task->id,
            ];
            return response()->json($array);
        }
    }

    public function rejectTask(Request $request)
    {
        $task = $this->repository->find($request->id);

        if (empty($task)) {
            abort(404);
        }
        $dateStart = new Carbon($request->start);
        $dateEnd = new Carbon($request->end);

        $dataToStore = [
            'start' => $dateStart->addDay()->toDateTimeString(),
            'end' => $dateEnd->addDay()->toDateTimeString(),
            'id' => $request->id,
            'user_id' => $task->user_id,
        ];
        $allow = TaskTimeHelper::allowTaskMove($dataToStore);

        if ($allow === true) {
            $task->update($request->all());
            if ($request->order_id !== null) {
                $orderItemKMD = 0;
                $totalPrice = $task->order->total_price;
                $profit = 0;
                foreach ($task->order->items as $item) {
                    if ($item->product->symbol == 'KMD') {
                        $orderItemKMD = $item->quantity;
                    }
                    $itemsArray[] = $item->product_id;
                    $profit += (((float)$item->net_selling_price_commercial_unit * (int)$item->quantity) - ((float)$item->net_purchase_price_commercial_unit * (int)$item->quantity)) * 1.23;
                }
                if ($task->order->status_id === 4) {
                    $consultantVal = OrderCalcHelper::calcConsultantValue($orderItemKMD,
                        number_format($profit, 2, '.', ''));
                    $totalPrice += $consultantVal;
                } else {
                    $consultantVal = $request->consultant_value;
                    $totalPrice += $task->order->total_price + (float)$request->consultant_value;
                }
                $task->order->update([
                    'production_date' => $request->date_start,
                    'consultant_notice' => $request->consultant_notice,
                    'consultant_value' => $consultantVal,
                    'warehouse_notice' => $request->warehouse_notice,
                    'warehouse_value' => $request->warehouse_value,
                    'total_price' => $totalPrice
                ]);
                $task->taskSalaryDetail()->create($request->all());
                $prev = [];
                dispatch_now(new AddLabelJob($request->order_id, [47], $prev));
            }
            $task->taskTime->update([
                'date_start' => $dateStart->toDateTimeString(),
                'date_end' => $dateEnd->toDateTimeString(),
                'task_id' => $request->id,
                'user_id' => $task->user_id,
            ]);

            $array = [
                'status' => 'REJECTED',
                'id' => $task->id,
            ];
            return response()->json($array);
        } else {
            $array = [
                'status' => 'WAITING_FOR_ACCEPT',
                'id' => $task->id,
                'message' => 'Musisz wybrać inne godziny dla zadania o id: ' . $task->id
            ];
            return response()->json($array);
        }
    }

    public function updateTask(Request $request, $id)
    {
        if ($request->update == 1) {
            $task = $this->repository->find($id);
            if ($task->order_id != null) {
                $customId = 'taskOrder-' . $task->order_id;
            } else {
                $customId = 'task-' . $task->id;
            }
            if (empty($task)) {
                abort(404);
            }

            $dataToStore = [
                'start' => $request->start,
                'end' => $request->end,
                'id' => $id,
                'user_id' => $task->user_id,
                'color' => substr($request->color, 1),
                'name' => $request->name,
            ];
            if (substr($request->color, 1) == '008000' || substr($request->color, 1) == '32CD32') {
                $dataToStore['status'] = 'FINISHED';
            }
            $allow = TaskTimeHelper::allowTaskMove($dataToStore);
            if ($allow === true) {
                $dataToStore['date_start'] = $dataToStore['start'];
                $dataToStore['date_end'] = $dataToStore['end'];
                $task->update($dataToStore);
                $task->taskTime->update($dataToStore);
                if ($task->taskSalaryDetail == null) {
                    $task->taskSalaryDetail()->create($request->all());
                } else {
                    $task->taskSalaryDetail->update($request->all());
                }
                if ($task->order_id !== null) {
                    $task->taskSalaryDetail->update($request->all());
                    $orderItemKMD = 0;
                    $totalPrice = $task->order->total_price;
                    $profit = 0;
                    foreach ($task->order->items as $item) {
                        if ($item->product->symbol == 'KMD') {
                            $orderItemKMD = $item->quantity;
                        }
                        $itemsArray[] = $item->product_id;
                        $profit += (((float)$item->net_selling_price_commercial_unit * (int)$item->quantity) - ((float)$item->net_purchase_price_commercial_unit * (int)$item->quantity)) * 1.23;
                    }
                    if ($task->order->status_id === 4) {
                        $consultantVal = OrderCalcHelper::calcConsultantValue($orderItemKMD,
                            number_format($profit, 2, '.', ''));
                        $totalPrice += $consultantVal;
                    } else {
                        $consultantVal = $request->consultant_value;
                        $totalPrice += $task->order->total_price + (float)$request->consultant_value;
                    }
//                    if (strtotime($task->order->shipment_date) < strtotime($task->taskTime->date_start)) {
//                        $shipmentDate = $task->order->production_date;
//                    } else {
                    $shipmentDate = $request->shipment_date;
//                    }
                    $task->order->update([
                        'shipment_date' => $shipmentDate,
                        'consultant_notice' => $request->consultant_notice,
                        'consultant_value' => $consultantVal,
                        'warehouse_notice' => $request->warehouse_notice,
                        'warehouse_value' => $request->warehouse_value,
                        'total_price' => $totalPrice
                    ]);
                    $prev = [];

                    if ($task->color == '32CD32') {
                        dispatch_now(new RemoveLabelJob($task->order_id, [49], $prev));
                        dispatch_now(new AddLabelJob($task->order_id, [50], $prev));
                    }
                    if ($task->color == '008000') {
                        dispatch_now(new RemoveLabelJob($task->order_id, [74], $prev));
                        dispatch_now(new AddLabelJob($task->order_id, [41], $prev));
                    }
                    $dateTime = new Carbon($request->start);
                    $title = $task->order_id . ' - ' . $dateTime->format('d-m') . ' - ' . $task->order->warehouse_value;
                    $task->update([
                        'name' => $title
                    ]);
                }
                return redirect()->route('planning.timetable.index', [
                    'id' => $customId,
                    'view_type' => $request->view_type,
                    'active_start' => $request->active_start,
                ])->with([
                    'message' => __('tasks.messages.update'),
                    'alert-type' => 'success'
                ]);
            } else {
                return redirect()->route('planning.timetable.index', [
                    'id' => $customId,
                    'view_type' => $request->view_type,
                    'active_start' => $request->active_start,
                ])->with([
                    'message' => __('tasks.messages.update_error'),
                    'alert-type' => 'error'
                ]);
            }
        } else {
            $task = $this->repository->find($id);
            if ($task->order_id != null) {
                $customId = 'taskOrder-' . $task->order_id;
            } else {
                $customId = 'task-' . $task->id;
            }
            if (empty($task)) {
                abort(404);
            }

            $task->delete();

            return redirect()->route('planning.timetable.index', [
                'view_type' => $request->view_type,
                'active_start' => $request->active_start,
            ])->with([
                'message' => __('tasks.messages.delete'),
                'alert-type' => 'success'
            ]);
        }

    }

    public function getTask($id)
    {
        $task = $this->repository->with(['user', 'taskTime', 'taskSalaryDetail', 'order'])->find($id);

        if (empty($task)) {
            abort(404);
        }

        return response()->json($task);
    }
}
