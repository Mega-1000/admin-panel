<?php

namespace App\Services;

use App\Factory\AllegroBilling\ImportAllegroBillingDTOFactory;

class AllegroBillingService extends AllegroApiService
{

    protected $auth_record_id = 2;

    public function getAllBillingsData(): void
    {
        $data = [];

        $limit = 1000;
        $offset = 0;

        $importBillingService = app(ImportAllegroBillingService::class);
        $importBillingService->clearAllegroGeneralExpenses();
        do {
            $data['limit'] = $limit;
            $data['offset'] = 0;

            $response = $this->getData($data);

            $data['offset'] += $limit;
            $records = [];
            if (array_key_exists('billingEntries', $response) && count($response['billingEntries'])) {
                foreach ($response['billingEntries'] as $billingEntry) {
                    $records[] = ImportAllegroBillingDTOFactory::createFromRestApi($billingEntry);
                }
                $importBillingService->importAppendData($records);
            }
        } while (count($records) > 0);

    }


    private function getData(array $data)
    {
        $url = $this->getRestUrl('/billing/billing-entries?' . http_build_query($data));
        return $this->request('GET', $url, []);
    }

}
