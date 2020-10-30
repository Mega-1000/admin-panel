<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Deliverer;
use App\Repositories\DelivererRepositoryEloquent;

class TransportService
{
    private $delivererRepository;

    public function __construct(DelivererRepositoryEloquent $delivererRepository)
    {
        $this->delivererRepository = $delivererRepository;
    }

    public function getDeliverer(int $delivererId): ?Deliverer
    {
        return $this->delivererRepository->find($delivererId);
    }

    public function getDelivererByName(string $name): ?Deliverer
    {
        return $this->delivererRepository->findByField('name', $name)->first();
    }

    public function updateDeliverer(Deliverer $deliverer, string $name): bool
    {
        return $deliverer->update(['name' => $name]);
    }

    /**
     * @param string $name
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createDeliverer(string $name)
    {
        return $this->delivererRepository->create([
            'name' => $name
        ]);
    }
}
