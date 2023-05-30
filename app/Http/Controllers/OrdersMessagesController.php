<?php

namespace App\Http\Controllers;

use App\ChatStatus;
use App\Http\Requests\changeChatVisibilityRequest;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderMessageCreateRequest;
use App\Http\Requests\OrderMessageUpdateRequest;
use App\Http\Requests\OrderTaskUpdateRequest;
use App\Mail\MessageSent;
use App\Mail\SelfMessageSent;
use App\Repositories\EmployeeRepository;
use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Mailer;
use Yajra\DataTables\Facades\DataTables;


/**
 * Class OrderTasksController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersMessagesController extends Controller
{
    /**
     * @var OrderPaymentRepository
     */
    protected $repository;

    protected $employeeRepository;

    protected $orderRepository;

    /**
     * OrderController constructor.
     *
     * @param OrderRepository $repository
     */
    public function __construct(
        OrderRepository        $orderRepository,
        OrderMessageRepository $repository,
        EmployeeRepository     $employeeRepository
    )
    {
        $this->repository = $repository;
        $this->employeeRepository = $employeeRepository;
        $this->orderRepository = $orderRepository;
    }


    /**
     * @return Factory|View
     */
    public function create($id)
    {
        $employees = $this->employeeRepository->all();
        return view('orderMessages.create', compact('id', 'employees'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $orderMessage = $this->repository->find($id);
        $employees = $this->employeeRepository->all();
        return view('orderMessages.edit', compact('orderMessage', 'id', 'employees'));
    }

    /**
     * @param OrderTaskUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(OrderMessageUpdateRequest $request, $id)
    {
        $orderMessage = $this->repository->find($id);

        if (empty($orderMessage)) {
            abort(404);
        }

        $orderId = $orderMessage->order_id;

        $this->repository->update([
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'user_id' => Auth::user()->id,
        ], $id);

        return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
            'message' => __('order_messages.message.update'),
            'alert-type' => 'success',
        ]);
    }

    public function store(OrderMessageCreateRequest $request)
    {
        $order_id = $request->input('order_id');

        if (empty(Auth::user()->userEmailData)) {
            return redirect()->route('orders.edit', ['order_id' => $order_id])->with([
                'message' => __('order_messages.message.email_failure'),
                'alert-type' => 'error',
            ])->withInput(['tab' => 'orderMessages']);
        }

        $message = $this->repository->create([
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'status' => 'OPEN',
            'type' => $request->input('type'),
            'user_id' => Auth::user()->id,
            'order_id' => $order_id,
        ]);

        if ($request->file('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();

            $request->file('attachment')->storeAs("public/attachments/{$message->order_id}/{$message->id}", $fileName);

            $message->file = $fileName;
            $message->save();
        }

        $order = $this->orderRepository->find($order_id);

        $frontId = $order->id_from_front_db;

        switch ($request->input('type')) {
            case 'GENERAL':
                $type = 'uwagi_ogolne';
                $typeText = 'UWAGI OGÓLNE';
                break;
            case 'SHIPPING':
                $type = 'uwagi_spedycja';
                $typeText = 'UWAGI DO SPEDYCJI';
                break;
            case 'WAREHOUSE':
                $type = 'uwagi_magazyn';
                $typeText = 'UWAGI DO MAGAZYNU';
                break;
        }

        $date = date("Y-m-d H:i:s");
        $clientEmail = $order->customer->login;

        if ($order->customer->id == 4128) {
            $mail = $order->warehouse->firm->email;

            Mailer::create()
                ->to($mail)
                ->send(new SelfMessageSent($date, $type, $typeText, $order->warehouse->id, $order_id));
        } else {
            if ($request->input('type') == 'GENERAL' || $request->input('type') == 'SHIPPING' || $request->input('type') == 'WAREHOUSE') {
                if (!strpos($clientEmail, 'allegromail.pl')) {
                    Mailer::create()
                        ->to($clientEmail)
                        ->send(new MessageSent($date, $type, $typeText, $frontId, $order_id));
                }
            }
        }


        return redirect()->route('orders.edit', ['order_id' => $order_id])->with([
            'message' => __('order_messages.message.store'),
            'alert-type' => 'success',
        ])->withInput(['tab' => 'orderMessages']);
    }

    public function storeWarehouseMessage(OrderMessageCreateRequest $request)
    {
        $order_id = $request->input('order_id');

        $message = $this->repository->create([
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'status' => 'OPEN',
            'type' => $request->input('type'),
            'order_id' => $order_id,
        ]);

        if ($request->file('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();

            $request->file('attachment')->storeAs("public/attachments/{$message->order_id}/{$message->id}", $fileName);

            $message->file = $fileName;
            $message->save();
        }

        return view('orderMessages.communication_success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (empty($deleted)) {
            return redirect()->back()->with([
                'message' => __('orders.message.not_delete'),
                'alert-type' => 'error',
            ])->withInput(['tab' => 'orderMessages']);
        }

        return redirect()->back()->with([
            'message' => __('orders.message.delete'),
            'alert-type' => 'success',
        ])->withInput(['tab' => 'orderMessages']);
    }

    /**
     * @return JsonResponse
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

    public function communication($warehouseId, $orderId)
    {
        $order = $this->orderRepository->find($orderId);
        $messages = $this->repository->orderBy('type')->findWhere(["order_id" => $orderId]);

        return view('orderMessages.communication', compact('order', 'messages', 'warehouseId'));
    }

    public function userCommunication($orderId)
    {
        $order = $this->orderRepository->find($orderId);
        $messages = $this->repository->orderBy('type')->findWhere(["order_id" => $orderId]);

        return view('orderMessages.user.communication', compact('order', 'messages'));
    }

    /**
     * @return mixed
     */
    public function changeChatVisibility(changeChatVisibilityRequest $request): RedirectResponse
    {
        ChatStatus::first()->update([
            'is_active' => !ChatStatus::first()->is_active,
            'message' => $request->validated('message-value'),
        ]);

        return redirect()->back();
    }
}
