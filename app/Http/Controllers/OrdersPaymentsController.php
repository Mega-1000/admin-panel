<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Payment;
use App\Http\Requests\OrderPaymentCreateRequest;
use App\Http\Requests\OrderPaymentUpdateRequest;
use App\Jobs\AddLabelJob;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\RemoveLabelJob;
use App\Mail\ConfirmData;
use App\Mail\WarehousePaymentAccept;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\OrderPackageRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Models\Role;
use Yajra\DataTables\Facades\DataTables;


/**
 * Class OrderPaymentsController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersPaymentsController extends Controller
{
    /**
     * @var OrderPaymentRepository
     */
    protected $repository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var OrderPackageRepository
     */
    protected $orderPackageRepository;

    /**
     * OrderPaymentController constructor.
     *
     * @param OrderPaymentRepository $repository
     * @param OrderRepository $orderRepository
     * @param PaymentRepository $paymentRepository
     * @param CustomerRepository $customerRepository
     * @param OrderPackageRepository $orderPackageRepository
     */
    public function __construct(OrderPaymentRepository $repository, OrderRepository $orderRepository, PaymentRepository $paymentRepository, CustomerRepository $customerRepository, OrderPackageRepository $orderPackageRepository)
    {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->customerRepository = $customerRepository;
        $this->orderPackageRepository = $orderPackageRepository;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @var integer $id Order ID
     */
    public function create($id)
    {
        return view('orderPayments.create', compact('id'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @var integer $id Order ID
     */
    public function createMaster($id)
    {
        $order = $this->orderRepository->find($id);
        return view('orderPayments.master.create', compact('order'));
    }

    public function createMasterWithoutOrder($id)
    {
        $customerId = $id;
        $order = null;
        return view('orderPayments.master.create', compact('customerId', 'order'));
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
        $orderPayment = $this->repository->find($id);
        $customerOrders = $orderPayment->order->customer->orders;
        return view('orderPayments.edit', compact('orderPayment', 'id', 'customerOrders'));
    }

    /**
     * @param OrderPaymentUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OrderPaymentUpdateRequest $request, $id)
    {
        $orderPayment = $this->repository->find($id);
        $oldOrderId = $orderPayment->order_id;

        if (empty($orderPayment)) {
            abort(404);
        }

        $order_id = $request->input('order_id');
        $promise = $request->input('promise');
        if ($promise == 'yes') {
            $promise = '1';
        } else {
            $promise = '';
        }

        if ($orderPayment->promise == '1' && $promise == '') {
            dispatch_now(new AddLabelJob($orderPayment->order_id, [5]));
        }

        $payment = $this->repository->update([
            'amount' => $request->input('amount'),
            'notices' => $request->input('notices'),
            'promise' => $promise,
            'promise_date' => $request->input('promise_date'),
            'order_id' => $order_id,
            'created_at' => $request->input('created_at')
        ], $id);

        OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);

        return redirect()->route('orders.edit', ['order_id' => $oldOrderId])->with([
            'message' => __('order_payments.message.update'),
            'alert-type' => 'success'
        ]);
    }

    public function store(OrderPaymentCreateRequest $request)
    {
        $order_id = $request->input('order_id');
        $chooseOrder = $request->input('chooseOrder');
        $masterPaymentId = $request->input('masterPaymentId');

        if (!empty($chooseOrder)) {
            $orderId = $chooseOrder;
        } else {
            $orderId = $order_id;
        }

        $promise = $request->input('promise');

        if ($promise == 'on') {
            $promise = '1';
        } else {
            $promise = '';
        }

        $isWarehousePayment = $request->input('warehousePayment');

        $type = $request->input('payment-type');

        OrdersPaymentsController::payOrder($orderId, $request->input('amount'),
            $masterPaymentId, $promise,
            $chooseOrder, $request->input('promise_date'),
            $type, $isWarehousePayment
            );

        return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
            'message' => __('order_payments.message.store'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'orderPayments']);
    }

    public function storeFromImport($orderId, $amount, $date)
    {
        if($date == null) {
            $date = \Carbon\Carbon::now();
        }
        $globalAmount = $amount;
        if(strlen($orderId) > 10) {
            $orderPackage = $this->orderPackageRepository->findWhere(['letter_number' => $orderId])->first();
            if(!empty($orderPackage)) {
                $orderId = $orderPackage->order_id;
                $order = $this->orderRepository->findWhere(['id' => $orderId])->first();
            } else {
                return false;
            }
        }
        elseif (strlen($orderId) > 4 && strlen($orderId) < 10) {
            $order = $this->orderRepository->findWhere(['id_from_front_db' => $orderId])->first();
            $orderId = $order->id;
        } elseif (strlen($orderId) < 5) {
            $order = $this->orderRepository->find($orderId);
        }
        /////// połączone
        $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        if($connectedOrders->count() > 0) {
            $hasGroupPromisePayment = false;
            $hasGroupBookedPayment = false;

            foreach($connectedOrders as $connectedOrder) {
                if($connectedOrder->hasPromisePayments() > 0) {
                    $hasGroupPromisePayment = true;
                }
                if($connectedOrder->hasBookedPayments() > 0) {
                    $hasGroupBookedPayment = true;
                }
            }
            if($order->hasPromisePayments() > 0) {
                $hasGroupPromisePayment = true;
            }
            if($order->hasBookedPayments() > 0) {
                $hasGroupBookedPayment = true;
            }
            if($hasGroupPromisePayment == true) {
           $orderGroupPromisePaymentSum = 0;
                foreach ($connectedOrders as $connectedOrder) {
                    $orderGroupPromisePaymentSum += $connectedOrder->promisePaymentsSum();
                }
                $orderGroupPromisePaymentSum += $order->promisePaymentsSum();
                if ((float)$amount == (float)$orderGroupPromisePaymentSum) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if ($connectedOrder->hasPromisePayments() > 0) {
                            foreach ($connectedOrder->promisePayments() as $promisePayment) {
                                if (empty($this->repository->findWhere([
                                    'amount' => $promisePayment->amount,
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                ])->first())) {

                                    if(empty($this->paymentRepository->findWhere([
                                        'amount' => $amount,
                                        'customer_id' => $order->customer_id,
                                    ])->first())) {
                                        $globalPayment = $this->paymentRepository->create([
                                            'amount' => $amount,
                                            'amount_left' => $amount,
                                            'customer_id' => $order->customer_id,
                                            'created_at' => $date
                                        ]);
                                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                        $payment = $this->repository->create([
                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                            'order_id' => $connectedOrder->id,
                                            'master_payment_id' => $globalPayment->id,
                                            'promise' => '',
                                        ]);
                                    } else {
                                        $globalPayment = $this->paymentRepository->findWhere([
                                            'amount' => $amount,
                                            'customer_id' => $order->customer_id,
                                        ])->first();
                                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                        $payment = $this->repository->create([
                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                            'order_id' => $connectedOrder->id,
                                            'master_payment_id' => $globalPayment->id,
                                            'promise' => '',
                                        ]);
                                    }

                                    OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);
                                    dispatch_now(new AddLabelJob($connectedOrder->id, [130]));
                                    if ($payment != null && $order->status_id != 5) {
                                        $this->orderRepository->update([
                                            'status_id' => 5,
                                        ], $orderId);
                                    }
                                }
                            }
                        } else {
                            foreach ($order->promisePayments() as $promisePayment) {
                                if (empty($this->repository->findWhere([
                                    'amount' => $promisePayment->amount,
                                    'order_id' => $order->id,
                                    'promise' => '',
                                ])->first())) {
                                    if(empty($this->paymentRepository->findWhere([
                                        'amount' => $amount,
                                        'customer_id' => $order->customer_id,
                                    ])->first())) {
                                        $globalPayment = $this->paymentRepository->create([
                                            'amount' => $amount,
                                            'amount_left' => $amount,
                                            'customer_id' => $order->customer_id,
                                            'created_at' => $date
                                        ]);
					if($amount > $order->getSumOfGrossValues()) {
                                            $globalPayment->update(['amount_left' => $globalPayment->amount - $order->getSumOfGrossValues()]);

                                            $payment = $this->repository->create([
                                                'amount' => $order->getSumOfGrossValues(),
                                                'order_id' => $order->id,
                                                'master_payment_id' => $globalPayment->id,
                                                'promise' => '',
                                            ]);
					if($globalPayment->amount_left > 0) {
                                            foreach ($connectedOrders as $connectedOrder) {
                                                    if($globalPayment->amount_left > $connectedOrder->getSumOfGrossValues()) {
                                                        $payment = $this->repository->create([
                                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                                            'order_id' => $connectedOrder->id,
                                                            'master_payment_id' => $globalPayment->id,
                                                            'promise' => '',
                                                        ]);
                                                        $globalPayment->update(['amount_left' => $globalPayment->amount_left - $connectedOrder->getSumOfGrossValues()]);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $globalPayment = $this->paymentRepository->findWhere([
                                            'amount' => $amount,
                                            'customer_id' => $order->customer_id,
                                        ])->first();
                                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                        $payment = $this->repository->create([
                                            'amount' => $order->getSumOfGrossValues(),
                                            'order_id' => $order->id,
                                            'master_payment_id' => $globalPayment->id,
                                            'promise' => '',
                                        ]);
                                    }

                                    OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);
                                    dispatch_now(new AddLabelJob($order->id, [130]));
                                    if ($payment != null && $order->status_id != 5) {
                                        $this->orderRepository->update([
                                            'status_id' => 5,
                                        ], $orderId);
                                    }
                                }
                            }
                        }
                    }
                }
                return ['orderId' => $orderId, 'amount' => $amount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];
            }

            if($hasGroupPromisePayment == true) {
                $orderGroupPromisePaymentSum = 0;
                foreach ($connectedOrders as $connectedOrder) {
                    $orderGroupPromisePaymentSum += $connectedOrder->promisePaymentsSum();
                }
                $orderGroupPromisePaymentSum += $order->promisePaymentsSum();
                if ((float)$amount < (float)$orderGroupPromisePaymentSum) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if ($connectedOrder->hasPromisePayments() > 0) {
                            dispatch_now(new AddLabelJob($connectedOrder->id, [128]));
                        }
                    }
                }
            }

            if($hasGroupPromisePayment == true) {
                $orderGroupPromisePaymentSum = 0;
                foreach ($connectedOrders as $connectedOrder) {
                    $orderGroupPromisePaymentSum += $connectedOrder->promisePaymentsSum();
                }
                $orderGroupPromisePaymentSum += $order->promisePaymentsSum();
                if ((float)$amount > (float)$orderGroupPromisePaymentSum) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if ($connectedOrder->hasPromisePayments() > 0) {
                            foreach ($connectedOrder->promisePayments() as $promisePayment) {
                                if (empty($this->repository->findWhere([
                                    'amount' => $promisePayment->amount,
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                ])->first())) {

                                    if(empty($this->paymentRepository->findWhere([
                                        'amount' => $amount,
                                        'customer_id' => $order->customer_id,
                                    ])->first())) {
                                        $globalPayment = $this->paymentRepository->create([
                                            'amount' => $amount,
                                            'amount_left' => $amount,
                                            'customer_id' => $order->customer_id,
                                            'created_at' => $date
                                        ]);
                                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                        $payment = $this->repository->create([
                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                            'order_id' => $connectedOrder->id,
                                            'master_payment_id' => $globalPayment->id,
                                            'promise' => '',
                                        ]);
                                    } else {
                                        $globalPayment = $this->paymentRepository->findWhere([
                                            'amount' => $amount,
                                            'customer_id' => $order->customer_id,
                                        ])->first();
                                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                        $payment = $this->repository->create([
                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                            'order_id' => $connectedOrder->id,
                                            'master_payment_id' => $globalPayment->id,
                                            'promise' => '',
                                        ]);
                                    }

                                    OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);

                                    if ($payment != null && $order->status_id != 5) {
                                        $this->orderRepository->update([
                                            'status_id' => 5,
                                        ], $orderId);
                                    }
                                }
                            }
                        }
                    }
                    if(empty($this->paymentRepository->findWhere([
                        'amount' => $amount,
                        'customer_id' => $order->customer_id,
                    ])->first())) {
                        $globalPayment = $this->paymentRepository->create([
                            'amount' => $amount,
                            'amount_left' => $amount,
                            'customer_id' => $order->customer_id,
                            'created_at' => $date
                        ]);
                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                        $payment = $this->repository->create([
                            'amount' => $amount,
                            'order_id' => $orderId,
                            'master_payment_id' => $globalPayment->id,
                            'promise' => '',
                        ]);
                    } else {
                        $globalPayment = $this->paymentRepository->findWhere([
                            'amount' => $amount,
                            'customer_id' => $order->customer_id,
                        ])->first();
                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                        $payment = $this->repository->create([
                            'amount' => $amount,
                            'order_id' => $orderId,
                            'master_payment_id' => $globalPayment->id,
                            'promise' => '',
                        ]);
                    }
                }
                return ['orderId' => $orderId, 'amount' => $amount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];
            }

            if($hasGroupBookedPayment == true) {
                $ordersSum = 0;
                foreach($connectedOrders as $connectedOrder) {
                    $ordersSum += $connectedOrder->getSumOfGrossValues();
                }
                $ordersSum += $order->getSumOfGrossValues();
                if((float)$ordersSum == (float)$amount) {
                    foreach($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if(empty($this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first())) {
                                $globalPayment = $this->paymentRepository->create([
                                    'amount' => $amount,
                                    'amount_left' => $amount,
                                    'customer_id' => $order->customer_id,
                                    'created_at' => $date
                                ]);
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'master_payment_id' => $globalPayment->id,
                                    'promise' => '',
                                ]);
                            } else {
                                $globalPayment = $this->paymentRepository->findWhere([
                                    'amount' => $amount,
                                    'customer_id' => $order->customer_id,
                                ])->first();
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'master_payment_id' => $globalPayment->id,
                                    'promise' => '',
                                ]);
                            }

                            dispatch_now(new AddLabelJob($connectedOrder->id, [40]));
                        }
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if(empty($this->paymentRepository->findWhere([
                            'amount' => $amount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $amount,
                                'amount_left' => $amount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date
                            ]);
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'master_payment_id' => $globalPayment->id,
                                'promise' => '',
                            ]);
                        } else {
                            $globalPayment = $this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first();
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'master_payment_id' => $globalPayment->id,
                                'promise' => '',
                            ]);
                        }

                        dispatch_now(new RemoveLabelJob($order->id, [40]));
                        dispatch_now(new DispatchLabelEventByNameJob($order->id, "payment-received"));
                    }
                    return ['orderId' => $orderId, 'amount' => $amount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }

                if((float)$ordersSum > (float)$amount) {
                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if(empty($this->paymentRepository->findWhere([
                            'amount' => $globalAmount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $globalAmount,
                                'amount_left' => $globalAmount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date
                            ]);
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $order->toPay()]);
                            if($order->toPay() < $amount) {
                                $amount = $amount - $order->toPay();
                                $payment = $this->repository->create([
                                    'amount' => $order->toPay(),
                                    'order_id' => $orderId,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id
                                ]);
                            } else {
                                $payment = $this->repository->create([
                                    'amount' => $amount,
                                    'order_id' => $orderId,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id
                                ]);
                                $amount = 0;
                            }
                        }

                        dispatch_now(new RemoveLabelJob($order->id, [40]));
                        dispatch_now(new DispatchLabelEventByNameJob($order->id, "payment-received"));
                    }
                    foreach($connectedOrders as $connectedOrder) {
                        if($amount >= $connectedOrder->toPay()) {
                            if (empty($this->repository->findWhere([
                                'amount' => $connectedOrder->getSumOfGrossValues(),
                                'order_id' => $connectedOrder->id,
                                'promise' => '',
                            ])->first())) {
                                if(empty($this->paymentRepository->findWhere([
                                    'amount' => $amount,
                                    'customer_id' => $order->customer_id,
                                ])->first())) {
                                    $globalPayment = $this->paymentRepository->create([
                                        'amount' => $amount,
                                        'amount_left' => $amount,
                                        'customer_id' => $order->customer_id,
                                        'created_at' => $date
                                    ]);
                                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                    $payment = $this->repository->create([
                                        'amount' => $connectedOrder->getSumOfGrossValues(),
                                        'order_id' => $connectedOrder->id,
                                        'master_payment_id' => $globalPayment->id,
                                        'promise' => '',
                                    ]);
                                } else {
                                    $globalPayment = $this->paymentRepository->findWhere([
                                        'amount' => $amount,
                                        'customer_id' => $order->customer_id,
                                    ])->first();
                                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                    $payment = $this->repository->create([
                                        'amount' => $connectedOrder->getSumOfGrossValues(),
                                        'order_id' => $connectedOrder->id,
                                        'master_payment_id' => $globalPayment->id,
                                        'promise' => '',
                                    ]);
                                }

                                dispatch_now(new AddLabelJob($connectedOrder->id, [40]));
                            }
                        } else {
                            if (empty($this->repository->findWhere([
                                'amount' => $connectedOrder->getSumOfGrossValues(),
                                'order_id' => $connectedOrder->id,
                                'promise' => '',
                            ])->first())) {
                                if(empty($this->paymentRepository->findWhere([
                                    'amount' => $globalAmount,
                                    'customer_id' => $order->customer_id,
                                ])->first())) {
                                    $globalPayment = $this->paymentRepository->create([
                                        'amount' => $amount,
                                        'amount_left' => $globalAmount,
                                        'customer_id' => $order->customer_id,
                                        'created_at' => $date
                                    ]);
                                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                    $payment = $this->repository->create([
                                        'amount' => $amount,
                                        'order_id' => $connectedOrder->id,
                                        'promise' => '',
                                        'master_payment_id' => $globalPayment->id,
                                    ]);
                                } else {
                                    $globalPayment = $this->paymentRepository->findWhere([
                                        'amount' => $globalAmount,
                                        'customer_id' => $order->customer_id,
                                    ])->first();
                                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                    $payment = $this->repository->create([
                                        'amount' => $amount,
                                        'order_id' => $connectedOrder->id,
                                        'master_payment_id' => $globalPayment->id,
                                        'promise' => '',
                                    ]);
                                }

                                dispatch_now(new AddLabelJob($connectedOrder->id, [40]));
                            }
                        }
                    }
                    return ['orderId' => $orderId, 'amount' => $globalAmount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }

                if((float)$ordersSum < (float)$amount) {
                    foreach($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if(empty($this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first())) {
                                $globalPayment = $this->paymentRepository->create([
                                    'amount' => $amount,
                                    'amount_left' => $amount,
                                    'customer_id' => $order->customer_id,
                                    'created_at' => $date
                                ]);
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                            } else {
                                $globalPayment = $this->paymentRepository->findWhere([
                                    'amount' => $amount,
                                    'customer_id' => $order->customer_id,
                                ])->first();
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                            }

                            dispatch_now(new RemoveLabelJob($connectedOrder->id, [40]));
                        }
                        $amount = $amount - $connectedOrder->getSumOfGrossValues();
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if(empty($this->paymentRepository->findWhere([
                            'amount' => $amount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $amount,
                                'amount_left' => $amount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date

                            ]);
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                        } else {
                            $globalPayment = $this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first();
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                        }
                        dispatch_now(new RemoveLabelJob($order->id, [40]));
                    }
                    $amount = $amount - $order->getSumOfGrossValues();
                    return ['orderId' => $orderId, 'amount' => $amount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }
            }

            if($hasGroupBookedPayment == false && $hasGroupPromisePayment == false) {
                $ordersSum = 0;
                foreach($connectedOrders as $connectedOrder) {
                    $ordersSum += $connectedOrder->getSumOfGrossValues();
                }
                $ordersSum += $order->getSumOfGrossValues();

                if((float)$ordersSum == (float)$amount) {
                    foreach($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if(empty($this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first())) {
                                $globalPayment = $this->paymentRepository->create([
                                    'amount' => $amount,
                                    'amount_left' => $amount,
                                    'customer_id' => $order->customer_id,
                                    'created_at' => $date

                                ]);
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                            } else {
                                $globalPayment = $this->paymentRepository->findWhere([
                                    'amount' => $amount,
                                    'customer_id' => $order->customer_id,
                                ])->first();
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                            }

                            dispatch_now(new RemoveLabelJob($connectedOrder->id, [40]));
                        }
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if(empty($this->paymentRepository->findWhere([
                            'amount' => $amount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $amount,
                                'amount_left' => $amount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date

                            ]);
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                        } else {
                            $globalPayment = $this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first();
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                        }

                        dispatch_now(new RemoveLabelJob($order->id, [40]));
                    }
                    return ['orderId' => $orderId, 'amount' => $amount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }

                if((float)$ordersSum > (float)$amount) {
                }

                if((float)$ordersSum < (float)$amount) {
                    foreach($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if(empty($this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first())) {
                                $globalPayment = $this->paymentRepository->create([
                                    'amount' => $amount,
                                    'amount_left' => $amount,
                                    'customer_id' => $order->customer_id,
                                    'created_at' => $date

                                ]);
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                            } else {
                                $globalPayment = $this->paymentRepository->findWhere([
                                    'amount' => $amount,
                                    'customer_id' => $order->customer_id,
                                ])->first();
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                            }

                            dispatch_now(new RemoveLabelJob($connectedOrder->id, [40]));
                        }
                        $amount = $amount - $connectedOrder->getSumOfGrossValues();
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if(empty($this->paymentRepository->findWhere([
                            'amount' => $amount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $amount,
                                'amount_left' => $amount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date

                            ]);
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                        } else {
                            $globalPayment = $this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first();
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                        }
                        dispatch_now(new RemoveLabelJob($order->id, [40]));
                    }
                    $amount = $amount - $order->getSumOfGrossValues();
                    return ['orderId' => $orderId, 'amount' => $amount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }
            }
        } else {
            if ($order->payments->count() == 0) {
                dispatch_now(new DispatchLabelEventByNameJob($orderId, "payment-received"));
                dispatch_now(new RemoveLabelJob($orderId, [44]));
            }
            if (empty($this->repository->findWhere([
                'amount' => $amount,
                'order_id' => $orderId,
                'promise' => '',
            ])->first())) {
                if(empty($this->paymentRepository->findWhere([
                    'amount' => $amount,
                    'customer_id' => $order->customer_id,
                ])->first())) {
                    $globalPayment = $this->paymentRepository->create([
                        'amount' => $amount,
                        'amount_left' => $amount,
                        'customer_id' => $order->customer_id,
                        'created_at' => $date

                    ]);
                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                    $payment = $this->repository->create([
                        'amount' => $amount,
                        'order_id' => $orderId,
                        'promise' => '',
                        'master_payment_id' => $globalPayment->id,
                    ]);
                } else {
                    $globalPayment = $this->paymentRepository->findWhere([
                        'amount' => $amount,
                        'customer_id' => $order->customer_id,
                    ])->first();
                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);
                    $payment = $this->repository->create([
                        'amount' => $amount,
                        'order_id' => $orderId,
                        'promise' => '',
                        'master_payment_id' => $globalPayment->id,
                    ]);
                }




                OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);

                if ($payment != null && $order->status_id != 5) {
                    $this->orderRepository->update([
                        'status_id' => 5,
                    ], $orderId);
                }
                return ['orderId' => $orderId, 'amount' => $amount,  'info' => 'Zlecenie zostało pomyślnie utworzone.'];

            } else {
                return ['orderId' => $orderId, 'amount' => $amount, 'error' => 'Zlecenie posiada już taką wpłatę.'];
            }
        }
    }

    public function storeMaster(Request $request)
    {
        $orderId = $request->input('order_id');
        $promise = $request->input('promise');
        if (!empty($orderId)) {
            if($request->input('payment-type') == 'WAREHOUSE') {
                $order = Order::find($orderId);
                $payment = Payment::create([
                    'amount' => str_replace(",", ".", $request->input('amount')),
                    'amount_left' => str_replace(",", ".", $request->input('amount')),
                    'customer_id' => $request->input('customer_id'),
                    'warehouse_id' => $order->warehouse->id,
                    'notices' => $request->input('notices'),
                    'type' => $request->input('payment-type'),
                    'promise' => $promise
                ]);
            } else {
                $payment = Payment::create([
                    'amount' => str_replace(",", ".", $request->input('amount')),
                    'amount_left' => str_replace(",", ".", $request->input('amount')),
                    'customer_id' => $request->input('customer_id'),
                    'notices' => $request->input('notices'),
                    'type' => $request->input('payment-type'),
                    'promise' => $promise
                ]);
            }

            return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
                'message' => __('order_payments.message.store'),
                'alert-type' => 'success'
            ])->withInput(['tab' => 'orderPayments']);
        } else {
            return redirect()->route('payments.edit', ['id' => $request->input('customer_id')])->with([
                'message' => __('order_payments.message.store'),
                'alert-type' => 'success'
            ]);

        }

//        if(Auth::user()->role_id != 2 || Auth::user()->role_id != 3) {
//            return redirect()->route('orders.edit', ['order_id' => $order_id])->with([
//                'message' => __('order_payments.message.access_forbidden'),
//                'alert-type' => 'warning'
//            ]);
//        }

    }

    public function paymentsDestroy($id)
    {
        $deleted = $this->paymentRepository->delete($id);

        $orderPaymentsToDelete = $this->repository->findWhere(['master_payment_id' => $id]);
        foreach ($orderPaymentsToDelete as $orderPaymentToDelete) {
            $orderPaymentsToDelete->delete();
        }

        if (empty($deleted)) {
            return redirect()->back()->with([
                'message' => __('order_payments.message.not_delete'),
                'alert-type' => 'error'
            ])->withInput(['tab' => 'orderPayments']);
        }

        return redirect()->back()->with([
            'message' => __('order_payments.message.delete'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'orderPayments']);
    }

    public function bookPayment(Request $request)
    {
        $masterPaymentId = $request->input('masterPaymentId');
        $amount = $request->input('amount');
        $orderId = $request->input('promiseOrderId');

        $payment = $this->paymentRepository->find($masterPaymentId);
        $payment->update([
            'amount' => $amount,
            'promise' => ''
        ]);

        dispatch_now(new DispatchLabelEventByNameJob($orderId, "payment-received"));

        return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
            'message' => __('order_payments.message.store'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'orderPayments']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orderPayment = $this->repository->find($id);

        if ($orderPayment->master_payment_id != NULL) {
            $payment = $this->paymentRepository->find($orderPayment->master_payment_id);
            $payment->update([
                'amount_left' => $payment->amount_left + $orderPayment->amount,
            ]);
        }

        $deleted = $this->repository->delete($id);

        if (empty($deleted)) {
            return redirect()->back()->with([
                'message' => __('order_payments.message.not_delete'),
                'alert-type' => 'error'
            ])->withInput(['tab' => 'orderPayments']);
        }

        return redirect()->back()->with([
            'message' => __('order_payments.message.delete'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'orderPayments']);
    }

    public function payments()
    {
        $role = Role::find(Auth::user()->role_id);
        $roleName = $role->name;
        $this->roleName = $roleName;
        //pobieramy widzialności dla danego moduły oraz użytkownika
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('customers'));
        foreach ($visibilities as $key => $row) {
            $visibilities[$key]->show = json_decode($row->show, true);
            $visibilities[$key]->hidden = json_decode($row->hidden, true);
        }

        return view('payments.index', compact('roleName', 'visibilities'));
    }

    public function paymentsEdit($id)
    {
        $payment = $this->paymentRepository->find($id);

        return view('payments.edit',
            compact('payment'));
    }

    public function paymentUpdate(Request $request, $id)
    {
        $payment = $this->paymentRepository->find($id);

        $this->paymentRepository->update([
	    'amount' => $request->get('amount'),
            'created_at' => $request->get('created_at')
        ], $payment->id);

        return redirect()->back();
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
        $collection = $this->repository->findWhere(['order_id' => $id]);
        return $collection;
    }

    protected static function dispatchLabelsForPaymentAmount($payment): void
    {
        if ($payment->order->isPaymentRegulated()) {
            dispatch_now(new DispatchLabelEventByNameJob($payment->order->id, "payment-equal-to-order-value"));
        } else {
            dispatch_now(new DispatchLabelEventByNameJob($payment->order->id, "required-payment-before-unloading"));
        }
    }

    public static function payOrder($orderId, $amount, $masterPaymentId, string $promise, $chooseOrder, $promiseDate, $type = null, $isWarehousePayment = null): void
    {
        $order = Order::find($orderId);

        if ($order->payments->count() == 0) {
            dispatch_now(new DispatchLabelEventByNameJob($orderId, "payment-received"));
            dispatch_now(new RemoveLabelJob($orderId, [44]));
        }

        if($type == null) {
            $type = 'WAREHOUSE';
        }

        $token = null;

        if($isWarehousePayment) {
            $token = md5(uniqid());
            $url = route('ordersPayment.warehousePaymentConfirmation', ['token' => $token]);
            try {
                \Mailer::create()
                    ->to($order->warehouse->warehouse_email)
                    ->send(new WarehousePaymentAccept($orderId, $amount, $order->invoices()->first()->invoice_name, $url));
            } catch (\Swift_TransportException $e) {
                Log::error('Warehouse payment accept email was not sent due to. Error: ' . $e->getMessage());
            }
        }

        $payment = OrderPayment::create([
            'amount' => str_replace(",", ".", $amount),
            'master_payment_id' => $masterPaymentId ? $masterPaymentId : null,
            'order_id' => $orderId,
            'promise' => $promise,
            'promise_date' => $promiseDate ?: null,
            'type' => $type,
            'status' => $isWarehousePayment ? 'PENDING' : null,
            'token' => $token ? $token : null
        ]);

        if ($promise == '') {
            dispatch_now(new RemoveLabelJob($orderId, [119]));
        }

        OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);

        if (!empty($chooseOrder)) {
            $masterPayment = Payment::find($masterPaymentId);
            $masterPayment->amount_left = $masterPayment->amount_left - str_replace(",", ".", $amount);
            $masterPayment->save();
            $deleted = OrderPayment::where('order_id', $orderId)
                ->where('amount', $amount)
                ->where('promise', '1')->first();

            if (!empty($deleted)) {
                $deleted->delete();
            }
        }

        if ($payment != null && $order->status_id != 5) {
            $order->status_id = 5;
            $order->save();
        }
    }

    public function warehousePaymentConfirmation($token)
    {
        $orderPayment = OrderPayment::where('token', '=', $token)->first();

        return view('orderPayments.confirmWarehousePayment', compact('orderPayment', 'token'));
    }

    public function warehousePaymentConfirmationStore(Request $request)
    {
        $orderPayment = OrderPayment::find($request->input('orderPaymentId'));
        $orderPayment->update([
            'status' => 'ACCEPTED'
        ]);

        return view('orderPayments.warehousePaymentConfirmed');
    }
}

