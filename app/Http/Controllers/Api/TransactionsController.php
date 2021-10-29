<?php

namespace App\Http\Controllers\Api;

use App\Entities\Customer;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;

class TransactionsController extends Controller
{
    use ApiResponsesTrait;

    /** @var TransactionRepository */
    protected $transactionRepository;

    private $error_code = null;

    private $errors = [

    ];

    private $defaultError = 'Wystąpił wewnętrzny błąd systemu przy składaniu zamówienia. Dział techniczny został o tym poinformowany.';

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
                        'transactions' => $customer->transactions
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
}
