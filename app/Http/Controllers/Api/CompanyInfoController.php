<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\ReportTypes;

class CompanyInfoController extends Controller
{

    use ApiResponsesTrait;

    public function byNip($nip)
    {
        $gus = new GusApi(config('integrations.gus.api_key'));

        try {
            $gus->login();
            $reports = $gus->getByNip($nip);
            $reportTypeJDG = ReportTypes::REPORT_PERSON_CEIDG;
            $reportType = ReportTypes::REPORT_ORGANIZATION;
            $data = $gus->getFullReport($reports[0], $reportType);
            if (count($data) == 0) {
                $data = $gus->getFullReport($reports[0], $reportTypeJDG)[0];
                $data['type'] = 'JDG';
            } else {
                $data = $gus->getFullReport($reports[0], $reportType)[0];
                $data['type'] = 'SP';
            }
        } catch (InvalidUserKeyException $e) {
            $data['error'] = 'Bad user key';
        } catch (NotFoundException $e) {
            $data['error'] = 'No data found';
        }

        return $data;
    }
}
