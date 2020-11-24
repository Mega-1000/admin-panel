<?php

declare(strict_types=1);

namespace App\Services;

use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;
use App\Repositories\DelivererRepositoryEloquent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use \Symfony\Component\HttpFoundation\File\File;

class TransportService
{
    private $delivererRepository;

    private $delivererImportRuleRepository;

    public function __construct(
        DelivererRepositoryEloquent $delivererRepository,
        DelivererImportRuleRepositoryEloquent $delivererImportRuleRepository
    ) {
        $this->delivererRepository = $delivererRepository;
        $this->delivererImportRuleRepository = $delivererImportRuleRepository;
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createDeliverer(string $name)
    {
        return $this->delivererRepository->create([
            'name' => $name
        ]);
    }

    public function saveDelivererImportRules(Deliverer $deliverer, array $delivererImportRules): void
    {
        if (empty($delivererImportRules)) {
            return;
        }

        $this->delivererImportRuleRepository->removeAllDelivererImportRules($deliverer);
        $this->delivererImportRuleRepository->saveImportRules($delivererImportRules);
    }

    public function getDelivererImportRules()
    {

    }

    public function saveFileToImport(UploadedFile $file): File
    {
        $fileName = Str::random(40) . '.csv';

        return $file->move(Storage::path('user-files/transport/'), $fileName);
    }
}
