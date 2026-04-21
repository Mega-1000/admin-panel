<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderPaymentCreateRequest;
use App\Http\Requests\OrderPaymentUpdateRequest;
use App\Http\Requests\OrderTaskCreateRequest;
use App\Http\Requests\OrderTaskUpdateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Repositories\CustomerRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\LabelRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderTaskRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\FirmAddressRepository;
use App\Repositories\FirmRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


/**
 * Class OrderTasksController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersTasksController extends Controller
{
    /**
     * @var OrderPaymentRepository
     */
    protected $repository;

    protected $employeeRepository;
   
    /**
     * OrderController constructor.
     *
     * @param OrderRepository $repository
     */
    public function __construct(OrderTaskRepository $repository, EmployeeRepository $employeeRepository)
    {
        $this->repository = $repository;
        $this->employeeRepository = $employeeRepository;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($id)
    {
        $employees = $this->employeeRepository->all();
        return view('orderTasks.create', compact('id', 'employees'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderTask = $this->repository->find($id);
        $employees = $this->employeeRepository->all();
        $date = new \DateTime($orderTask->show_label_at);
        $date = $date->format('d-m-Y');
        return view('orderTasks.edit', compact('orderTask', 'id', 'employees', 'date'));
    }

    /**
     * @param OrderTaskUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OrderTaskUpdateRequest $request, $id)
    {
        $orderTask = $this->repository->find($id);

        if (empty($orderTask)) {
            abort(404);
        }

        $orderId = $orderTask->order_id;

        $this->repository->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'show_label_at' => $request->input('show_label_at'),
            'employee_id' => $request->input('employee_id'),
        ], $id);

        return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
            'message' => __('order_tasks.message.update'),
            'alert-type' => 'success'
        ]);
    }

    public function store(OrderTaskCreateRequest $request)
    {

        $order_id = $request->input('order_id');

        $this->repository->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'show_label_at' => $request->input('show_label_at'),
            'order_id' => $order_id,
            'employee_id' => $request->input('employee_id'),
        ]);

        return redirect()->route('orders.edit', ['order_id' => $order_id])->with([
            'message' => __('order_tasks.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (empty($deleted)) {
            return redirect()->back()->with([
                'message' => __('orders.message.not_delete'),
                'alert-type' => 'error'
            ])->withInput(['tab' => 'orderTasks']);
        }

        return redirect()->back()->with([
            'message' => __('orders.message.delete'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'orderTasks']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable($id)
    {
        $collection = $this->prepareCollection($id);
        return DataTables::collection($collection)->make(true);
    }


    /**
     * @return mixed
     */
    public function prepareCollection($id)
    {
        $collection = $this->repository->findByField('order_id', $id);

        return $collection;
    }
}
