<?php

namespace App\Http\Controllers\Api;

use App\Entities\Customer;
use App\Entities\Order;
use App\Entities\Transaction;
use App\Http\Controllers\Controller;
use App\Jobs\ImportAllegroPayInJob;
use App\Jobs\ImportBankPayIn;
use App\Jobs\ImportShippingPayIn;
use App\Repositories\ProviderTransactionRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    use ApiResponsesTrait;

    /** @var TransactionRepository */
    protected $transactionRepository;

    /** @var ProviderTransactionRepository */
    protected $providersTransactionRepository;

    /**
     * TransactionRepository constructor.
     * @param TransactionRepository $transactionRepository
     * @param ProviderTransactionRepository $providersTransactionRepository
     */
    public function __construct(
        TransactionRepository         $transactionRepository,
        ProviderTransactionRepository $providersTransactionRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->providersTransactionRepository = $providersTransactionRepository;
    }

    /**
     * Zwraca klientów z transakcjami
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function index(Request $request): JsonResponse
    {
        $response = [];

        try {
            $query = Customer::with(['addresses' => function ($query) {
                $query->where('type', 'STANDARD_ADDRESS');
            }])
                ->with('orders:id,customer_id')
                ->whereHas('transactions', function ($q) {
                    $q->where('company_name', '=', 'EPH');
                })
                ->orderBy('customers.id', 'desc');

            if ($request->has('nip')) {
                $query->where('nip', 'like', '%' . $request->get('nip') . '%');
            }
            if ($request->has('nickAllegro')) {
                $query->where('nick_allegro', 'like', '%' . $request->get('nickAllegro') . '%');
            }
            if ($request->has('email')) {
                $query->where('login', 'like', '%' . $request->get('email') . '%');
            }
            if ($request->has('phone')) {
                $query->where('phone', 'like', '%' . $request->get('phone') . '%');
            }
            $result = $query->paginate(300);

            if (!empty($result)) {
                $response['status'] = 200;
                $response['currentPage'] = $result->currentPage();
                $response['lastPage'] = $result->lastPage();
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
                        'orderIds' => $customer->orders->pluck('id')
                    ];
                }
            } else {
                $response = [
                    'errorCode' => 424,
                    'errorMessage' => 'Brak transakcji'
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }

    /**
     * Zapis nowej transakcji
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidatorRules($request),
            $this->getValidatorMessages()
        );

        if ($validator->passes()) {
            $operationKind = $request->get('operationKind');
            $operationValue = ((in_array($operationKind,
                    [
                        'wpłata',
                        'uznanie',
                    ]
                ) ? '+' : '-')) . $request->get('operationValue');

            if (!empty($lastCustomerTransaction = $this->transactionRepository->findWhere([
                ['customer_id', '=', $request->get('customerId')]
            ])->last())) {
                $balance = $lastCustomerTransaction->balance;
            } else {
                $balance = 0;
            }

            $transaction = $this->transactionRepository->create([
                'customer_id' => $request->get('customerId'),
                'posted_in_system_date' => $request->get('registrationInSystemDate'),
                'posted_in_bank_date' => $request->get('registrationInBankDate'),
                'payment_id' => $request->get('paymentId'),
                'kind_of_operation' => $request->get('operationKind'),
                'order_id' => ($request->get('orderId') === '0') ? null : $request->get('orderId'),
                'operator' => $request->get('operator'),
                'operation_value' => $operationValue,
                'balance' => (float)$balance + (float)$operationValue,
                'accounting_notes' => $request->get('accountingNotes'),
                'transaction_notes' => $request->get('transactionNotes'),
                'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL
            ]);

            if ($operationKind === 'przeksięgowanie') {
                $transaction->order->payments()->create([
                    'transaction_id' => $transaction->id,
                    'amount' => $operationValue,
                    'type' => 'CLIENT',
                    'promise' => '',
                ]);
            }

            return response()->json(['success' => 'Transakcja została zapisana.']);
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

    /**
     * Aktualizacja transakcji
     *
     * @param Transaction $transaction Transakcja
     * @param Request $request Request
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function update(Transaction $transaction, Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidatorRules($request),
            $this->getValidatorMessages()
        );

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

            if ((int)$transaction->operation_value < 0) {
                $oldBalance = (int)$balance + abs((int)$transaction->operation_value);
            } else {
                $oldBalance = (int)$balance - (int)$transaction->operation_value;
            }

            $transaction->update([
                'customer_id' => $request->get('customerId'),
                'posted_in_system_date' => $request->get('registrationInSystemDate'),
                'posted_in_bank_date' => $request->get('registrationInBankDate'),
                'payment_id' => $request->get('paymentId'),
                'kind_of_operation' => $request->get('operationKind'),
                'order_id' => ($request->get('orderId') === '0') ? null : $request->get('orderId'),
                'operator' => $request->get('operator'),
                'operation_value' => $operationValue,
                'balance' => $oldBalance + (int)$operationValue,
                'accounting_notes' => $request->get('accountingNotes'),
                'transaction_notes' => $request->get('transactionNotes'),
            ]);
            return response()->json(['success' => 'Transakcja została zaktualizowana.']);
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

    /**
     * Usunięcie transakcji
     *
     * @param Transaction $transaction Transakcja
     *
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $response = [];
        try {
            $result = $this->transactionRepository->delete($transaction->id);
            if ($result) {
                $response['status'] = 200;
            }
        } catch (\Exception $exception) {
            $response = [
                'status' => 424,
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }
        return response()->json($response, $response['status']);
    }

    /**
     * Import transaction from file
     *
     * @param string $kind
     * @param Request $request
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function import(string $kind, Request $request): JsonResponse
    {
        $job = null;
        switch ($kind) {
            case 'allegroPayIn':
                $job = new ImportAllegroPayInJob($request->file('file'));
                break;
            case 'bankPayIn':
                $job = new ImportBankPayIn($request->file('file'));
                break;
            case 'shippingTransaction':
                $job = new ImportShippingPayIn($request->file('file'));
                break;
            default:
                $response = [
                    'errorCode' => 303,
                    'errorMessage' => 'Wybrany format importu nie jest wspierany.'
                ];
        }
        if ($job instanceof ShouldQueue) {
            try {
                $response = dispatch_now($job);
            } catch (\Exception $exception) {
                Log::notice('twoja stara 5' . $exception);
                $response = [
                    'errorCode' => 500,
                    'errorMessage' => $exception->getMessage()
                ];
            }
        }

        return response()->json($response);
    }

    /**
     * Zwraca reguły walidacji transakcji
     *
     * @param Request $request
     * @return string[]
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getValidatorRules(Request $request): array
    {
        return [
            'registrationInSystemDate' => 'required|date',
            'paymentId' => 'nullable',
            'operationKind' => 'required',
            'customerId' => 'required|exists:customers,id',
            'orderId' => ($request->get('orderId') === '0') ? 'nullable' : 'required|exists:orders,id',
            'operator' => 'required|string',
            'operationValue' => 'required|numeric',
            'accountingNotes' => 'nullable|string',
            'transactionNotes' => 'nullable|string',
        ];
    }

    /**
     * Zwraca wiadomości do walidacji transakcji
     *
     * @return string[]
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getValidatorMessages(): array
    {
        return [
            'registrationInSystemDate.required' => 'Uzupełnij date rejestracji przelewu w systemie.',
            'registrationInBankDate.required' => 'Uzupełnij date rejestracji przelewu w banku.',
            'operationKind.required' => 'Pole rodzaj operacji jest wymagane.',
            'customerId.required' => 'Pole identyfikator klienta jest wymagane',
            'orderId.required' => 'Pole identyfikator zamówienia jest wymagane',
            'operator.required' => 'Pole operator jest wymagane.',
            'operationValue.required' => 'Pole wartość operacji jest wymagane',
        ];
    }

    /**
     * Zwraca klientów z transakcjami
     *
     * @param int $id
     * @return JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function customer(int $id): JsonResponse
    {
        $response = [];

        try {
            $transactions = Transaction::where('customer_id', '=', $id)->where('company_name', '=', Transaction::NEW_COMPANY_NAME_SYMBOL)->orderBy('id', 'desc')->get();

            if (!empty($transactions)) {
                $response['transactions'] = $transactions;
            } else {
                $response = [
                    'errorCode' => 424,
                    'errorMessage' => 'Brak transakcji'
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }

    public function providersTransactions(Request $request): JsonResponse
    {
        $response = [];
        try {
            $query = $this->providersTransactionRepository->select()
                ->whereHas('transactions', function ($q) {
                    $q->where('company_name', '=', 'EPH');
                });
            if ($request->has('provider')) {
                $query->where('provider', 'like', '%' . $request->get('provider') . '%');
            }

            $result = $query->paginate(20);

            if (!empty($result)) {
                $response['status'] = 200;
                $response['currentPage'] = $result->currentPage();
                $response['lastPage'] = $result->lastPage();
                foreach ($result as $transaction) {
                    $response['transactions'][] = [
                        'id' => $transaction->id,
                        'provider' => $transaction->provider,
                        'waybillNumber' => $transaction->waybill_number,
                        'invoiceNumber' => $transaction->invoice_number,
                        'orderId' => $transaction->order_id,
                        'cashOnDelivery' => $transaction->cash_on_delivery,
                        'providerBalance' => $transaction->provider_balance,
                        'providerBalanceOnInvoice' => $transaction->provider_balance_on_invoice,
                        'transactionId' => $transaction->transaction_id,
                    ];
                }
            } else {
                $response = [
                    'errorCode' => 424,
                    'errorMessage' => 'Brak transakcji'
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }

        return response()->json($response);
    }
}
