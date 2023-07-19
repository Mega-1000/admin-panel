<?php

namespace App\Helpers;

use App\Enums\PackageStatus;
use App\Helpers\interfaces\ICourier;
use App\Integrations\Pocztex\ElektronicznyNadawca;
use App\Integrations\Pocztex\envelopeStatusType;
use App\Integrations\Pocztex\getEnvelopeContentShort;
use App\Integrations\Pocztex\getEnvelopeStatus;
use App\Integrations\Pocztex\statusType;
use Illuminate\Support\Facades\Log;

class PocztexCourier implements ICourier
{
    /**
     * @param $package
     */
    public function checkStatus($package): void
    {
        $integration = new ElektronicznyNadawca();
        $request = new getEnvelopeContentShort();
        $request->idEnvelope = $package->sending_number;
        $status = $integration->getEnvelopeContentShort($request);

        if (!$status || !isset($status->przesylka) || $status->przesylka->status !== statusType::POTWIERDZONA) {
            Log::notice('Błąd ze statusem przesyłki ' . $package->order_id,
                (array)$status->przesylka
            );
            return;
        }

        $request = new getEnvelopeStatus();
        $request->idEnvelope = $package->sending_number;
        $status = $integration->getEnvelopeStatus($request);

        switch ($status->envelopeStatus) {
            case envelopeStatusType::PRZYJETY:
                $package->status = PackageStatus::DELIVERED;
                break;
            case envelopeStatusType::WYSLANY:
                $package->status = PackageStatus::SENDING;
                break;
        }
        if ($package->isDirty()) {
            $package->save();
        }
    }
}
