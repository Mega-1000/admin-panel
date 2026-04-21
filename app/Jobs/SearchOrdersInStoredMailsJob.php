<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use App\Services\Label\AddLabelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchOrdersInStoredMailsJob extends Job implements ShouldQueue
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        OrderMessageRepository $messageRepository,
        OrderPackageRepository $orderPackageRepository,
        OrderRepository        $orderRepository
    )
    {
        $mbox = imap_open(config('mail.imap_inbox'), "info@" . config('app.domain_name'), config('mail.imap_password'));
        $message_count = imap_num_msg($mbox);
        for ($i = 1; $i < $message_count; $i++) {
            $headers = imap_fetchheader($mbox, $i, FT_PREFETCHTEXT);
            $overview = imap_fetch_overview($mbox, $i, 0);
            $body = imap_body($mbox, $i);
            $timestamp = $overview[0]->date;
            if (property_exists($overview[0], 'subject')) {
                $subject = iconv_mime_decode($overview[0]->subject);
            } else {
                $subject = '';
            }
            $data = [];
            $found = false;
            $re = '/\s(\d+)\s?/i';
            preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);
            if (!empty($matches)) {     //found some matching number
                $number = $matches[0][1];
                $orderPackage = $orderPackageRepository->scopeQuery(function ($query) use ($number) {
                    return $query->where("sending_number", $number)
                        ->orWhere("letter_number", $number);
                })->first();
                if (!empty($orderPackage)) {        //found that number as either sending_number or as letter_number
                    $found = true;
                    $data['type'] = "SHIPPING";
                    $data['order_id'] = $orderPackage->order_id;
                } else {
                    $order = $orderRepository->findWhere(["id" => $number])->first();       //found as order id
                    if (!empty($order)) {
                        $found = true;
                        $data['order_id'] = $order->id;
                    }
                }
            }
            if ($found) {
                $emailMessage = DB::table('emails_messages')->where('timestamp', $timestamp)->first();
                if (empty($emailMessage)) {
                    $unique = uniqid();
                    $path = storage_path('app/public/mails/' . $unique . '.eml');
                    file_put_contents($path, $headers . "\n" . $body);
                    DB::table('emails_messages')->insert([
                        [
                            'path' => $unique . '.eml',
                            'timestamp' => $timestamp,
                            'order_id' => $data['order_id'],
                        ]
                    ]);
                    $loopPrevention = [];
                    /** @var Order $order */
                    $order = Order::query()->findOrFail($data['order_id']);
                    AddLabelService::addLabels($order, [138], $loopPrevention, [], Auth::user()?->id);
                }

            }
        }
    }
}



