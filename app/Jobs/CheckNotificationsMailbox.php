<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderManualNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Entities\OrderWarehouseNotification;
use App\Facades\Mailer;
use App\Http\Controllers\Api\OrderWarehouseNotificationController;
use App\Http\Requests\Api\OrderWarehouseNotification\DenyShipmentRequest;
use App\Http\Requests\Api\OrderWarehouseNotification\AcceptShipmentRequest;

class CheckNotificationsMailbox implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $host;
    private $user;
    private $password;
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
        'chat' => [
            'code' => 'AWDK',
            'value' => null,
        ],
        'released' => [
            'code' => 'TZW',
            'value' => null,
        ],
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        $this->host = '{'.config('notifications.host').':993/imap/ssl}INBOX';
        $this->user = config('notifications.username');
        $this->password = config('notifications.password');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        try {
            $this->imap = imap_open($this->host, $this->user, $this->password);
        } catch (\Exception $e){
            Log::error('Problem with imap_open',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            return false;
        }

        $mails = imap_search($this->imap, 'UNSEEN');

        if ($mails) {

            foreach ($mails as $mailNumber) {
                $headers = imap_fetch_overview($this->imap, $mailNumber, 0);
                $subject = quoted_printable_decode($headers[0]->subject);
                $header = imap_headerinfo($this->imap, $mailNumber);
                $from = $header->from[0]->mailbox . "@" . $header->from[0]->host;

                // any number between 3 and 20 length
                $isOrderId = preg_match("/(\d{3,20})/", $subject, $matches);
                // get plain text
                $body = imap_fetchbody($this->imap, $mailNumber, 1.1);
                // check if is correct length min. 100 char.
                if (strlen($body) < 100) {
                    // get rest of text
                    $body = imap_fetchbody($this->imap, $mailNumber, 1);
                    if (strlen($body) < 100) continue;
                }
                $msg = trim(quoted_printable_decode($body));
                // no order ID in subject
                if (!$isOrderId) $isOrderId = preg_match("/Nr\soferty:\s?(\d+)/mi", $msg, $matches);
                if (!$isOrderId) continue;

                $orderId = $matches[1];

                // build pattern
                $pattern = "/";
                foreach ($this->emailElements as $element) {
                    // search by given prefix that means (DOOF): is mandatory, and ; at the end is mandatory too
                    $pattern .= "\({$element['code']}\):\s?(.*)|";
                }
                $pattern = rtrim($pattern, '|');
                $pattern .= "/mi";
                preg_match_all($pattern, $msg, $elementsMatches);

                $i = 0;
                foreach ($this->emailElements as &$element) {
                    if( !isset($elementsMatches[$i + 1]) ) break;
                    // get first value ignores empty values, first value is the newest value of matches
                    $filteredMatches = array_filter($elementsMatches[$i + 1]);
                    $element['value'] = trim( reset($filteredMatches), " \t\n\r\0\x0B*" );
                    $i++;
                }

                $order = Order::findOrFail($orderId);

                $orderWarehouseNotification = App::make(OrderWarehouseNotificationController::class);

                // order start shipment
                $attachments = $this->getAttachments($mailNumber);
                if( !empty($attachments) ) {
                    $params = [
                        'orderId' => $orderId,
                        'isVisibleForClient' => 0,
                    ];
                    // get base64 code from main (first) attachment
                    $mainAttachment = reset($attachments)['attachment'];
                    $uploadedFile = FileHelper::createUploadedFileFromBase64($mainAttachment);
                    $request = new Request($params);
                    $request->files->set('file', $uploadedFile);
                    $res = $orderWarehouseNotification->sendInvoice($request);
                    if($res->getStatusCode() == 200) {
                        $subject = 'Faktura została pomyślnie wysłana dla oferty nr: '.$orderId;
                        $email = new OrderManualNotification($subject, $subject, '');
                        Mailer::notification()->to($from)->send($email);
                    }
                }
                if($this->emailElements['released']['value'] == 1) {
                    $params = [
                        'orderId' => $orderId,
                    ];
                    $request = new Request($params);
                    $res = $orderWarehouseNotification->changeStatus($request);
                    if($res->getStatusCode() == 200) {
                        $subject = 'Status został pomyślnie zmieniony jako wydany towar dla oferty nr: '.$orderId;
                        $email = new OrderManualNotification($subject, $subject, '');
                        Mailer::notification()->to($from)->send($email);
                    }
                }
                $dataArray = [
                    'order_id' => $orderId,
                    'warehouse_id' => $order->warehouse_id,
                    'waiting_for_response' => true,
                ];
                $notification = OrderWarehouseNotification::where($dataArray)->first();
                if (!$notification) continue;

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
                $validator = Validator::make($request->all(), $request->rules(), [], $request->attributes());
                $request->setValidator($validator);

                if ($validator->fails()) {
                    $subject = 'Wykryto błędy w Pańskim formularzu dot. oferty nr: '.$orderId;
                    $msgHeader = 'Proszę poprawić następujące błędy, oraz ponownie wysłać w pełni uzupełniony formularz:';
                    $errMsgTemplate = '';
                    $validationErrors = $validator->errors();
                    foreach ($validationErrors->all() as $error) {
                        $errMsgTemplate .= "<p>$error</p>";
                    }
                    $email = new OrderManualNotification($subject, $msgHeader, $errMsgTemplate);
                    Mailer::notification()->to($from)->send($email);
                } else {
                    if ($this->emailElements['accept']['value'] == 1) {
                        $res = $orderWarehouseNotification->accept($request, $notification->id);
                        $subject = 'Formularz został zaakceptowany do realizacji dla oferty nr: '.$orderId;
                    } else {

                        $params = [
                            'order_id' => $orderId,
                            'warehouse_id' => $order->warehouse_id,
                            'customer_notices' => $this->emailElements['comments']['value'],
                        ];
                        
                        $request = new DenyShipmentRequest($params);
                        $validator = Validator::make($request->all(), $request->rules(), [], $request->attributes());
                        $request->setValidator($validator);
                        $res = $orderWarehouseNotification->deny($request, $notification->id);
                        $subject = 'Formularz został odrzucony do realizacji dla oferty nr: '.$orderId;
                    }
                    if($res->getStatusCode() == 200) {
                        $email = new OrderManualNotification($subject, $subject, '');
                        Mailer::notification()->to($from)->send($email);
                    }
                }
            }
        }
        imap_close($this->imap);
    }
    public function getAttachments(int $mailNumber) {

        $structure = imap_fetchstructure($this->imap, $mailNumber);
        $attachments = array();

        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 0; $i < count($structure->parts); $i++) {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
                if ($structure->parts[$i]->ifdparameters) {
                    foreach ($structure->parts[$i]->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }
                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($this->imap, $mailNumber, $i + 1);
                } else {
                    unset($attachments[$i]);
                }
            }
        }
        return $attachments;
    }
}
