<?php

namespace App\Http\Controllers\Api;

use App\Entities\Customer;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    use ApiResponsesTrait;

    /** @var TransactionRepository */
    protected $transactionRepository;

    /**
     * TransactionRepository constructor.
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function index()
    {
        $response = [];
        try {
            $result = Customer::with(['addresses' => function ($query) {
                $query->where('type', 'STANDARD_ADDRESS');
            }])
                ->with('orders:id,customer_id')
                ->with('transactions')->has('transactions')->limit(25)->get();

            if (!empty($result)) {
                $response['status'] = 200;
                foreach ($result as $customer) {
                    $response['customers'][] = [
                        'id' => $customer->id,
                        'login' => $customer->login,
                        'nickAllegro' => $customer->nick_allegro,
                        'firstName' => $customer->addresses[0]->firstname,
                        'lastName' => $customer->addresses[0]->lastname,
                        'firmName' => $customer->addresses[0]->firmname,
                        'nip' => $customer->addresses[0]->nip,
                        'phone' => $customer->addresses[0]->phone,
                        'address' => $customer->addresses[0]->city,
                        'email' => $customer->addresses[0]->email,
                        'transactions' => $customer->transactions,
                        'orderIds' => $customer->orders->pluck('id')
                    ];
                }
            } else {
                $response = [
                    'error_code' => 505,
                    'error_message' => 'Brak transakcji'
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'error_code' => $exception->getCode(),
                'error_message' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'registrationInSystemDate' => 'required|date',
                'paymentId' => 'nullable',
                'operationKind' => 'required',
                'customerId' => 'required|exists:customers,id',
                'orderId' => ($request->get('orderId') === '0') ? 'nullable' : 'required|exists:orders,id',
                'operator' => 'required|string',
                'operationValue' => 'required|numeric',
                'accountingNotes' => 'nullable|string',
                'transactionNotes' => 'nullable|string',
            ],
            [
                'registrationInSystemDate.required' => 'Uzupełnij date rejestracji przelewu w systemie.',
                'registrationInBankDate.required' => 'Uzupełnij date rejestracji przelewu w banku.',
                'operationKind.required' => 'Pole rodzaj operacji jest wymagane.',
                'customerId.required' => 'Pole identyfikator klienta jest wymagane',
                'orderId.required' => 'Pole identyfikator zamówienia jest wymagane',
                'operator.required' => 'Pole operator jest wymagane.',
                'operationValue.required' => 'Pole wartość operacji jest wymagane',
            ]);

        if ($validator->passes()) {
            $operationValue = ((in_array($request->get('operationKind'),
                    [
                        'Wpłata',
                        'Uznanie'
                    ]
                ) ? '+' : '-')) . $request->get('operationValue');

            $balance = $this->transactionRepository->findWhere([
                ['customer_id', '=', $request->get('customerId')]
            ])->last()->balance;

            $this->transactionRepository->create([
                'customer_id' => $request->get('customerId'),
                'posted_in_system_date' => $request->get('registrationInSystemDate'),
                'posted_in_bank_date' => $request->get('registrationInBankDate'),
                'payment_id' => $request->get('paymentId'),
                'kind_of_operation' => $request->get('operationKind'),
                'order_id' => ($request->get('orderId') === '0') ? null : $request->get('orderId'),
                'operator' => $request->get('operator'),
                'operation_value' => $operationValue,
                'balance' => (int)$balance + (int)$operationValue,
                'accounting_notes' => $request->get('accountingNotes'),
                'transaction_notes' => $request->get('transactionNotes'),
            ]);
            return response()->json(['success' => 'Added new records.']);
        } else {
            return response()->json(
                [
                    'errorCode' => 442,
                    'errorMessage' => 'Wystąpił błąd podczas zapisu transakcji.',
                    'errors' => $validator->errors(),
                ],
                422);
        }
    }
}
