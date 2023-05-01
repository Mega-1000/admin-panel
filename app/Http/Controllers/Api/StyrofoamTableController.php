<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DeliverAddressNotFoundException;
use App\Repositories\Products;
use App\Services\StyrofoarmService;
use Illuminate\Database\Eloquent\Collection;
class StyrofoamTableController
{
    public function __construct(
        private readonly StyrofoarmService $styrofoarmService
    )
    {
    }

    /**
     * @throws DeliverAddressNotFoundException
     */
    public function __invoke(string $postalCode): array
    {
        return $this->styrofoarmService->getStyrofoarmsByFirms();
    }
}
