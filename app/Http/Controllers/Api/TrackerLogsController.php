<?php

namespace App\Http\Controllers\Api;

use App\Entities\TrackerLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrackerLogsController extends Controller
{
    public function index()
    {
        return TrackerLog::query()->orderBy('id', 'DESC')->get();
    }

    public function new(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'time' => 'required',
            'page' => 'required',
        ]);

        $log = new TrackerLog();
        if ($request->user_id) {
            $log->user_id = $request->user_id;
        } else {
            $log->user_id = 1;
        }
        $log->time = $request->time;
        $log->page = $request->page;
        $log->description = '';

        if ($log->save()) {
            return response(json_encode(
                $log
            ), 200);
        }

        return response(json_encode([
            'error_code' => 500,
            'error_message' => 'BŁĄD'
        ]), 500);
    }

    public function update(TrackerLog $log, Request $request)
    {
        if ($request->has('time')) {
            $log->time = $request->time;
        }
        if ($request->has('description')) {
            $oldDescription = $log->description;
            $oldDescription .= 'Time: ' . $log->time . '<br>';
            $log->description = $oldDescription . $request->description . '<br><br>';
        }


        if ($log->update()) {
            return response(json_encode(
                $log
            ), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }
}
