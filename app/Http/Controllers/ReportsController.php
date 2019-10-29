<?php

namespace App\Http\Controllers;

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

    protected $taskRepository;

    protected $warehouseRepository;

    public function __construct(
        ReportRepository $repository,
        UserRepository $userRepository,
        TaskRepository $taskRepository,
        WarehouseRepository $warehouseRepository
    ) {
        $this->userRepository = $userRepository;
        $this->repository = $repository;
        $this->taskRepository = $taskRepository;
        $this->warehouseRepository = $warehouseRepository;
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

        $report = $this->repository->create($request->all());
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $report = $this->repository->find($id);
        $users = $this->userRepository->all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.report.edit', compact('report', 'users', 'warehouses'));
    }


    /**
     * @param ReportUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ReportUpdateRequest $request, $id)
    {
        $report = $this->repository->find($id);

        if ($report->update($request->all())) {
            return redirect()->route('planning.reports.index')->with([
                'message' => __('reports.message.update'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->back()->with([
            'message' => __('reports.message.error.update'),
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

    public function generatePdfReport($id)
    {
        $report = $this->repository->find($id);
        if ($report) {
            if($report->user_id !== null) {
                $tasks = $this->taskRepository->with([
                    'taskTime' => function ($query) use ($report) {
                        $from = new Carbon($report->from);
                        $to = new Carbon($report->to);
                        $query->whereBetween('date_start', [$from->toDateTimeString(), $to->toDateTimeString()])
                            ->orWhereBetween('date_end', [$from->toDateTimeString(), $to->toDateTimeString()]);
                    },
                    'taskSalaryDetail'
                ])->findWhere([['created_by', '=', $report->user_id]])->all();
            } else {
                if($report->user_id === null) {
                    $tasks = $this->taskRepository->with([
                        'taskTime',
                        'taskSalaryDetail'
                    ])->findWhere([['warehouse_id', '=', $report->warehouse_id]])->all();
                }
            }
            $sum = 0;
            if(count($tasks) == 0){
                return redirect()->back()->with([
                    'message' => __('reports.message.error.generateReport'),
                    'alert-type' => 'error'
                ]);
            }

            foreach ($tasks as $task) {
                if ($task->order_id !== null) {
                    if ($task->taskSalaryDetail !== null) {
                        if($report->user_id === null) {
                            $sum += (float)$task->taskSalaryDetail->warehouse_value;
                        } else {
                            $sum += (float)$task->taskSalaryDetail->consultant_value;
                        }
                    }
                }
            }
            $pdf = PDF::loadView('pdf.report', [
                'report' => $report,
                'date' => Carbon::today()->toDateString(),
                'sum' => $sum,
                'tasks' => $tasks
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
