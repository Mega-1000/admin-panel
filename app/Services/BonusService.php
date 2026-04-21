<?php namespace App\Services;

use App\Entities\BonusAndPenalty;
use App\Entities\Order;
use App\User;
use Carbon\Carbon;

class BonusService
{

    const CONSULTANT_INDEX = -1;
    const WAREHOUSE_INDEX = -2;

    public function getChat(BonusAndPenalty $bonus)
    {
        if ($bonus->chat) {
            return json_decode($bonus->chat, true);
        } else {
            return [];
        }
    }

    public function sendMessage(BonusAndPenalty $bonus, string $message, User $sender)
    {
        $chat = $this->getChat($bonus);
        $chat[] = [
            'name' => $sender->firstname . ' ' . $sender->lastname,
            'message' => $message
        ];
        $bonus->chat = json_encode($chat);
        $bonus->save();
    }

    public function findResponsibleUsers(int $orderId): array
    {
        $order = Order::with(['employee', 'task'])->find($orderId);

        try {
            $warehouse = $order->taskSchedule()->orderBy('created_at', 'desc')->first()->user;
        } catch (\ErrorException $e) {
            $warehouse = 'BRAK';
        }

        return [
            'warehouse' => $warehouse,
            'consultant' => $order->employee ?: 'BRAK'
        ];
    }

}
