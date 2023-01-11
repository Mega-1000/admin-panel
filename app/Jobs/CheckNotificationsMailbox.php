<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Entities\OrderWarehouseNotification;
use App\Http\Controllers\Api\OrderWarehouseNotificationController;
use App\Http\Requests\Api\OrderWarehouseNotification\AcceptShipmentRequest;

class CheckNotificationsMailbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $host = '{s104.linuxpl.com:993/imap/ssl}INBOX';
    private $user = 'awizacje@ephpolska.pl';
    private $password = '1!Qaa2@Wss';
    private $imap;

    // order should be same as in order-status-changed-to-dispatch.blade.php
    public $emailElements = [
        'accept' => [
            'code' => 'AAZPP',
            'value' => null,
        ],
        'cancel' => [
            'code' => 'OAZPP',
            'value' => null,
        ],
        'from' => [
            'code' => 'DOOF',
            'value' => null,
        ],
        'to' => [
            'code' => 'DODF',
            'value' => null,
        ],
        'name' => [
            'code' => 'OOZO',
            'value' => null,
        ],
        'phone' => [
            'code' => 'NTOOZO',
            'value' => null,
        ],
        'phoneToDriver' => [
            'code' => 'NTDK',
            'value' => null,
        ],
        'comments' => [
            'code' => 'U',
            'value' => null,
        ],
        'released' => [
            'code' => 'TZW',
            'value' => null,
        ],
        'isVisibleForClient' => [
            'code' => 'WF',
            'value' => null,
        ],
    ];

    public $emailElementsNumber;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emailElementsNumber = count($this->emailElements);
        $this->imap = imap_open($this->host, $this->user, $this->password)
                or die('unable to connect: ' . imap_last_error());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // $mails = imap_search($this->imap, 'UNSEEN');
        $mails = imap_search($this->imap, 'ALL');
        
        if ($mails) {
            rsort($mails);

            foreach ($mails as $mailNumber) {
                $headers = imap_fetch_overview($this->imap, $mailNumber, 0);
                $subject = quoted_printable_decode($headers[0]->subject);
                $header = imap_headerinfo($this->imap, $mailNumber);
                $from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                // after number can be _ or \s then number
                $isOrderId = preg_match("/nr\._?\s?(\d+)/", $subject, $matches);
                // get plain text
                $body = imap_fetchbody($this->imap, $mailNumber, 1.1);
                // check if is correct length min. 100 char.
                if( strlen($body) < 100 ) {
                    // get rest of text
                    $body = imap_fetchbody($this->imap, $mailNumber, 1);
                    if (strlen($body) < 100) continue;
                }
                $msg = trim(quoted_printable_decode($body));
                // no order ID in subject
                if(!$isOrderId) $isOrderId = preg_match("/Nr\soferty:\s?(\d+)/mi", $msg, $matches);
                if(!$isOrderId) continue;

                $orderId = $matches[1];

                $pattern = "/";
                foreach($this->emailElements as $element) {
                    // search by given prefix that means (WDOO): is mandatory, and ; at the end is mandatory too
                    $pattern .= "\({$element['code']}\):\s?(.*);|";
                }
                $pattern = rtrim($pattern, '|');
                $pattern .= "/mi";
                $numberOfMatches = preg_match_all($pattern, $msg, $elementsMatches);

                // all elements should exist
                if($numberOfMatches < $this->emailElementsNumber) continue;

                $i = 0;
                foreach($this->emailElements as &$element) {
                    // +1 means that index of matches start from 1, then get first occurrence in second array, that has indexed pattern
                    $element['value'] = $elementsMatches[$i + 1][$i];
                    $i++;
                }
                $order = Order::findOrFail($orderId);
                // get notification ID or create one
                $dataArray = [
                    'order_id' => $orderId,
                    'warehouse_id' => $order->warehouse_id,
                    'waiting_for_response' => true,
                ];
                $notification = OrderWarehouseNotification::where($dataArray)->first();

                if (!$notification && !$order->isOrderHasLabel(Label::WAREHOUSE_REMINDER)) {
                    $subject = "Prośba o potwierdzenie awizacji dla zamówienia nr. " . $orderId;
                    $notification = OrderWarehouseNotification::create($dataArray);
                }

                // make params and handle form accept / deny
                $params = [
                    'order_id' => $orderId,
                    'warehouse_id' => $order->warehouse_id,
                    'realization_date_from' => $this->emailElements['from']['value'],
                    'realization_date_to' => $this->emailElements['to']['value'],
                    'contact_person' => $this->emailElements['name']['value'],
                    'contact_person_phone' => $this->emailElements['phone']['value'],
                    'driver_contact' => $this->emailElements['phoneToDriver']['value'],
                    'customer_notices' => $this->emailElements['comments']['value'],
                ];
                // make request with given $params and validator
                $request = new AcceptShipmentRequest($params);
                $validator = Validator::make($request->all(), $request->rules());
                $request->setValidator($validator);
                echo '<pre>' , print_r($this->emailElements) , '</pre>';
                echo '<pre>' , print_r($params) , '</pre>';
                // if($validator->fails()) {

                //     $errMsgTemplate = '<body><h3>Proszę poprawić następujące błędy:</h3>';
                //     $validationErrors = $validator->errors();
                //     foreach ($validationErrors->all() as $error) {
                //         $errMsgTemplate .= "<p>$error</p>";
                //     }
                //     $errMsgTemplate .= '</body>';
                //     $subject = 'Wykryto błędy w Pańskim formularzu:';
                //     imap_mail($from, $subject, $errMsgTemplate);
                // } else {
                //     $orderWarehouseNotification = App::make(OrderWarehouseNotificationController::class);

                //     if($this->emailElements['accept'] == 1) $orderWarehouseNotification->accept($request, $notification->id);
                //     else $orderWarehouseNotification->deny($request, $notification->id);
                // }
            }
        }
        imap_close($this->imap);
    }
}
