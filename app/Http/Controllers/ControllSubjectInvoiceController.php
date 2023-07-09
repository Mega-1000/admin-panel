<?php

namespace App\Http\Controllers;

use App\Factory\ControllSubjectInvoiceDTOFactory;
use App\Services\ControllSubjectInvoiceService;
use Illuminate\Http\Request;

class ControllSubjectInvoiceController extends Controller
{
    public function __invoke(
        ControllSubjectInvoiceService $controllSubjectInvoiceService,
        Request $request,
    ): array
    {
        return $controllSubjectInvoiceService->handle(
            ControllSubjectInvoiceDTOFactory::createFromCsvFile($request->file('file'))
        );
    }
}
