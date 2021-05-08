<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entities\LabelLog;
use App\Enums\LabelLogType;

class LabelsHelper
{
    const FINISH_LOGISTIC_LABEL_ID = 68;
    const TRANSPORT_SPEDITION_INIT_LABEL_ID = 103;
    const WAIT_FOR_SPEDITION_FOR_ACCEPT_LABEL_ID = 107;
    const VALIDATE_ORDER = 45;
    const SEND_TO_WAREHOUSE_FOR_VALIDATION = 52;
    const WAIT_FOR_WAREHOUSE_TO_ACCEPT = 47;

    public static function prepareLabelLogsContent(int $orderId): string
    {
        $labelLogs = LabelLog::where('order_id', $orderId)->get();
        $labelLogsContent = '';

        foreach($labelLogs as $labelLog) {
            $labelLogType = $labelLog->type == LabelLogType::ATTACH ? 'dodał' : 'usunął';
            $labelLogConsequence = $labelLog->has_consequence ? 'ZE SKUTKIEM' : 'BEZ SKUTKU';
            $labelLogsContent .= "{$labelLog->action_at} Użytkownik: {$labelLog->user->name} {$labelLogType} etykietę {$labelLog->label->name} [{$labelLogConsequence}] &#10;";
        }

        return $labelLogsContent;
    }
}
