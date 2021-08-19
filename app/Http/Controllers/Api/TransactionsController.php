<?php

namespace App\Http\Controllers\Api;

use App\Repositories\TransactionRepository;
use App\Http\Controllers\Controller;

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
     * OrdersController constructor.
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
        $transaction = TransactionRepository::all();

    }

}
