<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderWarehouseNotificationRepository;

class OrderWarehouseNotificationService
{
    private $orderWarehouseNotificationRepository;

    public function __construct(OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository)
    {
        $this->orderWarehouseNotificationRepository = $orderWarehouseNotificationRepository;
    }

    public function removeNotifications(int $orderId): void
    {
        $notifications = $this->orderWarehouseNotificationRepository->findWhere([
            'order_id' => $orderId
        ]);

        foreach($notifications as $notification) {
            $notification->delete();
        }
    }
}
