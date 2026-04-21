<?php

namespace App\Http\Controllers;

use App\Entities\ChatAuctionFirm;
use App\Entities\Firm;
use App\Entities\Order;
use App\Helpers\LocationHelper;
use App\Helpers\SMSHelper;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function sendSms(Request $request, string $token)
    {
        $firm = ChatAuctionFirm::where('token', $token)->first();

        $employee = LocationHelper::getNearestEmployeeOfFirm(Order::find($request->query('orderId'))->customer, $firm->firm);

        SMSHelper::sendSms(
            576205389,
            "EPH Polska",
            $request->query('message'),
        );
    }
}
