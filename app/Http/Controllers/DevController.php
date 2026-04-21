<?php

namespace App\Http\Controllers;

use App\Jobs\CheckPriceChangesInProductsJob;
use App\Facades\Mailer;
use Illuminate\Support\Facades\Log;
use App\Mail\TestMail;

class DevController extends Controller
{

    public function test()
    {
      Log::info('TestEmailJob started');
      Mailer::notification()
          ->to('info@mega1000.pl')
          ->send(new TestMail());
      Log::info('TestEmailJob send');
    }
}
