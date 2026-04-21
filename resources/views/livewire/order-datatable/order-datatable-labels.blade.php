<div>
    @foreach(!empty($order['labels']) && count($order['labels']) > 0 ? array_filter($order['labels'], function ($label) use($labelGroupName) { return $label['label_group']['name'] === $labelGroupName; }) : [] as $label)
        <span
            style="cursor: pointer"
            wire:click="removeLabel({{ $label['id'] }}, {{ $order['id'] }})"
        >
        <i class="{{ $label['icon_name'] }}" style="font-size: 24px; background-color: {{ $label['color'] }}; padding: 5px"></i>
    </span>
    @endforeach
</div>


