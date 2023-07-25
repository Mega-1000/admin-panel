<?php

namespace App\Helpers;

use App\Helpers\interfaces\iCourier;
use App\Integrations\Jas\Jas;

class JasCourier implements iCourier
{
    /**
     * @param $package
     */
    public function checkStatus ($package): void
    {
        $integration = new Jas($this->config['jas']);

        $userId = $integration->login();
        if ($userId === false) {
            return;
        }
        $status = $integration->getPackageStatus($userId, $package->letter_number);

        if (
            preg_match('/DOSTARCZONO/', $status, $matches)
            || preg_match('/ZAKOŃCZONO/', $status, $matches)
            || $status === 'DOSTARCZONO'
            || $status === 'ZAKOŃCZONO'
        ) {
            $package->status = 'DELIVERED';
            $package->save();
            return;
        }
        if (
            preg_match('/TRANSPORT/', $status, $matches)
            || preg_match('/MAGAZYN/', $status, $matches)
            || $status == 'TRANSPORT'
            || $status == 'MAGAZYN'
        ) {
            $package->status = 'SENDING';
            $package->save();
        }
    }
}
