<?php

namespace App\Http\Controllers;

use App\Factory\AllegroBilling\ImportAllegroBillingDTOFactory;
use App\Http\Requests\ImportAllegroBillingRequest;
use App\Services\ImportAllegroBillingService;
use Illuminate\Http\JsonResponse;
class ImportAllegroBillingController extends Controller
{
    public function __invoke(
        ImportAllegroBillingRequest    $request,
        ImportAllegroBillingDTOFactory $DTOFactory,
        ImportAllegroBillingService    $importAllegroBillingService
    ): JsonResponse
    {
        $data = $DTOFactory->createFromFile($request->file('file'));
        $importAllegroBillingService->import($data);

        return response()->json([
            'message' => 'Imported Allegro billing',
        ]);
    }
}
