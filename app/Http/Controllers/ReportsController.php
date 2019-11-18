<?php

namespace App\Http\Controllers;

use App\Entities\ReportProperty;
use App\Repositories\ReportDailyRepository;
use App\Repositories\ReportPropertyRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Repositories\WarehouseRepository;
use Carbon\Carbon;
use App\Http\Requests\ReportCreateRequest;
use App\Http\Requests\ReportUpdateRequest;
use App\Repositories\ReportRepository;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Class ReportsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ReportsController extends Controller
{
    /**
     * @var ReportRepository
     */
    protected $repository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var WarehouseRepository
     */
    protected $warehouseRepository;


    /**
     * @var ReportPropertyRepository
     */
    protected $reportPropertyRepository;

    /**
     * @var ReportDailyRepository
     */
    protected $reportDailyRepository;

    /**
     * ReportsController constructor.
     * @param ReportRepository $repository
     * @param UserRepository $userRepository
     * @param TaskRepository $taskRepository
     * @param WarehouseRepository $warehouseRepository
     * @param ReportPropertyRepository $reportPropertyRepository
     * @param ReportDailyRepository $reportDailyRepository
     */
    public function __construct(
        ReportRepository $repository,
        UserRepository $userRepository,
        TaskRepository $taskRepository,
        WarehouseRepository $warehouseRepository,
        ReportPropertyRepository $reportPropertyRepository,
        ReportDailyRepository $reportDailyRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->repository = $repository;
        $this->taskRepository = $taskRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->reportPropertyRepository = $reportPropertyRepository;
        $this->reportDailyRepository = $reportDailyRepository;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index()
    {
        $reports = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $reports,
            ]);
        }

        return view('planning.report.index', compact('reports'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $users = $this->userRepository->all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.report.create', compact('users', 'warehouses'));
    }

    /**
     * @param ReportCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ReportCreateRequest $request)
    {
        $dataToStore = $request->all();
        $report = $this->repository->create($dataToStore);
        $allSum = 0;
        $allTime = 0;
        foreach ($dataToStore['users_id'] as $userId) {
            $report->users()->attach($userId);
            if ($userId !== null) {
                $tasks = $this->taskRepository->with([
                    'taskTime',
                    'taskSalaryDetail'
                ])->whereHas('taskTime', function ($query) use ($dataToStore, $userId) {
                    $from = new Carbon($dataToStore['from'] . ' ' . '00:00:00');
                    $to = new Carbon($dataToStore['to'] . ' ' . '23:59:59');
                    $query->whereBetween('date_start', [$from->toDateTimeString(), $to->toDateTimeString()])
                        ->orWhereBetween('date_end', [$from->toDateTimeString(), $to->toDateTimeString()]);
                })->findWhere([['user_id', '=', $userId], ['name', '!=', 'Przerwa']])->all();
            }
            if (count($tasks) == 0) {
                continue;
            }
            foreach ($tasks as $task) {
                $sum = 0;
                $time = 0;
                $dateStart = new Carbon($task->taskTime->date_start);
                $reportDaily = $this->reportDailyRepository->findWhere([['date', '=', $dateStart->toDateString()], ['report_id', '=', $report->id], ['user_id', '=', $userId]])->first();
                if ($task->order_id !== null) {
                    if ($task->taskSalaryDetail !== null) {
                        $user = $this->userRepository->find($userId);
                        if ($user->role_id === 5) {
                            $sum += (float)$task->taskSalaryDetail->warehouse_value;
                            $report->properties()->create([
                                'task_id' => $task->id,
                                'time_work' => 'Nie dotyczy',
                                'price' => (float)$task->taskSalaryDetail->warehouse_value,
                                'user_id' => $userId
                            ]);
                        } else {
                            $sum += (float)$task->taskSalaryDetail->consultant_value;
                            $report->properties()->create([
                                'task_id' => $task->id,
                                'time_work' => 'Nie dotyczy',
                                'price' => (float)$task->taskSalaryDetail->consultant_value,
                                'user_id' => $userId
                            ]);
                        }
                    }
                } else {
                    $start = strtotime($task->taskTime->date_start);
                    $end = strtotime($task->taskTime->date_end);
                    $time = ($end - $start) / 3600;
                    $allTime += $time;
                    $sum += $task->user->rate_hour * $time;
                    $allSum += $task->user->rate_hour * $time;
                    $report->properties()->create([
                        'task_id' => $task->id,
                        'time_work' => $time,
                        'price' => (float)$task->user->rate_hour * $time,
                        'user_id' => $userId
                    ]);
                }
                if (!$reportDaily) {
                    $report->daily()->create([
                        'user_id' => $userId,
                        'date' => $dateStart->toDateString(),
                        'price' => $sum
                    ]);
                } else {
                    $reportDaily->update(['price' => $reportDaily->price + $sum]);
                }
                unset($time);
            }
        }
        $dataToStore['value'] = $allSum;
        $report->update($dataToStore);
        if ($report) {
            return redirect()->route('planning.reports.index')->with([
                'message' => __('reports.message.store'),
                'alert-type' => 'success'
            ]);
        };

        return redirect()->route('planning.reports.index')->with([
            'message' => __('reports.message.error.store'),
            'alert-type' => 'error'
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $report = $this->repository->find($id);

        if (empty($report)) {
            abort(404);
        }

        $report->delete($report->id);

        return redirect()->route('planning.reports.index')->with([
            'message' => __('reports.message.delete'),
            'alert-type' => 'info'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable()
    {
        $collection = $this->prepareCollection();

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection()
    {
        $collection = $this->repository->all();

        return $collection;
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generateReport($id)
    {
        $report = $this->repository->find($id);
        if (!$report) {
            abort(404);
        }
        return view('planning.report.show', compact(['report']));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generatePdfReport($id)
    {
        ini_set('max_execution_time', -1);
        $report = $this->repository->find($id);
        if ($report) {

            $pdf = PDF::loadView('pdf.report', [
                'report' => $report,
            ])->setPaper('a4');
            return $pdf->download('report.pdf');
        } else {
            return redirect()->back()->with([
                'message' => __('reports.message.error.generateReport'),
                'alert-type' => 'error'
            ]);
        }
    }
}
