<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Services;

use App\Domains\DelivererPackageImport\DelivererImportLogger;
use App\Repositories\DelivererImportRepositoryEloquent;

class DelivererImportLogService
{
    private $delivererImportRepository;

    public function __construct(DelivererImportRepositoryEloquent $delivererImportRepository)
    {
        $this->delivererImportRepository = $delivererImportRepository;
    }

    public function createLog(int $delivererId, string $fileName, string $uniqueLogFileName): string
    {
        $log = $this->delivererImportRepository->create([
            'deliverer_id' => $delivererId,
            'originalFileName' => $fileName,
            'importFileName' => $uniqueLogFileName . '.' . DelivererImportLogger::FILE_LOG_EXTENSION,
        ]);

        return $log->importFileName;
    }

    public function logFileExists(string $id): bool
    {
        return file_exists($this->getLogFilePath($id));
    }

    public function getLogFilePath(string $id): string
    {
        return storage_path(
            DelivererImportLogger::FILE_LOGS_DIRECTORY . '/' . $id . '.' . DelivererImportLogger::FILE_LOG_EXTENSION
        );
    }
}
