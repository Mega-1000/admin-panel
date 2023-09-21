<?php

namespace App\Services;

use App\Factory\AllegroBilling\ImportAllegroBillingDTOFactory;

class AllegroBillingService extends AllegroApiService
{

    protected $auth_record_id = 2;

    public function getAllBillingsData(): void
    {
        $limit = 100;

        $data = ['offset' => 0, 'limit' => $limit];


        $importBillingService = app(ImportAllegroBillingService::class);
        $importBillingService->clearAllegroGeneralExpenses();
        do {
            $response = $this->getData($data);

            $data['offset'] += $limit;
            $records = [];
            if (is_array($response) && array_key_exists('billingEntries', $response) && count($response['billingEntries'])) {
                $records = ImportAllegroBillingDTOFactory::createFromRestApi($response['billingEntries']);
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
