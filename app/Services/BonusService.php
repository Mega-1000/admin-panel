<?php namespace App\Services;

use App\Entities\Order;
use App\User;

class BonusService
{

    const CONSULTANT_INDEX = -1;
    const WAREHOUSE_INDEX = -2;

    public function getChat()
    {

    }

    public function sendMessage()
    {

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

    public function updateLabels()
    {

    }

    public function generateUserReport()
    {

    }

    public function generateMasterReport()
    {

    }

}
