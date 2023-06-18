<?php

namespace App\Helpers;

class AllegroBillingImportHelper
{
    private array $courierPatterns = [
        '/DPD - Kurier opłaty dodatkowe/',
        '/Inpost - opłaty dodatkowe/',
    ];

    private array $courierChargePatterns = [
        '/Allegro Paczkomaty InPost/',
        '/Przesyłka DPD/',
        '/UPS operator - opłaty podstawowe/',
    ];

    private string $numberPattern = '/Numer nadania: (\d+)/';

    /**
     * @param string $details
     * @return string|null
     */
    public function extractTrackingNumber(string $details): ?string
    {
        return preg_match($this->numberPattern, $details)
            ? explode(',', preg_replace($this->numberPattern, '$1', $details))[0]
            : null;
    }

    /**
     * @param string $details
     * @return bool
     */
    public function hasCourierMatch(string $details): bool
    {
        foreach ($this->courierPatterns as $pattern) {
            if (preg_match($pattern, $details)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $details
     * @return bool
     */
    public function hasCourierChargeMatch(string $details): bool
    {
        foreach ($this->courierChargePatterns as $pattern) {
            if (preg_match($pattern, $details)) {
                return true;
            }
        }

        return false;
    }
}
