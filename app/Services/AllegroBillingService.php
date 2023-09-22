<?php

namespace App\Services;

use App\Factory\AllegroBilling\ImportAllegroBillingDTOFactory;
use Carbon\Carbon;

class AllegroBillingService extends AllegroApiService
{

    protected $auth_record_id = 2;

    public function getAllBillingsData(Carbon $minDate = null, int $daysInSingleRow = 7, bool $truncateData = false): void
    {
        $limit = 100;

        $minDate = $minDate === null ? (new Carbon())->addDays(-120) : $minDate;
        $lastDay = clone $minDate;

        $data = [
            'offset' => 0,
            'limit' => $limit,
            'occurredAt.gte' => $minDate->startOfDay()->format('Y-m-d\TH:i:s.v\Z'),
            'occurredAt.lte' => $minDate->addDays($daysInSingleRow > 1 ? $daysInSingleRow : 0)->endOfDay()->format('Y-m-d\TH:i:s.v\Z')];


        $importBillingService = app(ImportAllegroBillingService::class);
        if ($truncateData === true) {
            $importBillingService->clearAllegroGeneralExpenses();
        }
        $today = now()->addDays($daysInSingleRow);
        do {
            $response = $this->getData($data);
            $from = $data['offset'];
            $data['offset'] += $limit;
            $records = [];
            if (is_array($response) && array_key_exists('billingEntries', $response) && count($response['billingEntries'])) {
                $recordsCount = count($response['billingEntries']);
                echo "Ładowanie danych";
                $daysInfo = " z dnia " . $minDate->format('Y-m-d') . ' ';
                if ($daysInSingleRow > 1) {
                    $daysInfo = " z dni " . $lastDay->format('Y-m-d') . ' do ' . $minDate->format('Y-m-d');
                }

                echo $daysInfo . ' rekordów ' . $from . ' - ' . ($from + min($recordsCount, $limit)) . PHP_EOL;

                $records = ImportAllegroBillingDTOFactory::createFromRestApi($response['billingEntries']);
                $importBillingService->importAppendData($records);
            }

            if (count($records) === 0 || count($records) < $limit) {
                $lastDay = clone $minDate;
                $lastDay->addDay();
                $data['occurredAt.gte'] = $lastDay->startOfDay()->format('Y-m-d\TH:i:s.v\Z');
                $data['occurredAt.lte'] = $minDate->addDays($daysInSingleRow)->endOfDay()->format('Y-m-d\TH:i:s.v\Z');
                $data['offset'] = 0;
            }
        } while ($minDate->endOfDay()->getTimestamp() <= $today->endOfDay()->getTimestamp());

    }


    private function getData(array $data)
    {
        $url = $this->getRestUrl('/billing/billing-entries?' . http_build_query($data));
        return $this->request('GET', $url, []);
    }

}
