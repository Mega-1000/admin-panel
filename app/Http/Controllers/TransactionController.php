<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class TransactionController
 * @package App\Http\Controllers
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|View|BinaryFileResponse
     */
    public function index(Request $request)
    {
        if ($request->has('kind')) {
            $filePath = null;
            switch ($request->kind) {
                case 'allegroPayIn':
                    $filePath = 'public/transaction/TransactionWithoutOrders' . date('Y-m-d') . '.csv';
                    break;
                case 'bankPayIn':
                    $filePath = 'public/transaction/bankTransactionWithoutOrder' . date('Y-m-d') . '.csv';
                    break;
                case 'shippingTransaction':
                    $filePath = 'public/transaction/ShippingTransactionWithoutOrder' . date('Y-m-d') . '.csv';
                    break;
                default:
                    Log::warning('Nieobsługiwany typ transakcji.');
            }
            if (Storage::disk('local')->exists($filePath)) {
                return Storage::download($filePath);
            }
        }

        return view('transactions.index');
    }
}
