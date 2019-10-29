<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\GusApi;
use GusApi\ReportTypes;

class CompanyInfoController extends Controller
{
    use ApiResponsesTrait;

    public function byNip($nip)
    {
        $gus = new GusApi(env('GUS_API_KEY'));

        try {
            $gus->login();
            $reports = $gus->getByNip($nip);
            $reportType = ReportTypes::REPORT_PUBLIC_LAW;
            $data = $gus->getFullReport($reports[0], $reportType)[0];
        } catch (InvalidUserKeyException $e) {
            $data['error'] = 'Bad user key';
        } catch (\GusApi\Exception\NotFoundException $e) {
            $data['error'] =  'No data found';
        }

        return $data;
    }
}
