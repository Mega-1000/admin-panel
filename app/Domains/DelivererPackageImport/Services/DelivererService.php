<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Services;

use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;
use App\Repositories\DelivererRepositoryEloquent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use \Symfony\Component\HttpFoundation\File\File;

class DelivererService
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

    public function findDeliverer(int $delivererId): ?Deliverer
    {
        return $this->delivererRepository->find($delivererId);
    }

    public function getDelivererByName(string $name): ?Deliverer
    {
        return $this->delivererRepository->findByField('name', $name)->first();
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

        $this->delivererImportRuleRepository->removeDelivererImportRules($deliverer);
        $this->delivererImportRuleRepository->saveImportRules($delivererImportRules);
    }

    public function saveFileToImport(UploadedFile $file): array
    {
        $fileName = Str::random(40) . '.csv';

        return [
            'oldFileName' => $file->getClientOriginalName(),
            'file' => $file->move(Storage::path('user-files/transport/'), $fileName),
        ];
    }

    public function updateDeliverer(
        Deliverer $deliverer,
        string $name,
        array $importRules
    ): bool {
        $this->delivererImportRuleRepository->removeDelivererImportRules($deliverer);

        $this->saveDelivererImportRules(
            $deliverer,
            $importRules
        );

        return $deliverer->update(['name' => $name]);
    }

    public function deleteDeliverer(Deliverer $deliverer): void
    {
        $this->delivererImportRuleRepository->removeDelivererImportRules($deliverer);
        $this->delivererRepository->removeDeliverer($deliverer);
    }
}
