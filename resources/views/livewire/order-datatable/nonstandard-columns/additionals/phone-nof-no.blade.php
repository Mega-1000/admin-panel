<div
    @php
    @endphp
    onmouseover="showPhoneInformations(this, '{{ $column['id'] }}')"
    onmouseout="hidePhoneInformations(this)"
>
    <a class="btn btn-primary" href="?{{ $column['label'] }}={{ $data }}">
        NO
    </a>

    <a class="btn btn-success" href="?{{ $column['label'] }}={{ $data }}" target="__blank">
        NOF
    </a>

    <br>

    <div id="tooltip-phone-info-{{ $column['id'] }}">
        okej test
    </div>
</div>
