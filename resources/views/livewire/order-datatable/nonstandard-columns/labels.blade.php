<div>
    @if($labelGroupName === 'info dodatkowe')
        <button onclick="uploadFile()">
            Dodaj
        </button>

        @foreach($order['files'] as $file)
            <a href="{{ route('orders.getFile', ['id' => $file['id'], 'file_id' => $file['hash']]) }}" target="_blank">
                {{ $file['file_name'] }}
            </a>
        @endforeach
    @endif

    @foreach(!empty($order['labels']) && count($order['labels']) > 0 ? array_filter($order['labels'], function ($label) use($labelGroupName) { return $label['label_group']['name'] === $labelGroupName; }) : [] as $label)
        <span
            style="cursor: pointer"
            onclick="removeLabel({{ $label['id'] }}, {{ $order['id'] }})"
        >
        <i class="{{ $label['icon_name'] }}" style="font-size: 24px; background-color: {{ $label['color'] }}; padding: 5px"></i>
    </span>
    @endforeach
</div>
