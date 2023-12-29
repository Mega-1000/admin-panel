<div>
    @if($labelGroupName === 'info dodatkowe')
        <button onclick="uploadFile({{ $order['id'] }})">
            Dodaj
        </button>

        @foreach($order['files'] as $file)
            <a href="{{ route('orders.getFile', ['id' => $order['id'], 'file_id' => $file['hash']]) }}" target="_blank">
                {{ $file['file_name'] }}
            </a>
        @endforeach
    @endif

    <div class="label-container">
        @foreach(!empty($order['labels']) && count($order['labels']) > 0 ? array_filter($order['labels'], function ($label) use($labelGroupName) { return $label['label_group']['name'] === $labelGroupName; }) : [] as $label)
            <span
                onclick="
                removeLabel({{ $order['id']}}, {{ $label['id'] }}, {{ $label['manual_label_selection_to_add_after_removal'] }}, 'null', {{$label['timed']}})"
                class="label-wrapper"
                style="cursor: pointer"
                onmouseover="showLabelName(this, '{{ $label['name'] }}')"
                onmouseout="hideLabelName(this)"
            >
                <i class="{{ $label['icon_name'] }}" style="font-size: 24px; background-color: {{ $label['color'] }}; padding: 5px"></i>
                <div class="label-popup">{{ $label['name'] }}</div>
            </span>
        @endforeach
    </div>
</div>

<style>
    .label-container {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        max-width: 100%;
        gap: 5px
    }

    .label-wrapper {
        position: relative;
        margin-right: 10px;
    }

    .label-popup {
        display: none;
        position: absolute;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        padding: 5px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .label-wrapper:hover .label-popup {
        display: block;
    }
</style>
