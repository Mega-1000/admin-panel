<?php

namespace App\Http\Controllers;

use App\Factory\ControllSubjectInvoiceDTOFactory;
use App\Services\ControllSubjectInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ControllSubjectInvoiceController extends Controller
{
    public function __invoke(
        ControllSubjectInvoiceService $controllSubjectInvoiceService,
        Request $request,
    ): View
    {
        $report = $controllSubjectInvoiceService->handle(
            ControllSubjectInvoiceDTOFactory::createFromCsvFile($request->file('file'))
        );

        return view('subject-controll.table', compact('report'));
    }
}
