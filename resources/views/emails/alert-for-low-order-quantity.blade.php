@php
    use App\Services\LowOrderQuantityAlertService;
@endphp

<div>
    {{ LowOrderQuantityAlertService::parseToken($alert->message, $order->id) }}
</div>
