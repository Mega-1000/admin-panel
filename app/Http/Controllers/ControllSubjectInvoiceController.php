<?php

namespace App\Http\Controllers;

use App\Factory\ControllSubjectInvoiceDTOFactory;
use App\Services\ControllSubjectInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControllSubjectInvoiceController extends Controller
{
    public function __invoke(
        ControllSubjectInvoiceService $controllSubjectInvoiceService,
        Request $request,
    ): array
    {
        DB::statemnt('TRUNCATE TABLE controll_subject_invoices');

        return $controllSubjectInvoiceService->handle(
            ControllSubjectInvoiceDTOFactory::createFromCsvFile($request->file('file'))
        );
    }
}
