<?php namespace App\Services;

use App\Entities\Order;
use App\User;
use Carbon\Carbon;

class BonusService
{

    const CONSULTANT_INDEX = -1;
    const WAREHOUSE_INDEX = -2;

    public function getChat(Order $order)
    {
        return json_decode($order->chat, true);
    }

    public function sendMessage(Order $order, string $message, User $sender)
    {
        $chat = $this->getChat($order);
        $chat[$sender->firstname.' '.$sender->lastname] = $message;
        $order->chat = json_encode($chat);
        $order->save();
    }

    public function findResponsibleUsers(int $orderId): array
    {
        $order = Order::with(['employee', 'task'])->find($orderId);

        try {
            $warehouse = $order->taskSchedule()->orderBy('created_at', 'desc')->first()->user;
        } catch (\ErrorException $e){
            $warehouse = 'BRAK';
        }

        return [
            'warehouse' => $warehouse,
            'consultant' => $order->employee ?: 'BRAK'
        ];
    }

    public function updateLabels(Order $order)
    {

    }

    public function generateUserReport(User $user, Carbon $dateFrom, Carbon $dateTo)
    {

    }

    public function generateMasterReport(Carbon $dateFrom, Carbon $dateTo)
    {

    }

}
