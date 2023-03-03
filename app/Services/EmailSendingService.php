<?php namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Enums\EmailSettingsEnum;
use App\Entities\Order;
use App\Entities\AllegroOrder;
use App\Entities\EmailSending;
use App\Entities\EmailSetting;
use App\Mail\MailSending;
use Exception;

use Illuminate\Support\Facades\Log;

class EmailSendingService
{
    public function addNewScheduledEmail($order_id): bool
    {
        $order = Order::find($order_id);
        if (empty($order)) {
            abort(404);
        }

        $emailSetting = EmailSetting::where('status', 'NEW')->get();
        foreach($emailSetting as $setting){
            $this->saveScheduledEmail($order, $setting);
        }

        return true;
    }

    public function addScheduledEmail($order_id, $labelID): bool
    {
        $order = Order::find($order_id);
        if (empty($order)) {
            abort(404);
        }

        if($labelID==EmailSettingsEnum::STATUS_LABELS['PRODUCED']){
            $emailSetting = EmailSetting::where('status', 'PRODUCED')->get();
            foreach($emailSetting as $setting){
                $this->saveScheduledEmail($order, $setting);
            }
        }

        if($labelID==EmailSettingsEnum::STATUS_LABELS['PICKED_UP']){
            $emailSetting = EmailSetting::where('status', 'PICKED_UP')->get();
            foreach($emailSetting as $setting){
                $this->saveScheduledEmail($order, $setting);
            }
        }

        if($labelID==EmailSettingsEnum::STATUS_LABELS['PROVIDED']){
            $emailSetting = EmailSetting::where('status', 'PROVIDED')->get();
            foreach($emailSetting as $setting){
                $this->saveScheduledEmail($order, $setting);
            }
        }

        return true;
    }

    public function saveScheduledEmail($order, $setting): int
    {
        $file = $this->generateAttachment($setting->content);
        
        $sending = new EmailSending();
        $sending->order_id = $order->id;
        $sending->email_setting_id = $setting->id;
        $sending->email = $this->getEmail($order);
        $sending->title = $setting->title;
        $sending->content = $this->generateContent($setting->content,$file);
        $sending->attachment = $file;
        $sending->scheduled_date = $this->getDate($setting);
        $sending->send_date = null;
        $sending->message_send = false;
        $sending->save();

        return $sending->id;
    }


    public function getEmail($order): string
    {
        if($order->allegro_form_id){
            $allegroOrder = AllegroOrder::where('order_id',$order->allegro_form_id)->get();
            return $allegroOrder->first()->buyer_email;
        }

        return $order->customer->login;
    }


    public function getDate($setting): string
    {
        $date = new \DateTime();
        $date->modify('+'.$setting->time.' minute');
        return $date->format('Y-m-d H:i:s');
    }

    public function generateContent($content,$file=null): string
    {
        $text = $this->getStringBetween($content, '[text]', '[/text]');
        $link = $this->getStringBetween($content, '[link]', '[/link]');

        if($text){
            $content = $this->getText($content,$text);
        }
        if($link){
            $content = $this->getLink($content,$link);
        }
        if($file){
            $content = str_replace("[file]".$file."[/file]", "", $content);
        }
        return $content;
    }


    public function generateAttachment($content): string
    {
        $file = $this->getStringBetween($content, '[file]', '[/file]');
        return $file;
    }

    public function getText($content,$text): string
    {
        $txt = file_get_contents($text);
        $msg = str_replace("[text]".$text."[/text]", $txt, $content);
        return $msg;
    }

    public function getLink($content,$link): string
    {
        $l = explode("|",$link);
        $txt = '<a href="'.$l[0].'">'.$l[1].'</a>';
        $msg = str_replace("[link]".$link."[/link]", $txt, $content);
        return $msg;
    }

    public function getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);

        if ($ini == 0) return '';

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }


    public function sendScheduledEmail(): bool
    {
        $date = new \DateTime();
        $now = $date->format('Y-m-d H:i:s');
        $sending = EmailSending::where('scheduled_date', '<=', $now)->where('message_send', 0)->get();
        foreach($sending as $send){
            $this->sendEmail($send,$now);
        }

        return true;
    }

    public function sendEmail($send,$now): bool
    {
        try {
            \Mailer::create()
                ->to($send->email)
                ->send(new MailSending($send->title,$send->content,$send->attachment));
            
            $send->message_send = 1;
            $send->send_date = $now;
            $send->save();

            return true;
        } catch (Exception $exception) {
            Log::error('Email was not sent due to. Error: ' . $exception->getMessage());
            
            return false;
        }
    }

}