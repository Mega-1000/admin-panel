<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport;

use App\Domains\DelivererPackageImport\Services\DelivererImportLogService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DelivererImportLogger
{
    public const FILE_LOGS_DIRECTORY = 'app/public/deliverer-logs';

    public const FILE_LOG_EXTENSION = 'xlsx';

    /* @var $logger Logger */
    private $logger;

    private $delivererImportLogService;

    private $logFileName;

    public function __construct(DelivererImportLogService $delivererImportLogService)
    {
        $this->delivererImportLogService = $delivererImportLogService;
    }

    public function setLogFileName(string $fileName): void
    {
        $this->logFileName = $fileName;

        $this->logger = new Logger($this->getLogFileBaseName(), [
            new StreamHandler($this->getFileLogPath()),
        ]);
    }

    public function logInfo($message): void
    {
        $this->logger->info($message);
    }

    public function logError($message): void
    {
        $this->logger->error($message);
    }

    public function logWarning($message): void
    {
        $this->logger->warning($message);
    }

    private function getFileLogPath(): string
    {
        return storage_path(self::FILE_LOGS_DIRECTORY . '/' . $this->logFileName);
    }

    private function getLogFileBaseName(): string
    {
        return pathinfo($this->logFileName, PATHINFO_FILENAME);
    }
}
