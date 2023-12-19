<?php

namespace App\Http\Controllers;

use App\Enums\LabelStatusEnum;
use App\Helpers\OrderPackagesDataHelper;
use App\Repositories\LabelGroupRepository;
use Illuminate\View\View;

class OrderDatatableController extends Controller
{
    /**
     * OrderDatatableController constructor.
     *
     * @param OrderPackagesDataHelper $orderPackagesDataHelper
     * @param LabelGroupRepository $labelGroupRepository
     */
    public function __construct(
        private readonly OrderPackagesDataHelper $orderPackagesDataHelper,
        private readonly LabelGroupRepository $labelGroupRepository
    ) {}

    public function __invoke(): View
    {
        $templateData = $this->orderPackagesDataHelper->getData();

        $labelGroups = $this->labelGroupRepository->get()->sortBy('order');

        return view('order-datatable-index', compact('templateData', 'labelGroups'));
    }
}
