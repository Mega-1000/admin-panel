<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Customer;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Payment;
use App\Entities\UserSurplusPayment;
use App\Entities\UserSurplusPaymentHistory;
use App\Entities\WorkingEvents;
use App\Enums\OrderPaymentLogTypeEnum;
use App\Helpers\AllegroPaymentImporter;
use App\Helpers\PriceHelper;
use App\Http\Requests\MasterPaymentCreateRequest;
use App\Http\Requests\OrderPaymentCreateRequest;
use App\Http\Requests\OrderPaymentUpdateRequest;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentLogService;
use App\Services\OrderPaymentService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use TCG\Voyager\Models\Role;
use Yajra\DataTables\Facades\DataTables;


/**
 * Class OrderPaymentsController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersPaymentsController extends Controller
{
    protected OrderPaymentRepository $repository;
    protected OrderRepository $orderRepository;
    protected PaymentRepository $paymentRepository;
    protected CustomerRepository $customerRepository;
    protected OrderPackageRepository $orderPackageRepository;
    protected OrderPaymentLogService $orderPaymentLogService;
    protected OrderPaymentService $orderPaymentService;

    public function __construct(
        OrderPaymentRepository $repository,
        OrderRepository        $orderRepository,
        PaymentRepository      $paymentRepository,
        CustomerRepository     $customerRepository,
        OrderPackageRepository $orderPackageRepository,
        OrderPaymentLogService $orderPaymentLogService,
        OrderPaymentService    $orderPaymentService
    )
    {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->customerRepository = $customerRepository;
        $this->orderPackageRepository = $orderPackageRepository;
        $this->orderPaymentLogService = $orderPaymentLogService;
        $this->orderPaymentService = $orderPaymentService;
    }

    /**
     * @return Factory|View
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

    public function payAllegro(Request $request)
    {
        $file = $request->file('file');
        $maxFileSize = 20000000;
        if ($file->getSize() > $maxFileSize) {
            return redirect()->route('orders.index')->with([
                'message' => __('transport.errors.too-big-file'),
                'alert-type' => 'error'
            ]);
        }
        $fixedCSV = str_replace(array("=\r\n", "=\r", "=\n"), "", $file->get());
        do {
            $fileName = Str::random(40) . '.csv';
            $path = Storage::path('user-files/allegro-payments/') . $fileName;
        } while (file_exists($path));
        Storage::put('user-files/allegro-payments/' . $fileName, $fixedCSV);
        $allegro = new AllegroPaymentImporter(Storage::path('user-files/allegro-payments/') . $fileName);
        $errors = $allegro->import();
        Storage::delete('user-files/allegro-payments/' . $fileName);
        return redirect()->route('orders.index')->with(
            'allegro_payments_errors', $errors
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(int $id)
    {
        WorkingEvents::createEvent(WorkingEvents::ORDER_PAYMENT_EDIT_EVENT, $id);
        $orderPayment = $this->repository->find($id);
        $customerOrders = $orderPayment->order->customer->orders;
        return view('orderPayments.edit', compact('orderPayment', 'id', 'customerOrders'));
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

        WorkingEvents::createEvent(WorkingEvents::ORDER_PAYMENT_STORE_EVENT, $order_id);
        $promise = $request->input('promise');

        if ($promise == 'on') {
            $promise = '1';
        } else {
            $promise = '';
        }

        $isWarehousePayment = $request->input('warehousePayment');

        $type = $request->input('payment-type');

        $promiseDate = $request->input('promise_date') ?: '';

        $orderPayment = $this->orderPaymentService->payOrder($orderId, $request->input('amount'),
            $masterPaymentId, $promise,
            $chooseOrder, $promiseDate,
            $type, $isWarehousePayment
        );


        $orderPaymentAmount = PriceHelper::modifyPriceToValidFormat($request->input('amount'));
        $orderPaymentsSum = $orderPayment->order->payments->sum('amount') - $orderPaymentAmount;

        $this->orderPaymentLogService->create(
            $orderId,
            $orderPayment->id,
            $orderPayment->order->customer_id,
            $orderPaymentsSum,
            $orderPaymentAmount,
            $request->input('created_at') ?: Carbon::now(),
            $request->input('notices') ?: '',
            $request->input('amount'),
            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
            true
        );

        return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
            'message' => __('order_payments.message.store'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'orderPayments']);
    }

    /**
     * @return Factory|View
     * @var integer $id Order ID
     */
    public function create($id)
    {
        WorkingEvents::createEvent(WorkingEvents::ORDER_PAYMENT_CREATE_EVENT, $id);
        return view('orderPayments.create', compact('id'));
    }

    // TODO WTF -- 1400 lines of code in one method?
    public function storeFromImport($orderId, $amount, $date)
    {
        if ($date == null) {
            $date = Carbon::now();
        }
        $globalAmount = $amount;
        if (strlen($orderId) > 10) {
            $orderPackage = $this->orderPackageRepository->findWhere(['letter_number' => $orderId])->first();
            if (!empty($orderPackage)) {
                $orderId = $orderPackage->order_id;
                $order = $this->orderRepository->findWhere(['id' => $orderId])->first();
            } else {
                return false;
            }
        } elseif (strlen($orderId) > 4 && strlen($orderId) < 10) {
            $order = $this->orderRepository->findWhere(['id_from_front_db' => $orderId])->first();
            $orderId = $order->id;
        } elseif (strlen($orderId) < 5) {
            $order = $this->orderRepository->find($orderId);
        }
        /////// połączone
        $clientPaymentAmount = $this->customerRepository->find($order->customer_id)->payments->sum('amount_left');
        $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        if ($connectedOrders->count() > 0) {
            $hasGroupPromisePayment = false;
            $hasGroupBookedPayment = false;

            foreach ($connectedOrders as $connectedOrder) {
                if ($connectedOrder->hasPromisePayments() > 0) {
                    $hasGroupPromisePayment = true;
                }
                if ($connectedOrder->hasBookedPayments() > 0) {
                    $hasGroupBookedPayment = true;
                }
            }
            if ($order->hasPromisePayments() > 0) {
                $hasGroupPromisePayment = true;
            }
            if ($order->hasBookedPayments() > 0) {
                $hasGroupBookedPayment = true;
            }
            if ($hasGroupPromisePayment === true) {
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

                                    if (empty($this->paymentRepository->findWhere([
                                        'amount' => $amount,
                                        'customer_id' => $order->customer_id,
                                    ])->first())) {
                                        $globalPayment = $this->paymentRepository->create([
                                            'amount' => $amount,
                                            'amount_left' => $amount,
                                            'customer_id' => $order->customer_id,
                                            'created_at' => $date
                                        ]);
                                        $this->orderPaymentLogService->create(
                                            $orderId,
                                            $globalPayment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $amount,
                                            $date,
                                            '',
                                            $amount,
                                            OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                            true
                                        );
                                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                        $payment = $this->repository->create([
                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                            'order_id' => $connectedOrder->id,
                                            'master_payment_id' => $globalPayment->id,
                                            'promise' => '',
                                        ]);

                                        $this->orderPaymentLogService->create(
                                            $connectedOrder->id,
                                            $payment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $connectedOrder->getSumOfGrossValues(),
                                            $date,
                                            '',
                                            $connectedOrder->getSumOfGrossValues(),
                                            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                            false
                                        );
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
                                        $this->orderPaymentLogService->create(
                                            $connectedOrder->id,
                                            $payment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $connectedOrder->getSumOfGrossValues(),
                                            $date,
                                            '',
                                            $connectedOrder->getSumOfGrossValues(),
                                            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                            false
                                        );
                                    }

                                    OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);
                                    $prev = [];
                                    AddLabelService::addLabels($connectedOrder, [130], $prev, [], Auth::user()->id);
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
                                    if (empty($this->paymentRepository->findWhere([
                                        'amount' => $amount,
                                        'customer_id' => $order->customer_id,
                                    ])->first())) {
                                        $globalPayment = $this->paymentRepository->create([
                                            'amount' => $amount,
                                            'amount_left' => $amount,
                                            'customer_id' => $order->customer_id,
                                            'created_at' => $date
                                        ]);
                                        $this->orderPaymentLogService->create(
                                            $orderId,
                                            $globalPayment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $amount,
                                            $date,
                                            '',
                                            $amount,
                                            OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                            true
                                        );
                                        if ($amount > $order->getSumOfGrossValues()) {
                                            $globalPayment->update(['amount_left' => $globalPayment->amount - $order->getSumOfGrossValues()]);

                                            $payment = $this->repository->create([
                                                'amount' => $order->getSumOfGrossValues(),
                                                'order_id' => $order->id,
                                                'master_payment_id' => $globalPayment->id,
                                                'promise' => '',
                                            ]);
                                            $this->orderPaymentLogService->create(
                                                $connectedOrder->id,
                                                $payment->id,
                                                $order->customer_id,
                                                $clientPaymentAmount,
                                                $connectedOrder->getSumOfGrossValues(),
                                                $date,
                                                '',
                                                $connectedOrder->getSumOfGrossValues(),
                                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                                false
                                            );
                                            if ($globalPayment->amount_left > 0) {
                                                foreach ($connectedOrders as $connectedOrder) {
                                                    if ($globalPayment->amount_left > $connectedOrder->getSumOfGrossValues()) {
                                                        $payment = $this->repository->create([
                                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                                            'order_id' => $connectedOrder->id,
                                                            'master_payment_id' => $globalPayment->id,
                                                            'promise' => '',
                                                        ]);
                                                        $this->orderPaymentLogService->create(
                                                            $connectedOrder->id,
                                                            $payment->id,
                                                            $order->customer_id,
                                                            $clientPaymentAmount,
                                                            $connectedOrder->getSumOfGrossValues(),
                                                            $date,
                                                            '',
                                                            $connectedOrder->getSumOfGrossValues(),
                                                            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                                            false
                                                        );
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
                                        $this->orderPaymentLogService->create(
                                            $connectedOrder->id,
                                            $payment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $connectedOrder->getSumOfGrossValues(),
                                            $date,
                                            '',
                                            $connectedOrder->getSumOfGrossValues(),
                                            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                            false
                                        );
                                    }

                                    OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);
                                    $prev = [];
                                    AddLabelService::addLabels($order, [130], $prev, [], Auth::user()->id);
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
                return ['orderId' => $orderId, 'amount' => $amount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];
            }

            if ($hasGroupPromisePayment === true) {
                $orderGroupPromisePaymentSum = 0;
                foreach ($connectedOrders as $connectedOrder) {
                    $orderGroupPromisePaymentSum += $connectedOrder->promisePaymentsSum();
                }
                $orderGroupPromisePaymentSum += $order->promisePaymentsSum();
                if ((float)$amount < (float)$orderGroupPromisePaymentSum) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if ($connectedOrder->hasPromisePayments() > 0) {
                            $prev = [];
                            AddLabelService::addLabels($order, [128], $prev, [], Auth::user()->id);
                        }
                    }
                }
            }

            if ($hasGroupPromisePayment === true) {
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

                                    if (empty($this->paymentRepository->findWhere([
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
                                        $this->orderPaymentLogService->create(
                                            $orderId,
                                            $globalPayment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $amount,
                                            $date,
                                            '',
                                            $amount,
                                            OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                            true
                                        );
                                        $payment = $this->repository->create([
                                            'amount' => $connectedOrder->getSumOfGrossValues(),
                                            'order_id' => $connectedOrder->id,
                                            'master_payment_id' => $globalPayment->id,
                                            'promise' => '',
                                        ]);
                                        $this->orderPaymentLogService->create(
                                            $connectedOrder->id,
                                            $payment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $connectedOrder->getSumOfGrossValues(),
                                            $date,
                                            '',
                                            $connectedOrder->getSumOfGrossValues(),
                                            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                            false
                                        );
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
                                        $this->orderPaymentLogService->create(
                                            $connectedOrder->id,
                                            $payment->id,
                                            $order->customer_id,
                                            $clientPaymentAmount,
                                            $connectedOrder->getSumOfGrossValues(),
                                            $date,
                                            '',
                                            $connectedOrder->getSumOfGrossValues(),
                                            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                            false
                                        );
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
                    if (empty($this->paymentRepository->findWhere([
                        'amount' => $amount,
                        'customer_id' => $order->customer_id,
                    ])->first())) {
                        $globalPayment = $this->paymentRepository->create([
                            'amount' => $amount,
                            'amount_left' => $amount,
                            'customer_id' => $order->customer_id,
                            'created_at' => $date
                        ]);
                        $this->orderPaymentLogService->create(
                            $orderId,
                            $globalPayment->id,
                            $order->customer_id,
                            $clientPaymentAmount,
                            $amount,
                            $date,
                            '',
                            $amount,
                            OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                            true
                        );
                        $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                        $payment = $this->repository->create([
                            'amount' => $amount,
                            'order_id' => $orderId,
                            'master_payment_id' => $globalPayment->id,
                            'promise' => '',
                        ]);
                        $this->orderPaymentLogService->create(
                            $connectedOrder->id,
                            $payment->id,
                            $order->customer_id,
                            $clientPaymentAmount,
                            $connectedOrder->getSumOfGrossValues(),
                            $date,
                            '',
                            $connectedOrder->getSumOfGrossValues(),
                            OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                            false
                        );
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
                        $this->orderPaymentLogService->create(
                            $orderId,
                            $globalPayment->id,
                            $order->customer_id,
                            $clientPaymentAmount,
                            $amount,
                            $date,
                            '',
                            $amount,
                            OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                            true
                        );
                    }
                }
                return ['orderId' => $orderId, 'amount' => $amount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];
            }

            if ($hasGroupBookedPayment === true) {
                $ordersSum = 0;
                foreach ($connectedOrders as $connectedOrder) {
                    $ordersSum += $connectedOrder->getSumOfGrossValues();
                }
                $ordersSum += $order->getSumOfGrossValues();
                if ((float)$ordersSum == (float)$amount) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if (empty($this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first())) {
                                $globalPayment = $this->paymentRepository->create([
                                    'amount' => $amount,
                                    'amount_left' => $amount,
                                    'customer_id' => $order->customer_id,
                                    'created_at' => $date
                                ]);
                                $this->orderPaymentLogService->create(
                                    $orderId,
                                    $globalPayment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $amount,
                                    $date,
                                    '',
                                    $amount,
                                    OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                    true
                                );
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'master_payment_id' => $globalPayment->id,
                                    'promise' => '',
                                ]);
                                $this->orderPaymentLogService->create(
                                    $orderId,
                                    $globalPayment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $amount,
                                    $date,
                                    '',
                                    $amount,
                                    OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                    true
                                );
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
                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $connectedOrder->getSumOfGrossValues(),
                                    $date,
                                    '',
                                    $connectedOrder->getSumOfGrossValues(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
                            }
                            $prev = [];
                            AddLabelService::addLabels($order, [40], $prev, [], Auth::user()->id);
                        }
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if (empty($this->paymentRepository->findWhere([
                            'amount' => $amount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $amount,
                                'amount_left' => $amount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date
                            ]);
                            $this->orderPaymentLogService->create(
                                $orderId,
                                $globalPayment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                true
                            );
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'master_payment_id' => $globalPayment->id,
                                'promise' => '',
                            ]);
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
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
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
                        }
                        $preventionArray = [];
                        RemoveLabelService::removeLabels($order, [40], $preventionArray, [], Auth::user()->id);
                        dispatch(new DispatchLabelEventByNameJob($order, "payment-received"));
                    }
                    return ['orderId' => $orderId, 'amount' => $amount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }

                if ((float)$ordersSum > (float)$amount) {
                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if (empty($this->paymentRepository->findWhere([
                            'amount' => $globalAmount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $globalAmount,
                                'amount_left' => $globalAmount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date
                            ]);
                            $this->orderPaymentLogService->create(
                                $orderId,
                                $globalPayment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $globalAmount,
                                $date,
                                '',
                                $globalAmount,
                                OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                true
                            );
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $order->toPay()]);
                            if ($order->toPay() < $amount) {
                                $amount = $amount - $order->toPay();
                                $payment = $this->repository->create([
                                    'amount' => $order->toPay(),
                                    'order_id' => $orderId,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id
                                ]);
                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $order->toPay(),
                                    $date,
                                    '',
                                    $order->toPay(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
                            } else {
                                $payment = $this->repository->create([
                                    'amount' => $amount,
                                    'order_id' => $orderId,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id
                                ]);
                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $amount,
                                    $date,
                                    '',
                                    $amount,
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
                                $amount = 0;
                            }
                        }
                        $prev = [];
                        RemoveLabelService::removeLabels($order, [40], $prev, [], Auth::user()?->id);
                        dispatch(new DispatchLabelEventByNameJob($order, "payment-received"));
                    }
                    foreach ($connectedOrders as $connectedOrder) {
                        if ($amount >= $connectedOrder->toPay()) {
                            if (empty($this->repository->findWhere([
                                'amount' => $connectedOrder->getSumOfGrossValues(),
                                'order_id' => $connectedOrder->id,
                                'promise' => '',
                            ])->first())) {
                                if (empty($this->paymentRepository->findWhere([
                                    'amount' => $amount,
                                    'customer_id' => $order->customer_id,
                                ])->first())) {
                                    $globalPayment = $this->paymentRepository->create([
                                        'amount' => $amount,
                                        'amount_left' => $amount,
                                        'customer_id' => $order->customer_id,
                                        'created_at' => $date
                                    ]);
                                    $this->orderPaymentLogService->create(
                                        $orderId,
                                        $globalPayment->id,
                                        $order->customer_id,
                                        $clientPaymentAmount,
                                        $amount,
                                        $date,
                                        '',
                                        $amount,
                                        OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                        true
                                    );
                                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                    $payment = $this->repository->create([
                                        'amount' => $connectedOrder->getSumOfGrossValues(),
                                        'order_id' => $connectedOrder->id,
                                        'master_payment_id' => $globalPayment->id,
                                        'promise' => '',
                                    ]);
                                    $this->orderPaymentLogService->create(
                                        $connectedOrder->id,
                                        $payment->id,
                                        $order->customer_id,
                                        $clientPaymentAmount,
                                        $connectedOrder->getSumOfGrossValues(),
                                        $date,
                                        '',
                                        $connectedOrder->getSumOfGrossValues(),
                                        OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                        false
                                    );
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
                                    $this->orderPaymentLogService->create(
                                        $connectedOrder->id,
                                        $payment->id,
                                        $order->customer_id,
                                        $clientPaymentAmount,
                                        $connectedOrder->getSumOfGrossValues(),
                                        $date,
                                        '',
                                        $connectedOrder->getSumOfGrossValues(),
                                        OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                        false
                                    );
                                }
                                $prev = [];
                                AddLabelService::addLabels($connectedOrder, [40], $prev, [], Auth::user()->id);
                            }
                        } else {
                            if (empty($this->repository->findWhere([
                                'amount' => $connectedOrder->getSumOfGrossValues(),
                                'order_id' => $connectedOrder->id,
                                'promise' => '',
                            ])->first())) {
                                if (empty($this->paymentRepository->findWhere([
                                    'amount' => $globalAmount,
                                    'customer_id' => $order->customer_id,
                                ])->first())) {
                                    $globalPayment = $this->paymentRepository->create([
                                        'amount' => $amount,
                                        'amount_left' => $globalAmount,
                                        'customer_id' => $order->customer_id,
                                        'created_at' => $date
                                    ]);
                                    $this->orderPaymentLogService->create(
                                        $orderId,
                                        $globalPayment->id,
                                        $order->customer_id,
                                        $clientPaymentAmount,
                                        $amount,
                                        $date,
                                        '',
                                        $amount,
                                        OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                        true
                                    );
                                    $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                    $payment = $this->repository->create([
                                        'amount' => $amount,
                                        'order_id' => $connectedOrder->id,
                                        'promise' => '',
                                        'master_payment_id' => $globalPayment->id,
                                    ]);
                                    $this->orderPaymentLogService->create(
                                        $connectedOrder->id,
                                        $payment->id,
                                        $order->customer_id,
                                        $clientPaymentAmount,
                                        $amount,
                                        $date,
                                        '',
                                        $amount,
                                        OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                        false
                                    );
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
                                    $this->orderPaymentLogService->create(
                                        $connectedOrder->id,
                                        $payment->id,
                                        $order->customer_id,
                                        $clientPaymentAmount,
                                        $amount,
                                        $date,
                                        '',
                                        $amount,
                                        OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                        false
                                    );
                                }
                                $prev = [];
                                AddLabelService::addLabels($connectedOrder, [40], $prev, [], Auth::user()->id);
                            }
                        }
                    }
                    return ['orderId' => $orderId, 'amount' => $globalAmount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }

                if ((float)$ordersSum < (float)$amount) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if (empty($this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first())) {
                                $globalPayment = $this->paymentRepository->create([
                                    'amount' => $amount,
                                    'amount_left' => $amount,
                                    'customer_id' => $order->customer_id,
                                    'created_at' => $date
                                ]);
                                $this->orderPaymentLogService->create(
                                    $orderId,
                                    $globalPayment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $amount,
                                    $date,
                                    '',
                                    $amount,
                                    OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                    true
                                );
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);

                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $connectedOrder->getSumOfGrossValues(),
                                    $date,
                                    '',
                                    $connectedOrder->getSumOfGrossValues(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
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

                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $connectedOrder->getSumOfGrossValues(),
                                    $date,
                                    '',
                                    $connectedOrder->getSumOfGrossValues(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
                            }

                            $preventionArray = [];
                            RemoveLabelService::removeLabels($connectedOrder, [40], $preventionArray, [], Auth::user()->id);
                        }
                        $amount = $amount - $connectedOrder->getSumOfGrossValues();
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if (empty($this->paymentRepository->findWhere([
                            'amount' => $amount,
                            'customer_id' => $order->customer_id,
                        ])->first())) {
                            $globalPayment = $this->paymentRepository->create([
                                'amount' => $amount,
                                'amount_left' => $amount,
                                'customer_id' => $order->customer_id,
                                'created_at' => $date

                            ]);
                            $this->orderPaymentLogService->create(
                                $orderId,
                                $globalPayment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                true
                            );
                            $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
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
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
                        }
                        $preventionArray = [];
                        RemoveLabelService::removeLabels($order, [40], $preventionArray, [], Auth::user()->id);
                    }
                    $amount = $amount - $order->getSumOfGrossValues();
                    return ['orderId' => $orderId, 'amount' => $amount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }
            }

            if ($hasGroupBookedPayment === false) {
                $ordersSum = 0;
                foreach ($connectedOrders as $connectedOrder) {
                    $ordersSum += $connectedOrder->getSumOfGrossValues();
                }
                $ordersSum += $order->getSumOfGrossValues();

                if ((float)$ordersSum == (float)$amount) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if (empty($this->paymentRepository->findWhere([
                                'amount' => $amount,
                                'customer_id' => $order->customer_id,
                            ])->first())) {
                                $globalPayment = $this->paymentRepository->create([
                                    'amount' => $amount,
                                    'amount_left' => $amount,
                                    'customer_id' => $order->customer_id,
                                    'created_at' => $date
                                ]);
                                $this->orderPaymentLogService->create(
                                    $orderId,
                                    $globalPayment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $amount,
                                    $date,
                                    '',
                                    $amount,
                                    OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                    true
                                );
                                $globalPayment->update(['amount_left' => $globalPayment->amount - $amount]);

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $connectedOrder->getSumOfGrossValues(),
                                    $date,
                                    '',
                                    $connectedOrder->getSumOfGrossValues(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
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
                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $connectedOrder->getSumOfGrossValues(),
                                    $date,
                                    '',
                                    $connectedOrder->getSumOfGrossValues(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
                            }

                            $preventionArray = [];
                            RemoveLabelService::removeLabels($connectedOrder, [40], $preventionArray, [], Auth::user()->id);
                        }
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if (empty($this->paymentRepository->findWhere([
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
                            $this->orderPaymentLogService->create(
                                $orderId,
                                $globalPayment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                true
                            );
                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
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
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
                        }
                        $preventionArray = [];
                        RemoveLabelService::removeLabels($order, [40], $preventionArray, [], Auth::user()->id);
                    }
                    return ['orderId' => $orderId, 'amount' => $amount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }

                if ((float)$ordersSum < (float)$amount) {
                    foreach ($connectedOrders as $connectedOrder) {
                        if (empty($this->repository->findWhere([
                            'amount' => $connectedOrder->getSumOfGrossValues(),
                            'order_id' => $connectedOrder->id,
                            'promise' => '',
                        ])->first())) {
                            if (empty($this->paymentRepository->findWhere([
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

                                $this->orderPaymentLogService->create(
                                    $orderId,
                                    $globalPayment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $amount,
                                    $date,
                                    '',
                                    $amount,
                                    OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                    true
                                );

                                $payment = $this->repository->create([
                                    'amount' => $connectedOrder->getSumOfGrossValues(),
                                    'order_id' => $connectedOrder->id,
                                    'promise' => '',
                                    'master_payment_id' => $globalPayment->id,
                                ]);
                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $connectedOrder->getSumOfGrossValues(),
                                    $date,
                                    '',
                                    $connectedOrder->getSumOfGrossValues(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
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
                                $this->orderPaymentLogService->create(
                                    $connectedOrder->id,
                                    $payment->id,
                                    $order->customer_id,
                                    $clientPaymentAmount,
                                    $connectedOrder->getSumOfGrossValues(),
                                    $date,
                                    '',
                                    $connectedOrder->getSumOfGrossValues(),
                                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                    false
                                );
                            }
                            $preventionArray = [];
                            RemoveLabelService::removeLabels($connectedOrder, [40], $preventionArray, [], Auth::user()->id);
                        }
                        $amount = $amount - $connectedOrder->getSumOfGrossValues();
                    }

                    if (empty($this->repository->findWhere([
                        'amount' => $order->getSumOfGrossValues(),
                        'order_id' => $order->id,
                        'promise' => '',
                    ])->first())) {
                        if (empty($this->paymentRepository->findWhere([
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

                            $this->orderPaymentLogService->create(
                                $orderId,
                                $globalPayment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                                true
                            );

                            $payment = $this->repository->create([
                                'amount' => $amount,
                                'order_id' => $orderId,
                                'promise' => '',
                                'master_payment_id' => $globalPayment->id,
                            ]);
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
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
                            $this->orderPaymentLogService->create(
                                $connectedOrder->id,
                                $payment->id,
                                $order->customer_id,
                                $clientPaymentAmount,
                                $amount,
                                $date,
                                '',
                                $amount,
                                OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                                false
                            );
                        }

                        $preventionArray = [];
                        RemoveLabelService::removeLabels($order, [40], $preventionArray, [], Auth::user()->id);
                    }
                    $amount = $amount - $order->getSumOfGrossValues();
                    return ['orderId' => $orderId, 'amount' => $amount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];
                }
            }
        } else {
            if ($order->payments->count() == 0) {

                dispatch(new DispatchLabelEventByNameJob($order, "payment-received"));

                $order = Order::query()->find($orderId);
                $preventionArray = [];
                RemoveLabelService::removeLabels($order, [44], $preventionArray, [], Auth::user()->id);
            }
            if (empty($this->repository->findWhere([
                'amount' => $amount,
                'order_id' => $orderId,
                'promise' => '',
            ])->first())) {
                if (empty($this->paymentRepository->findWhere([
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

                    $this->orderPaymentLogService->create(
                        $orderId,
                        $globalPayment->id,
                        $order->customer_id,
                        $clientPaymentAmount,
                        $amount,
                        $date,
                        '',
                        $amount,
                        OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                        true
                    );

                    $payment = $this->repository->create([
                        'amount' => $amount,
                        'order_id' => $orderId,
                        'promise' => '',
                        'master_payment_id' => $globalPayment->id,
                    ]);
                    $this->orderPaymentLogService->create(
                        $orderId,
                        $payment->id,
                        $order->customer_id,
                        $clientPaymentAmount,
                        $amount,
                        $date,
                        '',
                        $amount,
                        OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                        false
                    );
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
                    $this->orderPaymentLogService->create(
                        $orderId,
                        $payment->id,
                        $order->customer_id,
                        $clientPaymentAmount,
                        $amount,
                        $date,
                        '',
                        $amount,
                        OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                        false
                    );
                }

                OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);

                if ($payment != null && $order->status_id != 5) {
                    $this->orderRepository->update([
                        'status_id' => 5,
                    ], $orderId);
                }
                return ['orderId' => $orderId, 'amount' => $amount, 'info' => 'Zlecenie zostało pomyślnie utworzone.'];

            } else {
                return ['orderId' => $orderId, 'amount' => $amount, 'error' => 'Zlecenie posiada już taką wpłatę.'];
            }
        }

        return null;
    }

    /**
     * @param OrderPaymentUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(OrderPaymentUpdateRequest $request, $id)
    {
        WorkingEvents::createEvent(WorkingEvents::ORDER_PAYMENT_UPDATE_EVENT, $id);
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
            $prev = [];
            AddLabelService::addLabels($orderPayment, [5], $prev, [], Auth::user()->id);
        }

        $payment = $this->repository->update([
            'amount' => PriceHelper::modifyPriceToValidFormat($request->input('amount')),
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

    public static function dispatchLabelsForPaymentAmount($payment): void
    {
        if ($payment->order->isPaymentRegulated()) {
            dispatch(new DispatchLabelEventByNameJob($payment->order, "payment-equal-to-order-value"));
        } else {
            dispatch(new DispatchLabelEventByNameJob($payment->order, "required-payment-before-unloading"));
        }
    }

    public function storeMaster(MasterPaymentCreateRequest $request)
    {
        $orderId = $request->input('order_id');
        $promise = $request->input('promise');
        $amount = PriceHelper::modifyPriceToValidFormat($request->input('amount'));
        $clientPaymentAmount = $this->customerRepository->find($request->input('customer_id'))->payments->sum('amount_left');
        if (!empty($orderId)) {
            $validated = $request->validated();
            if ($request->input('payment-type') == 'WAREHOUSE') {
                $order = Order::find($orderId);
                $payment = Payment::create([
                    'amount' => $amount,
                    'amount_left' => $amount,
                    'customer_id' => $validated['customer_id'],
                    'warehouse_id' => $order->warehouse->id,
                    'notices' => $validated['notices'],
                    'type' => $validated['payment-type'],
                    'promise' => $promise
                ]);
            } else {
                $payment = Payment::create([
                    'amount' => $amount,
                    'amount_left' => $amount,
                    'customer_id' => $validated['customer_id'],
                    'notices' => $validated['notices'],
                    'type' => $validated['payment-type'],
                    'promise' => $promise
                ]);
            }


            $this->orderPaymentLogService->create(
                $orderId,
                null,
                $validated['customer_id'],
                $clientPaymentAmount,
                $amount,
                $validated['created_at'],
                $validated['notices'],
                $validated['amount'],
                OrderPaymentLogTypeEnum::CLIENT_PAYMENT,
                true
            );

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
        /** @var Order $order */
        $order = Order::query()->findOrFail($orderId);
        dispatch(new DispatchLabelEventByNameJob($order, "payment-received"));

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
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $orderPayment = $this->repository->find($id);

        if ($orderPayment->master_payment_id != NULL) {
            $payment = $this->paymentRepository->find($orderPayment->master_payment_id);
            $payment->update([
                'amount_left' => $payment->amount_left + $orderPayment->amount,
            ]);
            $clientPaymentAmount = $this->customerRepository->find($orderPayment->order->customer_id)->payments->sum('amount_left');
            $this->orderPaymentLogService->create(
                $orderPayment->order_id,
                $orderPayment->master_payment_id,
                $orderPayment->order->customer_id,
                $clientPaymentAmount,
                $orderPayment->amount,
                Carbon::now(),
                '',
                $orderPayment->amount,
                OrderPaymentLogTypeEnum::REMOVE_PAYMENT,
                true
            );
        }

        $this->orderPaymentLogService->create(
            $orderPayment->order_id,
            $orderPayment->master_payment_id,
            $orderPayment->order->customer_id,
            $orderPayment->order->payments->sum('amount'),
            $orderPayment->amount,
            Carbon::now(),
            '',
            $orderPayment->amount,
            OrderPaymentLogTypeEnum::REMOVE_PAYMENT,
            false
        );

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
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
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
        $collection = $this->repository->findWhere(['order_id' => $id]);
        return $collection;
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

    public function returnSurplusPayment(Request $request)
    {
        $userSurplusPayment = UserSurplusPayment::find($request->input('user_surplus_id'));
        if (empty($userSurplusPayment)) {
            abort(404);
        }
        $userSurplusPayment->update([
            'surplus_amount' => $userSurplusPayment->surplus_amount - $request->input('surplus_amount')
        ]);


        $userSurplusPaymentHistory = UserSurplusPaymentHistory::create([
            'user_id' => $request->input('surplus_customer_id'),
            'surplus_amount' => $request->input('surplus_amount'),
            'operation' => 'DECREASE',
            'user_surplus_payment' => $userSurplusPayment->id
        ]);

        $customer = Customer::find($request->input('surplus_customer_id'));

        $payments = $customer->surplusPayments;

        if ($payments->sum('surplus_amount') == 0) {
            foreach ($customer->orders as $order) {
                $preventionArray = [];
                RemoveLabelService::removeLabels($order, [Label::ORDER_SURPLUS], $preventionArray, [], Auth::user()->id);
            }
        } else {
            foreach ($customer->orders as $order) {
                $preventionArray = [];
                RemoveLabelService::removeLabels($order, [Label::ORDER_SURPLUS], $preventionArray, [], Auth::user()->id);
            }
        }

        return redirect()->back();
    }
}
