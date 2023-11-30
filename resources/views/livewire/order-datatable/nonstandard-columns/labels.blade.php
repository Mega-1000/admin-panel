@foreach(array_filter($order['labels'], function ($label) use($labelGroupName) { return $label['label_group']['name'] === $labelGroupName; }) as $label)
    <span
        style="cursor: pointer"
        wire:click="selectCurrent({{ $label['id'] }})"
    >
        <i class="{{ $label['icon_name'] }}" style="font-size: 24px; background-color: {{ $label['color'] }}; padding: 5px"></i>
    </span>
@endforeach
