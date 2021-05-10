<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\LabelLog;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\RemoveLabelJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LabelLogService
{
    public function saveLabelLog(int $orderId, int $labelId, string $type, bool $hasConsequence): LabelLog
    {
        return LabelLog::create([
            'order_id' => $orderId,
            'label_id' => $labelId,
            'type' => $type,
            'has_consequence' => $hasConsequence,
            'user_id' => Auth::user()->id,
            'action_at' => Carbon::now()
        ]);
    }
}
