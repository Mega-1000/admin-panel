<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Services;

use App\Repositories\DelivererImportRepositoryEloquent;
use Illuminate\Support\Str;

class DelivererImportLogService
{
    private $delivererImportRepository;

    public function __construct(DelivererImportRepositoryEloquent $delivererImportRepository)
    {
        $this->delivererImportRepository = $delivererImportRepository;
    }

    public function createLog(int $delivererId, string $fileName): string
    {
        $log = $this->delivererImportRepository->create([
            'deliverer_id' => $delivererId,
            'originalFileName' => $fileName,
            'importFileName' => $this->generateLogFileName(),
        ]);

        return $log->importFileName;
    }

    private function generateLogFileName(): string
    {
        return Str::random(16) . '.txt';
    }
}
