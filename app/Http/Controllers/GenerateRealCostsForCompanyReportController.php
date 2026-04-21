<?php

namespace App\Http\Controllers;

use App\Services\GenerateRealCostsForCompanyReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GenerateRealCostsForCompanyReportController extends Controller
{
    /**
     * Generate real costs for company report
     *
     * @param GenerateRealCostsForCompanyReportService $generateRealCostsForCompanyReportService
     * @return StreamedResponse
     */
    public function __invoke(
        GenerateRealCostsForCompanyReportService $generateRealCostsForCompanyReportService,
    ): StreamedResponse
    {
        $callback = $generateRealCostsForCompanyReportService->generate();

        $fileName = 'real-costs-for-company.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->stream($callback, 200, $headers);
    }
}
