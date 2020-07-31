<div class="flex-container-labels">
    <div class="label-with-desc-pair">
        <label>
            mas
            <button title="{{ $labelsButtons[\App\Entities\Label::WAREHOUSE_MARK]->name}}"
                    data-label-id="{{\App\Entities\Label::WAREHOUSE_MARK}}" class="add-label"><i
                    style="color: {{ ($labelsButtons[\App\Entities\Label::WAREHOUSE_MARK])->color }};
                        margin-top: 5px;"
                    class="{{ $labelsButtons[\App\Entities\Label::WAREHOUSE_MARK]->icon_name }}"></i>
            </button>
        </label>
    </div>
    <div class="label-with-desc-pair">
        <label>
            kon
            <button title="{{ $labelsButtons[\App\Entities\Label::SHIPPING_MARK]->name}}"
                    data-label-id="{{\App\Entities\Label::SHIPPING_MARK}}" class="add-label"><i
                    style="color: {{ ($labelsButtons[\App\Entities\Label::SHIPPING_MARK])->color }};
                        margin-top: 5px;"
                    class="{{ $labelsButtons[\App\Entities\Label::SHIPPING_MARK]->icon_name }}"></i>
            </button>
        </label>
    </div>
    <div class="label-with-desc-pair">
        <label>
            mag
            <button title="{{ $labelsButtons[\App\Entities\Label::CONSULTANT_MARK]->name}}"
                    data-label-id="{{\App\Entities\Label::CONSULTANT_MARK}}" class="add-label"><i
                    style="color: {{ ($labelsButtons[\App\Entities\Label::CONSULTANT_MARK])->color }};
                        margin-top: 5px;"
                    class="{{ $labelsButtons[\App\Entities\Label::CONSULTANT_MARK]->icon_name }}"></i>
            </button>
        </label>
    </div>
    <div class="label-with-desc-pair">
        <label>
            fin
            <button title="{{ $labelsButtons[\App\Entities\Label::MASTER_MARK]->name}}"
                    data-label-id="{{\App\Entities\Label::MASTER_MARK}}" class="add-label"><i
                    style="color: {{ ($labelsButtons[\App\Entities\Label::MASTER_MARK])->color }};
                        margin-top: 5px;"
                    class="{{ $labelsButtons[\App\Entities\Label::MASTER_MARK]->icon_name }}"></i>
            </button>
        </label>
    </div>
    <label for="warehouse_notice">{{ $title }}</label>
</div>
