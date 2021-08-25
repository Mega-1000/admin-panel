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
        return Customer::with(['addresses' => function ($query) {
            $query->where('type', 'STANDARD_ADDRESS');
        }])
            ->with('transactions')->has('transactions')->get();
    }
}
