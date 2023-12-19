<?php

namespace App\Http\Controllers;

use App\Helpers\OrderPackagesDataHelper;
use Illuminate\View\View;

class OrderDatatableController extends Controller
{
    /**
     * OrderDatatableController constructor.
     *
     * @param OrderPackagesDataHelper $orderPackagesDataHelper
     */
    public function __construct(
        private readonly OrderPackagesDataHelper $orderPackagesDataHelper,
    ) {}

    public function __invoke(): View
    {
        $templateData = $this->orderPackagesDataHelper->getData();

        return view('order-datatable-index', compact('templateData'));
    }
}
