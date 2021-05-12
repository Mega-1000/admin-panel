<?php
namespace App\Http\Controllers\Api;

use App\Entities\TrackerLogs;
use App\Http\Controllers\Controller;

class TrackerLogsController extends Controller
{
  public function index()
  {
      return TrackerLogs::get();
  }
}
