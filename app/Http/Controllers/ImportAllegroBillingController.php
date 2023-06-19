<?php

namespace App\Http\Controllers;

use App\Factory\AllegroBilling\ImportAllegroBillingDTOFactory;
use App\Http\Requests\ImportAllegroBillingRequest;
use App\Services\ImportAllegroBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ImportAllegroBillingController extends Controller
{
    public function __invoke(
        ImportAllegroBillingRequest    $request,
        ImportAllegroBillingDTOFactory $DTOFactory,
        ImportAllegroBillingService    $importAllegroBillingService
    ): RedirectResponse
    {
        $data = $DTOFactory->createFromFile($request->file('file'));
        $importAllegroBillingService->import($data);

        return redirect()->back()->with('success', 'Imported successfully');
    }
}
