<?php

namespace App\Http\Controllers;

use App\Factory\ControllSubjectInvoiceDTOFactory;
use App\Services\ControllSubjectInvoiceBuyingService;
use App\Services\ControllSubjectInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ControllSubjectInvoiceController extends Controller
{
    public function __invoke(
        ControllSubjectInvoiceService $controllSubjectInvoiceService,
        ControllSubjectInvoiceBuyingService $buyingService,
        Request $request,
    ): View
    {
        $dto = ControllSubjectInvoiceDTOFactory::createFromCsvFile($request->file('file'));

        $report = [];
        if ($request->get('invoice-kind' === 'faktury sprzedazy')) {
            dd('okej');
            $report = $controllSubjectInvoiceService->handle($dto);
        } else {
            dd('okej1');
            $buyingService->handle($dto);
        }


        return view('subject-controll.table', compact('report'));
    }
}
