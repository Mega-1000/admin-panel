<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\TaskTimeRepository;
use App\Repositories\UserRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Repositories\TaskRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class ArchiveController extends Controller
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
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->taskTimeRepository = $taskTimeRepository;
    }


    public function index()
    {
        return view('planning.archive.index');
    }

    public function view($id)
    {
        $task = $this->repository->find($id);
        $users = $this->userRepository->findWhere([['warehouse_id', '!=', null]]);
        $orders = $this->orderRepository->all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');

        return view('planning.archive.show', compact('task', 'warehouses', 'users', 'orders'));
    }


    public function datatable()
    {
        $collection = $this->prepareCollection();

        return DataTables::collection($collection)->make(true);
    }

    public function prepareCollection()
    {
        $collection = $this->repository->with(['user', 'warehouse', 'taskTime'])->whereHas('taskTime', function($query){
            $date = new Carbon();
            $query->where('date_start', '<=', $date->toDateString());
        })->all();

        return $collection;
    }
}
