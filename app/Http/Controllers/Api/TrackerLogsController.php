<?php
namespace App\Http\Controllers\Api;

use App\Entities\TrackerLogs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackerLogsController extends Controller
{
  public function index()
  {
      return TrackerLogs::get();
  }

    public function new(Request $request)
    {
        $this->validate($request, [
            'time' => 'required',
            'page' => 'required',
        ]);

        $log = new TrackerLogs();
        if(Auth::user()) {
            $log->user_id = Auth::user()->id;
        } else {
            $log->user_id = 1;
        }
        $log->time = $request->time;
        $log->page = $request->page;
        $log->description = '';

        if($log->save()) {
            return response(json_encode(
                $log
            ),200);
        }

        return response(json_encode([
            'error_code' => 500,
            'error_message' => 'BŁĄD'
        ]),500);
    }

    public function update(TrackerLogs $log, Request $request)
    {
        if($request->has('time')) {
            $log->time = $request->time;
        }
        if($request->has('description')) {
            $log->description = $request->description;
        }


        if ($log->update()) {
            return response(json_encode([
                $log
            ]),200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]),500);
    }
}
