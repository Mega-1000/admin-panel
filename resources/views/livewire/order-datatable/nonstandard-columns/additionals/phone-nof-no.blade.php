<div
    @php
    @endphp
    onmouseover="showPhoneInformations({{ $data['id'] }})"
    onmouseout="hidePhoneInformations({{ $data['id'] }})"
>
    <a class="btn btn-primary" href="?{{ $column['label'] }}={{ $data }}">
        NO
    </a>

    <a class="btn btn-success" href="?{{ $column['label'] }}={{ $data }}" target="__blank">
        NOF
    </a>

    <br>

    <div id="tooltip-phone-info-{{ $column['id'] }}" style="display: none">
        okej test
    </div>
</div>
