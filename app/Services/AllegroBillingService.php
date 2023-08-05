<?php

namespace App\Services;

use App\Helpers\AllegroApiHelper;
use App\Helpers\DateHelper;
use Carbon\Carbon;

class AllegroBillingService extends AllegroApiService 
{
    protected $auth_record_id = 3;

    public function getBillingEntriesFromYesterday(): array|false 
    {
        list($startDate, $endDate) = DateHelper::getYesterdayStartAndEnd();

        return $this->getBillingEntriesBetweenDates($startDate, $endDate);
    }

    public function getBillingEntriesBetweenDates(Carbon $startDate, Carbon $endDate): array|false
    {
        $limit = 100;
        $offset = 0;

        $queryParams = AllegroApiHelper::getDatesArray($startDate, $endDate);
        $queryParams['limit'] = $limit;

        $billingEntries = [];

        do {
            $queryParams['offset'] = $offset;
            
            $url = $this->getRestUrlWithQuery('/billing/billing-entries', $queryParams);

            if (!($response = $this->request('GET', $url, []))) {
                if (count($billingEntries) === 0) {
                    return false;
                }

                break;
            }

            $offset += $limit;

            if (count($response['billingEntries']) !== $limit) {
                break;
            }

            $billingEntries = array_merge($billingEntries, $response['billingEntries']);
        } while (true);

        return $billingEntries;
    }
}
