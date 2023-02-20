<h3>{{ $title }}</h3>
<div class="flex-container-labels">
    <div class="col-md-6">
        <p class="text-left">Dodaj etykiety</p>
        <div class="row">

            <div class="col-md-1 label-with-desc-pair">
                <label>
                    mas
                </label>
                <button title="{{ $labelsButtons[\App\Entities\Label::WAREHOUSE_MARK]->name}}"
                        data-label-id="{{\App\Entities\Label::WAREHOUSE_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[\App\Entities\Label::WAREHOUSE_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[\App\Entities\Label::WAREHOUSE_MARK]->icon_name }}"></i>
                </button>
                @if($user_type === \App\Enums\UserRole::Consultant)
                <h6 title="{{ $labelsButtons[\App\Entities\Label::WAREHOUSE_MARK]->name}}"
                        data-label-id="{{\App\Entities\Label::WAREHOUSE_MARK}}" class="add-label">
                </h6>
                @endif
            </div>
            <div class="col-md-1 label-with-desc-pair">
                <label>
                    kon
                </label>
                <button title="{{ $labelsButtons[\App\Entities\Label::SHIPPING_MARK]->name}}"
                        data-label-id="{{\App\Entities\Label::SHIPPING_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[\App\Entities\Label::SHIPPING_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[\App\Entities\Label::SHIPPING_MARK]->icon_name }}"></i>
                </button>
                @if($user_type === \App\Enums\UserRole::Consultant)
                <h6 title="{{ $labelsButtons[\App\Entities\Label::SHIPPING_MARK]->name}}"
                    data-label-id="{{\App\Entities\Label::SHIPPING_MARK}}" class="add-label">
                </h6>
                @endif
            </div>
            <div class="col-md-1 label-with-desc-pair">
                <label>
                    mag
                </label>
                <button title="{{ $labelsButtons[\App\Entities\Label::CONSULTANT_MARK]->name}}"
                        data-label-id="{{\App\Entities\Label::CONSULTANT_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[\App\Entities\Label::CONSULTANT_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[\App\Entities\Label::CONSULTANT_MARK]->icon_name }}"></i>
                </button>
                @if($user_type === \App\Enums\UserRole::Consultant)
                <h6 title="{{ $labelsButtons[\App\Entities\Label::CONSULTANT_MARK]->name}}"
                    data-label-id="{{\App\Entities\Label::CONSULTANT_MARK}}" class="add-label">
                </h6>
                @endif
            </div>
            <div class="col-md-1 label-with-desc-pair">
                <label>
                    fin
                </label>
                <button title="{{ $labelsButtons[\App\Entities\Label::MASTER_MARK]->name}}"
                        data-label-id="{{\App\Entities\Label::MASTER_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[\App\Entities\Label::MASTER_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[\App\Entities\Label::MASTER_MARK]->icon_name }}"></i>
                </button>
                @if($user_type === \App\Enums\UserRole::Consultant)
                <h6 title="{{ $labelsButtons[\App\Entities\Label::MASTER_MARK]->name}}"
                    data-label-id="{{\App\Entities\Label::MASTER_MARK}}" class="add-label">
                </h6>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <p class="text-center">Usuń etykiety</p>
        <div class="row">

            @if($order->labels->firstWhere('id', \App\Entities\Label::WAREHOUSE_MARK))
                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        mas
                    </label>
                    <button title="{{ $labelsButtons[\App\Entities\Label::WAREHOUSE_MARK]->name}}"
                            data-label-id="{{\App\Entities\Label::WAREHOUSE_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[\App\Entities\Label::WAREHOUSE_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[\App\Entities\Label::WAREHOUSE_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
            @if($order->labels->firstWhere('id', \App\Entities\Label::SHIPPING_MARK))

                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        kon
                    </label>
                    <button title="{{ $labelsButtons[\App\Entities\Label::SHIPPING_MARK]->name}}"
                            data-label-id="{{\App\Entities\Label::SHIPPING_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[\App\Entities\Label::SHIPPING_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[\App\Entities\Label::SHIPPING_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
            @if($order->labels->firstWhere('id', \App\Entities\Label::CONSULTANT_MARK))

                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        mag
                    </label>
                    <button title="{{ $labelsButtons[\App\Entities\Label::CONSULTANT_MARK]->name}}"
                            data-label-id="{{\App\Entities\Label::CONSULTANT_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[\App\Entities\Label::CONSULTANT_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[\App\Entities\Label::CONSULTANT_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
            @if($order->labels->firstWhere('id', \App\Entities\Label::MASTER_MARK))

                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        fin
                    </label>
                    <button title="{{ $labelsButtons[\App\Entities\Label::MASTER_MARK]->name}}"
                            data-label-id="{{\App\Entities\Label::MASTER_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[\App\Entities\Label::MASTER_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[\App\Entities\Label::MASTER_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
