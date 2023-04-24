<?php

namespace App\Services\Label;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderWarehouseNotification;
use App\Entities\Tag;
use App\Facades\Mailer;
use App\Helpers\EmailTagHandlerHelper;
use App\Mail\LabelAdd;
use App\Mail\OrderMessageMail;
use App\Mail\OrderStatusChangedToDispatchMail;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;

class LabelNotificationService
{
    public static function addLabelSentNotification(Order $order, Label $label): void
    {
        $tmpDate = new DateTime('2022-07-01');
        if ($order->created_at < $tmpDate) {
            return;
        }

        $tags = Tag::all();

        if ($label->message !== null) {
            $message = $label->message;
        } else {
            $message = '';
        }

        $emailTagHandler = new EmailTagHandlerHelper();
        $emailTagHandler->setOrder($order);

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }
        $status = explode('-', $label->name)[0];
        $subject = "Mega1000 - zmieniono status zamówienia: " . $order->id . ' na status: ' . str_replace('-', '', $status);
        try {
            if (strpos($order->customer->login, 'allegromail.pl') || empty($message)) {
                return;
            }
            Mailer::create()
                ->to($order->customer->login)
                ->send(new LabelAdd($subject, $message));
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            Log::error("Problem with mailer: $message", ['class' => $exception->getFile(), 'line' => $exception->getLine(), 'stack' => $exception->getTraceAsString()]);
        }
    }

    public static function orderStatusChangeToDispatchNotification(Order $order, bool $self, ?string $path = null, ?string $packageNumber = null, ?string $pathSecond = null): void
    {

        $warehouse = $order->warehouse;
        if ($warehouse && $warehouse->firm) {
            $warehouseMail = $warehouse->firm->email;
        }
        if (empty($warehouseMail)) {
            Log::notice('Brak adresu mailowego w firmie, lub magazyn nie istnieje', ['line' => __LINE__, 'file' => __FILE__, 'order' => $order->id]);
            return;
        }

        $subject = "Przypomnienie o potwierdzenie awizacji dla zamówienia nr. " . $order->id;

        $dataArray = [
            'order_id' => $order->id,
            'warehouse_id' => $order->warehouse_id,
            'waiting_for_response' => true,
        ];

        /** @var OrderWarehouseNotification $notification */
        $notification = OrderWarehouseNotification::query()->where($dataArray)->first();
        if (!empty($notification) && (!$order->isOrderHasLabel(Label::PACKAGE_NOTIFICATION_SENT_LABEL) || $order->isOrderHasLabel(Label::PACKAGE_NOTIFICATION_LABEL))) {
            $notification->update([
                'order_id' => $order->id,
                'warehouse_id' => $order->warehouse_id,
                'waiting_for_response' => false,
            ]);
            Log::notice('Znaleziono etykietę Awizacja przyjęta w zamówieniu. Status wysyłania notyfikacji został zmieniony na przyjęty.', ['line' => __LINE__, 'file' => __FILE__, 'order' => $order->id]);
            return;
        }
        if (!$notification && !$order->isOrderHasLabel(Label::WAREHOUSE_REMINDER)) {
            $subject = "Prośba o potwierdzenie awizacji dla zamówienia nr. " . $order->id;
            $notification = OrderWarehouseNotification::query()->create($dataArray);
        }

        $acceptanceFormLink = rtrim(config('app.front_nuxt_url'), "/") . "/magazyn/awizacja/{$notification->id}/{$order->warehouse_id}/{$order->id}";
        $sendFormInvoice = rtrim(config('app.front_nuxt_url'), "/") . "/magazyn/awizacja/{$notification->id}/{$order->warehouse_id}/{$order->id}/wyslij-fakture";

        if (!!filter_var($warehouseMail, FILTER_VALIDATE_EMAIL)) {
            if ($path === null) {
                $email = new OrderStatusChangedToDispatchMail($subject, $acceptanceFormLink, $sendFormInvoice, $order, $self);
                Mailer::notification()->to($warehouseMail)->send($email);
                Log::notice('Wysłano email awizacyjny na mail: ' . $warehouseMail . ' dla zamówienia: ' . $order->id, ['line' => __LINE__, 'file' => __FILE__]);
                return;
            }
            $email = new OrderStatusChangedToDispatchMail($subject, $acceptanceFormLink, $sendFormInvoice, $order, $self, $path, $packageNumber, $pathSecond);
            Mailer::notification()->to($warehouseMail)->send($email);
        }
    }

    public static function sendItemsConstructedMailJob(Order $order): void
    {
        $tags = Tag::all();
        $message = $order->sello_id
            ? setting('allegro.order_items_constructed_msg')
            : setting('site.order_items_constructed_msg');

        $subject = "Państwa oferta zostałą przygotowana i oczekuje na odbior przez kuriera";

        $emailTagHandler = new EmailTagHandlerHelper();
        $emailTagHandler->setOrder($order);

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }

        Mailer::create()
            ->to($order->customer->login)
            ->send(new OrderMessageMail($subject, $message));
    }

    public static function sendItemsRedeemedMail(Order $order): void
    {
        $tmpDate = new DateTime('2022-07-01');
        if ($order->created_at < $tmpDate) {
            return;
        }

        $tags = Tag::all();
        $message = $order->sello_id
            ? setting('allegro.order_items_redeemed_msg')
            : setting('site.order_items_redeemed_msg');

        $subject = "Państwa towar został odebrany przez kuriera";

        $emailTagHandler = new EmailTagHandlerHelper();
        $emailTagHandler->setOrder($order);

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }

        \Mailer::create()
            ->to($order->customer->login)
            ->send(new OrderMessageMail($subject, $message));
    }

}
