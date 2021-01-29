<?php
declare(strict_types=1);

namespace App\Enums\CourierStatus;

use BenSampo\Enum\Enum;

final class InpostPackageStatus extends Enum
{
    const WAITING_FOR_SENDING = ['dispatched_by_sender'];
    const SENDING = [
        'collected_from_sender',
        'taken_by_courier',
        'adopted_at_source_branch',
        'sent_from_source_branch',
        'ready_to_pickup_from_pok',
        'ready_to_pickup_from_pok_registered',
        'adopted_at_sorting_center',
        'sent_from_sorting_center',
        'adopted_at_target_branch',
        'out_for_delivery',
        'ready_to_pickup',
        'pickup_reminder_sent',
        'pickup_time_expired',
        'dispatched_by_sender_to_pok',
        'pickup_reminder_sent_address',
        'taken_by_courier_from_pok',
        'redirect_to_box',
        'stack_parcel_pickup_time_expired',
        'unstack_from_customer_service_point',
        'courier_avizo_in_customer_service_point',
        'out_for_delivery_to_address',
    ];
    const DELIVERED = ['delivered'];
}
