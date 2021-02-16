<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderPackageRepository;

class OrderPackageService
{
    protected $orderPackageRepository;

    public function __construct(OrderPackageRepository $orderPackageRepository)
    {
        $this->orderPackageRepository = $orderPackageRepository;
    }

    public function getPackageInfo(int $id)
    {
        return $this->orderPackageRepository->find($id);
    }
}
