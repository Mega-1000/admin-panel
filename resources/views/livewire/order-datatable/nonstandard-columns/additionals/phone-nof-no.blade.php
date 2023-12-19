<div
    onmouseover="showPhoneInformations({{ $wholeOrder['created_at'] }})"
    onmouseout="hidePhoneInformations({{ $wholeOrder['created_at'] }})"
>
    <a class="btn btn-primary" href="?{{ $column['label'] }}={{ $data }}">
        NO
    </a>

    <a class="btn btn-success" href="?{{ $column['label'] }}={{ $data }}" target="__blank">
        NOF
    </a>

    <br>

    <div id="tooltip-phone-info-{{ $wholeOrder['created_at'] }}" style="display: none">
        okej test
    </div>
</div>
