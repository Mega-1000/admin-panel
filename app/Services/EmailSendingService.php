<?php namespace App\Services;

use App\Enums\EmailSettingsEnum;
use App\Entities\Order;
use App\Entities\AllegroOrder;
use App\Entities\EmailSending;
use App\Entities\EmailSetting;
use App\Mail\MailSending;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\EmailTagHandlerHelper;
use App\Facades\Mailer;
use DateTime;

class EmailSendingService
{
    /**
     * Add new scheduled email with given status
     *
     * @param  Order  $order
     * @param  string $status - EmailSettingsEnum
     *
     * @return bool
     */
    public function addNewScheduledEmail(Order $order, string $status = EmailSettingsEnum::NEW): bool
    {
        if (empty($order)) {
            abort(404);
        }
        $statusEnum = EmailSettingsEnum::coerce($status);
        
        if($statusEnum === null) {
            return false;
        }

        $emailSetting = EmailSetting::where('status', $statusEnum->key)->get();
        foreach($emailSetting as $setting) {
            $this->saveScheduledEmail($order, $setting);
        }

        return true;
    }
    public function addScheduledEmail(Order $order, int $labelID): bool
    {
        if (empty($order)) {
            abort(404);
        }

        $status = EmailSettingsEnum::coerce($labelID);
        if( $status === null ) return false;
        $statusName = str_replace('LABEL_', '', $status->key);

        $emailSetting = EmailSetting::where('status', $statusName)->get();

        foreach($emailSetting as $setting) {
            $this->saveScheduledEmail($order, $setting);
        }

        return true;
    }

    public function saveScheduledEmail(Order $order, EmailSetting $setting): int
    {
        $file = $this->generateAttachment($setting->content);
        
        $sending = new EmailSending();
        $sending->order_id = $order->id;
        $sending->email_setting_id = $setting->id;
        $sending->email = $this->getEmail($order);
        $sending->title = $setting->title;
        $sending->content = $this->generateContent($setting->content, $file);
        $sending->attachment = $file;

        if($setting->status === 'PICKED_UP_2') {
            $nextBusinessDay = Carbon::now()->nextBusinessDay()->startOfDay()->addHours(7)->toDateTimeString();
            $sending->scheduled_date = $nextBusinessDay;
        } else {
            $sending->scheduled_date = $this->getDate($setting);
        }
        $sending->send_date = null;
        $sending->message_send = false;
        $sending->save();

        return $sending->id;
    }


    public function getEmail(Order $order): string
    {
        if($order->allegro_form_id){
            $allegroOrder = AllegroOrder::where('order_id',$order->allegro_form_id)->get();
            return $allegroOrder->first()->buyer_email;
        }

        return $order->customer->login;
    }


    public function getDate(EmailSetting $setting): string
    {
        $date = new DateTime();
        $date->modify('+'.$setting->time.' minute');
        return $date->format('Y-m-d H:i:s');
    }

    public function generateContent(string $content, ?string $file = null): string
    {
        $text = $this->getStringBetween($content, '[text]', '[/text]');
        $link = $this->getStringBetween($content, '[link]', '[/link]');

        if($text) {
            $content = $this->getText($content,$text);
        }
        if($link) {
            $content = $this->getLink($content,$link);
        }
        if($file) {
            $content = str_replace("[file]".$file."[/file]", "", $content);
        }
        return $content;
    }


    public function generateAttachment(string $content): string
    {
        $file = $this->getStringBetween($content, '[file]', '[/file]');
        return $file;
    }

    public function getText(string $content, string $text): string
    {
        $txt = file_get_contents($text);
        $msg = str_replace("[text]".$text."[/text]", $txt, $content);
        return $msg;
    }

    public function getLink(string $content, string $link): string
    {
        $l = explode("|",$link);
        $txt = '<a href="'.$l[0].'">'.$l[1].'</a>';
        $msg = str_replace("[link]".$link."[/link]", $txt, $content);
        return $msg;
    }

    public function getStringBetween(string $string, string $start, string $end): string {
        $string = ' ' . $string;
        $ini = strpos($string, $start);

        if ($ini == 0) return '';

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }


    public function sendScheduledEmail(): bool
    {
        $now = new Carbon();
        $sending = EmailSending::where( 'scheduled_date', '<=', $now->toDateTimeString() )->where('message_send', 0)->get();
        foreach($sending as $send) {
            $this->sendEmail($send, $now);
        }

        return true;
    }

    public function sendEmail(EmailSending $send, Carbon $now): bool
    {
        try {

            $order = Order::find( $send->order_id );
            $emailTagHandlerHelper = new EmailTagHandlerHelper();
            $msg = $emailTagHandlerHelper->parseTags($order, $send->content);
            Mailer::create()
                ->to($send->email)
                ->send(new MailSending($send->title, $msg, $send->attachment));
            
            $send->message_send = 1;
            $send->send_date = $now->toDateTimeString();
            $send->save();

            return true;
        } catch (Exception $exception) {
            Log::error('Email was not sent due to. Error: ' . $exception->getMessage());
            
            return false;
        }
    }

}
