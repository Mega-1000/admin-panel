<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\TrackerLogs;
use Illuminate\Database\Eloquent\Collection;

class TrackerLog
{
    /**
     * Get Trake rLogs
     * @param $data
     * @return Collection<TrackerLogs>
     */
    public static function getTrakerLogs(array $data): Collection
    {
        return TrackerLogs::whereDate('created_at', '>=', $data['start'])
                ->whereDate('updated_at', '<=', $data['end'])
                ->get();
    }
}