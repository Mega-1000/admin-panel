<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\TrackerLog as TrackLogModel;
use Illuminate\Database\Eloquent\Collection;

class TrackerLog
{
    /**
     * Get Trake rLogs
     * @param array $data
     * @return Collection<TrackLogModel>
     */
    public static function getTrackerLogs(array $data): Collection
    {
        return TrackerLog::whereDate('created_at', '>=', $data['start'])
            ->whereDate('updated_at', '<=', $data['end'])
            ->get();
    }
}
