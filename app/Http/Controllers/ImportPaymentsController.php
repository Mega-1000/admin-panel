<?php

namespace App\Http\Controllers;

use App\Jobs\ImportPaymentsFromPdfFile;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentImportRepository;
use Illuminate\Http\Request;

class ImportPaymentsController extends Controller
{

    /**
     * @var PaymentImportRepository
     */
    protected $paymentImportRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * ImportPaymentsController constructor.
     * @param PaymentImportRepository $paymentImportRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(PaymentImportRepository $paymentImportRepository, OrderRepository $orderRepository)
    {
        $this->paymentImportRepository = $paymentImportRepository;
        $this->orderRepository = $orderRepository;
    }


    public function importPayments()
    {
        return view('invoices.import.payments');
    }

    public function store(OrderRepository $orderRepository, Request $request)
    {
        $date = $request->get('created_at');

        $path = $request->file('payments')->store('payments');


        $this->paymentImportRepository->create([
            'file_path' => $path
        ]);

        $payments = dispatch_now(new ImportPaymentsFromPdfFile($orderRepository, $path, $date));


        return redirect()->route('invoices.importPayments')->with([
            'message' => __('order_payments.import'),
            'alert-type' => 'success',
            'payments' => $payments
        ]);
    }
}
