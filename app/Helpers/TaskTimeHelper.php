<?php

namespace App\Helpers;

use App\Entities\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskTimeHelper
{

    public static function getFirstAvailableTime($duration, $data = false)
    {
        if (!$data) {
            $date = Carbon::now();
            $date->setTime(8, 0);
            $data = [
                'start' => $date->toDateTimeString(),
                'end' => $date->addMinutes($duration)->toDateTimeString(),
                'id' => User::OLAWA_USER_ID,
                'user_id' => User::OLAWA_USER_ID
            ];
            Log::notice('Sprawdzam daty zadania', ['dataStart' => $data['start'], 'dataStop' => $data['end']]);
        }

        $allow = false;
        while (!$allow) {
            $allow = self::allowTaskMove($data);
            if ($allow) {
                return $data;
            }
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $data['start'])->addMinutes(5);
            $dateMax = Carbon::today();
            $dateMax->setTime(15, 30);
            if ($date->greaterThan($dateMax)) {
                $date->addDay();
                $date->setTime(8, 0);
            }
            $data['start'] = $date->toDateTimeString();
            $data['end'] = $date->addMinutes($duration)->toDateTimeString();
            Log::notice('Sprawdzam daty zadania', ['dataStart' => $data['start'], 'dataStop' => $data['end']]);
        }
    }

    public static function allowTaskMove($data)
    {
        $tasks = Task::with(['taskTime'])->whereNull('parent_id')->whereHas('taskTime', function ($query) use ($data) {
            $dateStart = new Carbon($data['start']);
            $dateEnd = new Carbon($data['end']);
            $query->whereRaw('((`date_start` BETWEEN "' . $dateStart->addMinute()->toDateTimeString() . '" AND "' . $dateEnd->subMinute()->toDateTimeString() . '" OR `date_end` BETWEEN "' . $dateStart->addMinute()->toDateTimeString() . '" AND "' . $dateEnd->subMinute()->toDateTimeString() . '") OR ("' . $dateStart->addMinute()->toDateTimeString() . '" BETWEEN `date_start` AND `date_end` OR "' . $dateEnd->subMinute()->toDateTimeString() . '" BETWEEN `date_start` AND `date_end` ))');
        })->where([
            ['id', '!=', $data['id'] !== null ? $data['id'] : null],
            ['user_id', '=', $data['user_id']]
        ])->count();
        return $tasks == 0;
    }
}
