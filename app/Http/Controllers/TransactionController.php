<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\View\View;

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
     * @return Factory|Application|View
     */
    public function index()
    {
        return view('transactions.index');
    }
}
