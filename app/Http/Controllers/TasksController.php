<?php

namespace App\Http\Controllers;

use App\Entities\Label;
use App\Entities\LabelGroup;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\TaskTime;
use App\Entities\TrackerLogs;
use App\Entities\Warehouse;
use App\Helpers\OrderCalcHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\TaskHelper;
use App\Helpers\TaskTimeHelper;
use App\Http\Requests\DenyTaskRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Requests\BreakDownRequest;
use App\Http\Requests\AddingTaskToPlanerRequest;
use App\Http\Requests\GetTaskRequest;
use App\Repositories\OrderRepository;
use App\Repositories\TaskRepository;
use App\Repositories\Tasks;
use App\Repositories\TaskTimes;
use App\Repositories\TaskTimeRepository;
use App\Repositories\UserRepository;
use App\Repositories\WarehouseRepository;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\TaskService;
use App\User;
use Carbon\Carbon;
use Exception;
use Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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

    public function __construct(
        TaskRepository      $repository,
        UserRepository      $userRepository,
        OrderRepository     $orderRepository,
        WarehouseRepository $warehouseRepository,
        protected readonly Tasks                $tasksRepository,
        protected readonly TaskTimeRepository   $taskTimeRepository,
        protected readonly TaskService          $taskService,
        protected readonly TaskTimes            $taskTimesRepository
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->warehouseRepository = $warehouseRepository;
    }


    public function index()
    {
        return view('planning.tasks.index');
    }

    public function store(TaskCreateRequest $request)
    {
        if ($request->quickTask) {
            $time = Carbon::now();
            $time->second = 0;
            $request->date_start = $time;
            $request->date_end = Carbon::now()->addMinutes(5);
        }
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
                    $profit = $this->calculateProfit($item, $profit);
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
                $arr = [];
                if (empty($request->order_id)) {
                    $arr = [
                        'consultant_notice' => $task->order->consultant_notice,
                        'warehouse_notice' => $task->order->warehouse_notice,
                    ];
                }
                $task->taskSalaryDetail()->create(array_merge($dataToStore, $arr));
                $prev = [];
                $order = Order::query()->findOrFail($request->order_id);
                AddLabelService::addLabels($order, [47], $prev, [], Auth::user()->id);
            }
            $this->taskTimeRepository->create([
                'task_id' => $task->id,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
            ]);

            $task->taskSalaryDetail()->create($request->all());

            if ($request->quickTask) {
                return back()->with(['message' => __('tasks.messages.store'),
                    'alert-type' => 'success'
                ]);
            }
            return redirect()->route('planning.tasks.index')->with([
                'message' => __('tasks.messages.store'),
                'alert-type' => 'success'
            ]);
        } else {
            if ($request->quickTask) {
                return back()->with(['message' => __('tasks.messages.store_error'),
                    'alert-type' => 'error'
                ]);
            }
            return redirect()->route('planning.tasks.index')->with([
                'message' => __('tasks.messages.store_error'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function create()
    {
        $users = $this->userRepository->findWhere([['warehouse_id', '!=', null]]);
        $orders = $this->orderRepository->all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.tasks.create', compact('users', 'orders', 'warehouses'));
    }

    /**
     * @param       $item
     * @param float $profit
     *
     * @return float
     */
    private function calculateProfit($item, float $profit): float
    {
        $profit += (((float)$item->gross_selling_price_commercial_unit * (int)$item->quantity) - ((float)$item->gross_purchase_price_commercial_unit * (int)$item->quantity));
        return $profit;
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
                $dataToStore['status'] = Task::FINISHED;
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
                    $profit = $this->calculateProfit($item, $profit);
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
                    'consultant_value' => $consultantVal,
                    'warehouse_value' => $request->warehouse_value,
                    'total_price' => $totalPrice
                ]);
                $task->taskSalaryDetail()->create($request->all());
                $prev = [];
                $order = Order::query()->findOrFail($request->order_id);
                AddLabelService::addLabels($order, [47], $prev, [], Auth::user()->id);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return View
     */
    public function edit(int $id)
    {
        $task = $this->repository->find($id);
        $users = $this->userRepository->findWhere([['warehouse_id', '!=', null]]);
        $orders = $this->orderRepository->all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.tasks.edit', compact('task', 'warehouses', 'users', 'orders'));
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

        if (!empty($request->order_id)) {
            $count = Task::where('order_id', $request->order_id)->count();
            if ($count) {
                return redirect()->route('planning.timetable.index')->with([
                    'message' => __('tasks.messages.task_with_order_exist'),
                    'alert-type' => 'error'
                ]);
            }
        }
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
                    $profit = $this->calculateProfit($item, $profit);
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
                    'consultant_value' => $consultantVal,
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
                $order = Order::query()->findOrFail($request->order_id);
                AddLabelService::addLabels($order, [47], $prev, [], Auth::user()->id);
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

    public function getTasks(GetTaskRequest $request, $id)
    {
        $request->validated();
        $data = $request->all();

        $tasks = Task::with(['taskTime', 'taskSalaryDetail'])
            ->whereHas('taskTime',
                function ($query) use ($request) {
                    $query->whereDate('date_start', '>=', $request->start);
                    $query->whereDate('date_end', '<=', $request->end);
                })
            ->where('warehouse_id', $id)
            ->whereNull('parent_id')
            ->whereNull('rendering')
            ->get();

        $laziness = TrackerLogs::whereDate('created_at', '>=', $request->start)
            ->whereDate('updated_at', '<=', $request->end)->get();


        $array = [];
        foreach ($tasks as $task) {
            $start = new Carbon($task->taskTime->date_start);
            $end = new Carbon($task->taskTime->date_end);
            if ($task->taskSalaryDetail != null) {
                $consultantNotice = $task->order ? $task->order->consultant_notices : $task->taskSalaryDetail->consultant_notice;
                $warehouseNotice = $task->order ? $task->order->warehouse_notice : $task->taskSalaryDetail->warehouse_notice;
                $text = 'ID Zadania: ' . $task->id . ', Nazwa zadania: ' . $task->name . ', Wykonuje: ' . $task->user->name . ', Rozpoczęcie: ' . $start->toDateTimeString() . ', Zakończenie: ' . $end->toDateTimeString() . ', Koszt obsługi konsultanta: ' . $task->taskSalaryDetail->consultant_value . ', Uwagi konsultanta: ' . $consultantNotice . ', Koszt obsługi magazynu: ' . $task->taskSalaryDetail->warehouse_value . ', Uwagi magazynu: ' . $warehouseNotice;
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
        //Zaśmieca timetable - narazie zakomentujemy
//        foreach ($laziness as $task) {
//            $start = new Carbon($task->created_at);
//            $end = new Carbon($task->updated_at);
//            $consultantNotice = $warehouseNotice = '';
//            $text = $task->description;
//
//            $array[] = [
//                'id' => 'tracker_id_'.$task->id,
//                'resourceId' => $task->user_id,
//                'title' => 'Brak aktywności',
//                'start' => $start->format('Y-m-d\TH:i'),
//                'end' => $end->format('Y-m-d\TH:i'),
//                'color' => '#FF0000',
//                'text' => $text ?? 'Brak uzasadnienia',
//                'customOrderId' => '',
//                'customTaskId' => ''
//            ];
//        }

        //DODANIE SEPARATORA
        $array = array_merge($array,$this->taskService->getSeparator($id,$data['start'],$data['end']));

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
                        $dateS = $dateStart->addMinutes(abs($differentE))->toDateTimeString();
                        $dateE = $dateEnd->addMinutes(abs($differentE))->toDateTimeString();

                        if ($differentS > 0) {
                            $dateS = $dateStart->subMinutes($differentS)->toDateTimeString();
                            $dateE = $dateEnd->subMinutes($differentS)->toDateTimeString();
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
        if ($request->move) {
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
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
                'id' => $id,
                'user_id' => $request->new_resource !== null ? $request->new_resource : $task->user_id
            ];
            $allow = TaskTimeHelper::allowTaskMove($dataToStore);
            $dataToSave = null;
            if ($allow === true) {
                if ($request->new_resource !== null) {
                    $dataToSave = ['user_id' => $request->new_resource];
                    $dataToSave = array_merge($dataToSave);
                    if ($request->new_resource != Task::TO_CONFIRM_USER_ID) {
                        if ($task->childs()->count() > 0) {
                            $task->childs()->get()->map(function ($child) use ($request) {
                                $this->removeLabel($request, $child);
                            });
                        } else {
                            $this->removeLabel($request, $task);
                        }
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

                    $dateS = $dateStart->addMinutes($different)->toDateTimeString();
                    $dateE = $dateEnd->addMinutes($different)->toDateTimeString();

                    if ($request->moveAllLeft) {
                        $dateS = $dateStart->subMinutes($different)->toDateTimeString();
                        $dateE = $dateEnd->subMinutes($different)->toDateTimeString();
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

    private function removeLabel(Request $request, $task)
    {
        if ($request->old_resource == 37 && $task->order_id != null) {
            $preventionArray = [];
            RemoveLabelService::removeLabels($task->order, [47], $preventionArray, [], Auth::user()->id);
        }
    }

    public function getForUser($id)
    {
        if (empty(User::find($id))) {
            return response(['error' => 'Brak danego użytkownika'], 400);
        }
        $tasks = Task::where('tasks.user_id', $id)
            ->where(function ($query) {
                $query->whereHas('order', function ($query) {
                    $query->whereHas('labels', function ($query) {
                        $query->where('labels.id', Label::ORDER_ITEMS_UNDER_CONSTRUCTION);
                    });
                })->orWhereHas('childs', function ($query) {
                    $query->whereHas('order', function ($query) {
                        $query->whereHas('labels', function ($query) {
                            $query->where('labels.id', Label::ORDER_ITEMS_UNDER_CONSTRUCTION);
                        });
                    });
                });
            })->orWhere(function ($query) use ($id) {
                $query->where('status', 'WAITING_FOR_ACCEPT')
                    ->where('tasks.user_id', $id)
                    ->where(function ($query) {
                        $query->whereDoesntHave('order')
                            ->orWhereDoesntHave('childs');
                    });
            })->get();
        return response($tasks);
    }

    public function produceOrdersRedirect(Request $request)
    {
        try {
            $task = Task::findOrFail($request->id);
            $end = Carbon::now();
            $end->second = 0;
            $task->TaskTime->date_end = $end;
            $task->status = Task::FINISHED;
            $task->TaskTime->save();

            $response = $this->markTaskAsProduced($task);
            if ($response === false) {
                return redirect()->back()->with([
                    'message' => __('tasks.messages.stocks_invalid'),
                    'alert-type' => 'error',
                    'stock-response' => [],
                ]);
            }
            $task->save();
            if ($request->warehouse_notice) {
                $task->taskSalaryDetail->warehouse_notice .= Order::formatMessage($task->user, $request->warehouse_notice);
                $task->taskSalaryDetail->save();
            }

            if ($request->description) {
                $task->taskSalaryDetail->warehouse_notice .= Order::formatMessage($task->user, $request->description);
                $task->taskSalaryDetail->save();
            }
        } catch (Exception $e) {
            return redirect()->back()->with([
                'message' => __('tasks.messages.update_error'),
                'alert-type' => 'error'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('tasks.messages.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param Request $request
     */
    private function markTaskAsProduced($task): bool
    {
        $response = null;
        if ($task->childs->count()) {
            $task->childs->map(function ($child) use (&$response) {
                if ($child->order_id) {
                    $preventionArray = [];
                    $response = RemoveLabelService::removeLabels(
                        $child->order,
                        [Label::ORDER_ITEMS_UNDER_CONSTRUCTION],
                        $preventionArray,
                        [],
                        Auth::user()->id
                    );
                }
            });
        } else if ($task->order_id) {
            $preventionArray = [];
            $response = RemoveLabelService::removeLabels(
                $task->order,
                [Label::ORDER_ITEMS_UNDER_CONSTRUCTION],
                $preventionArray,
                [],
                Auth::user()->id
            );
        }
        return array_key_exists('success', $response);
    }

    public function deny(DenyTaskRequest $request)
    {
        $request->validated();
        $data = $request->all();

        $task = Task::find($data['task_id']);
        $time = $task->taskTime()->first();
        $newTask = Task::create([
            'warehouse_id' => Warehouse::OLAWA_WAREHOUSE_ID,
            'user_id' => $task->user_id,
            'created_by' => $task->user_id,
            'name' => 'odrzucone zadanie',
            'color' => Task::DISABLED_COLOR,
            'status' => Task::FINISHED
        ]);

        $end = Carbon::now();
        $end->second = 0;
        TaskTime::create([
            'task_id' => $newTask->id,
            'date_start' => $time->date_start,
            'date_end' => $end
        ]);
        TaskSalaryDetails::create([
            'task_id' => $newTask->id,
            'consultant_value' => 0,
            'warehouse_value' => 0
        ]);
        $task->status = Task::FINISHED;
        $task->user_id = Task::TO_CONFIRM_USER_ID;
        $task->save();
        $user = $task->user;
        if ($task->childs->count()) {
            $task->childs->map(function ($item) use ($data, $user) {
                $this->addNotification($user, $data['description'], $item);
                if ($item->order) {
                    $item->order->labels()->attach(Label::SHIPPING_MARK);
                    $prev = [];
                    AddLabelService::addLabels($item->order, [Label::RED_HAMMER_ID], $prev, [], Auth::user()->id);
                }
            });
        } else {
            $this->addNotification($user, $data['description'], $task);
            if ($task->order) {
                $task->order->labels()->attach(Label::SHIPPING_MARK);
                $prev = [];
                AddLabelService::addLabels($task->order, [Label::RED_HAMMER_ID], $prev, [], Auth::user()->id);
            }
        }
        return redirect()->back()->with([
            'message' => __('tasks.messages.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $user
     * @param $description
     * @param $item
     */
    private function addNotification($user, $description, $item): void
    {
        $message = Order::formatMessage($user, $description);
        if ($item->order) {
            $item->order->warehouse_notice .= $message;
            $item->order->save();
        } else {
            $item->taskSalaryDetail->warehouse_notice .= $message;
        }
    }

    public function produceOrders(Request $request)
    {
        if ($request->id) {
            try {
                $task = Task::findOrFail($request->id);
                $this->markTaskAsProduced($task);
            } catch (Exception $e) {
                return response(['error' => true, 'message' => 'Nie znaleziono zadania']);
            }
            return response(['success' => true]);
        } else {
            return response(['error' => true]);
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
            }

            return response()->json(true);
        }

        return response()->json(false);
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
                'status' => Task::WAITING_FOR_ACCEPT,
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
                    $profit = $this->calculateProfit($item, $profit);
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
                    'consultant_value' => $consultantVal,
                    'warehouse_value' => $request->warehouse_value,
                    'total_price' => $totalPrice
                ]);
                $task->taskSalaryDetail()->create($request->all());
                $order = Order::query()->findOrFail($request->order_id);
                $prev = [];
                AddLabelService::addLabels($order, [Label::RED_HAMMER_ID], $prev, [], Auth::user()->id);
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
            return $this->onlyUpdateTask($id, $request);
        } else {
            return $this->deleteTask($id, $request);
        }

    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function onlyUpdateTask($id, Request $request): RedirectResponse
    {
        $task = $this->repository->find($id);
        if ($task->order_id != null) {
            $customId = 'taskOrder-' . $task->order_id;
        } else {
            $customId = 'task-' . $task->id;
        }
        if (empty($task)) {
            abort(404);
        }
        if ($request->new_group) {
            $newGroup = Task::whereIn('id', $request->new_group)->get();
            $this->updateOldAndCreateNewGroup($newGroup, $request, $task);
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
            $dataToStore['status'] = Task::FINISHED;
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
                    $profit = $this->calculateProfit($item, $profit);
                }
                if ($task->order->status_id === 4) {
                    $consultantVal = OrderCalcHelper::calcConsultantValue($orderItemKMD,
                        number_format($profit, 2, '.', ''));
                    $totalPrice += $consultantVal;
                } else {
                    $consultantVal = $request->consultant_value;
                    $totalPrice += $task->order->total_price + (float)$request->consultant_value;
                }
                $shipmentDate = $request->shipment_date;
                $task->order->update([
                    'shipment_date' => $shipmentDate,
                    'consultant_value' => $consultantVal,
                    'warehouse_value' => $request->warehouse_value,
                    'total_price' => $totalPrice
                ]);
                $prev = [];

                if ($task->color == '32CD32') {
                    RemoveLabelService::removeLabels($task->order, [49], $prev, [], Auth::user()->id);
                    AddLabelService::addLabels($task->order, [50], $prev, [], Auth::user()->id);
                }
                if ($task->color == '008000') {
                    RemoveLabelService::removeLabels($task->order, [74], $prev, [], Auth::user()->id);
                    AddLabelService::addLabels($task->order, [41], $prev, [], Auth::user()->id);
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
        }
        return redirect()->route('planning.timetable.index', [
            'id' => $customId,
            'view_type' => $request->view_type,
            'active_start' => $request->active_start,
        ])->with([
            'message' => __('tasks.messages.update_error'),
            'alert-type' => 'error'
        ]);
    }

    /**
     * @param $newGroup
     * @param $request
     * @param $task
     */
    private function updateOldAndCreateNewGroup($newGroup, $request, $task): void
    {
        $duration = $newGroup->reduce(function ($prev, $next) {
            $time = $next->taskTime;
            $finishTime = new Carbon($time->date_start);
            $startTime = new Carbon($time->date_end);
            $totalDuration = $finishTime->diffInMinutes($startTime);
            return $prev + $totalDuration;
        }, 0);
        $taskTime = $task->taskTime;
        $endTime = new Carbon($taskTime->date_end);
        $endTime->subMinutes($duration);
        $taskTime->date_end = $endTime->toDateTimeString();
        $taskTime->save();
        $request->end = $taskTime->date_end;
        if ($newGroup->count() > 1) {
            TaskHelper::createNewGroup($newGroup, $task, $duration);
        } else {
            TaskHelper::updateAbandonedTaskTime($newGroup->first(), $duration);
        }
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function deleteTask($id, Request $request): RedirectResponse
    {
        $task = Task::find($id);
        if ($task->order_id != null) {
            $customId = 'taskOrder-' . $task->order_id;
        } else {
            $customId = 'task-' . $task->id;
        }
        if (empty($task)) {
            abort(404);
        }

        if ($task->childs()->count()) {
            $orders = $task->childs->map(function ($task) {
                return $task->order;
            });
            $canDelete = $orders->filter(function ($order) {
                    return $order->labels()->where('label_id', Label::GREEN_HAMMER_ID)->count();
                })->count() == 0;
            if (!$canDelete) {
                return redirect()->route('planning.timetable.index', [
                    'view_type' => $request->view_type,
                    'active_start' => $request->active_start,
                ])->with([
                    'message' => __('tasks.messages.cannot_delete_ask_warehouse'),
                    'alert-type' => 'error'
                ]);
            }
            $orders->map(function ($order) {
                $order->labels()->detach(Label::BLUE_HAMMER_ID);
            });
            $task->childs()->delete();
            $task->delete();
        } else {
            if (!empty($task->order)) {
                $canDelete = $task->order->labels()->where('labels.id', Label::GREEN_HAMMER_ID)->count() == 0;
                if (!$canDelete) {
                    return redirect()->route('planning.timetable.index', [
                        'view_type' => $request->view_type,
                        'active_start' => $request->active_start,
                    ])->with([
                        'message' => __('tasks.messages.cannot_delete_ask_warehouse'),
                        'alert-type' => 'error'
                    ]);
                }
                $task->order->labels()->detach(Label::BLUE_HAMMER_ID);
            }
            $task->delete();
        }

        return redirect()->route('planning.timetable.index', [
            'view_type' => $request->view_type,
            'active_start' => $request->active_start,
        ])->with([
            'message' => __('tasks.messages.delete'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Get Task
     * @param $id
     */
    public function getTask($id): JsonResponse
    {
        $task = Task::with(['user', 'taskTime', 'taskSalaryDetail', 'order', 'childs' => function ($q) {
            $q->with(['order' => function ($q) {
                $q->with(['labels' => function ($q) {
                    $q->where('label_group_id', LabelGroup::PRODUCTION_LABEL_GROUP_ID)->orWhereIn('labels.id',
                        [Label::BLUE_BATTERY_LABEL_ID, Label::ORANGE_BATTERY_LABEL_ID, Label::ORDER_ITEMS_REDEEMED_LABEL]);
                }]);
            }]);
        }])->find($id);

        foreach ($task->childs as $child) {
            $child->order->similar = OrdersHelper::findSimilarOrders($child->order);
        }
        if (empty($task)) {
            abort(404);
        }

        return response()->json($task);
    }

    /**
     * Get Tasks with children
     */
    public function getTasksWithChildren(): JsonResponse
    {
        $tasks = $this->tasksRepository->getTasksWithChildren();
        
        return response()->json($tasks);
    }

    /**
     * Get Children
     * @param $id
     */
    public function getChildren($id): JsonResponse
    {
        $task = $this->tasksRepository->getChildren($id);
        
        return response()->json($task->childs);
    }

    /**
     * @param BreakDownRequest $request
     */
    public function breakDownTask(BreakDownRequest $request): RedirectResponse
    {
        try {
            $request->validated();
            $data = $request->all();

            $taskId = $data['task'];
            if($taskId != null){
                $tasks = Task::with(['parent'])->where('parent_id', $taskId)->get();
                foreach($tasks as $task){

                    $parent = $task->parent->first();
                    $name = explode(',',$parent->name);
                    if (($key = array_search($task->order_id, $name)) !== false) {
                        unset($name[$key]);
                    }
                    $parent->update([
                        'name' => join(', ',$name)
                    ]);

                    $task->update([
                        'user_id' => 36,
                        'parent_id' => null,
                        'status' => Task::WAITING_FOR_ACCEPT
                    ]);

                    if($task->order_id > 0){
                        $preventionArray = [];
                        RemoveLabelService::removeLabels(Order::find($task->order_id),[Label::GREEN_HAMMER_ID],$preventionArray,[],Auth::user()->id);
                        $prev = [];
                        AddLabelService::addLabels(Order::find($task->order_id), [Label::RED_HAMMER_ID,Label::CONSULTANT_MARK], $prev, [], Auth::user()->id);
                    }
                }
            }
        } catch (Exception $e) {

            Log::error('Nie udało się rozbic zadań', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return redirect()->back()->with([
                'message' => __('tasks.messages.update_error'),
                'alert-type' => 'error'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('tasks.messages.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param AddingTaskToPlanerRequest $request
     */
    public function addingTaskToPlanner(AddingTaskToPlanerRequest $request): JsonResponse
    {
        $request->validated();
        $data = $request->all();

        $order = Order::with(['customer','labels'])->find($data['order_id']);

        $task = $this->taskService->checkTaskLogin(
            $order->customer->login,
            $order->getDeliveryAddress(),
            $data['delivery_warehouse']
        );
        if($task==0){
            $id = $this->taskService->addTaskToPlanner($order,$data['delivery_warehouse']);
        }else{
            if($task==-1){
                $array = [
                    'status' => 'ERROR',
                    'id' => $order->id,
                    'delivery_warehouse' => $data['delivery_warehouse'],
                    'message' => 'Wstrzymać dodanianie zadania?'
                ];
                return response()->json($array);
            }else {
                $id = $this->taskService->addTaskToGroupPlanner($order,$task,$data['delivery_warehouse']);
            }
        }

        $array = [
            'status' => 'ADDED_TASK',
            'id' => $id,
            'message' => 'Dodano zadanie id: '.$id
        ];
        return response()->json($array);
    }

    /**
     * @param AddingTaskToPlanerRequest $request
     */
    public function saveTaskToPlanner(AddingTaskToPlanerRequest $request): RedirectResponse
    {
        $request->validated();
        $data = $request->all();
        $order = Order::with(['customer','labels'])->find($data['order_id']);
        $id = $this->taskService->addTaskToPlanner($order,$data['delivery_warehouse']);
        $array = [
            'status' => 'ADDED_TASK',
            'id' => $id,
            'message' => 'Dodano zadanie id: '.$id
        ];

        return redirect()->route('planning.tasks.index')->with([
            'message' => __('tasks.messages.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param int $taskId
     */
    public function checkQuantityInStock($taskId): JsonResponse
    {
        try {
            if($taskId != null){
                $tasks = Task::with(['parent'])->where('id',$taskId)->orWhere('parent_id', $taskId)->get();

                $lists = $this->getQuantityInStockList($tasks);
                $response = [
                    'status' => 200,
                    'data' => $lists
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'status' => 500,
                'error' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }

    /**
     * @param Task $tasks
     */
    public function getQuantityInStockList($tasks): array
    {
        $stockLists = []; 
        foreach($tasks as $task){
            if($task->order_id!==null){
                $orderItems = OrderItem::with(['product'])->where('order_id',$task->order_id)->get();
                foreach($orderItems as $orderItem){
                    if(
                        $orderItem->quantity > $orderItem->product->getPositions()->first()->position_quantity ||
                        $orderItem->quantity > $orderItem->product->stock->quantity
                    ){
                        $stockLists[$orderItem->order_id][] = [
                            'product_stock_id' => $orderItem->product->id,
                            'product_name' => $orderItem->product->name,
                            'product_symbol' => $orderItem->product->symbol,
                            'quantity' => $orderItem->quantity,
                            'stock_quantity' => $orderItem->product->stock->quantity,
                            'first_position_quantity' => $orderItem->product->getPositions()->first()->position_quantity
                        ];
                    }
                }
            }
        }
        return $stockLists;
    }
}
