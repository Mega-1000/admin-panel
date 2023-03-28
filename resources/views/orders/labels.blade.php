<h3>{{ $title }}</h3>
<div class="flex-container-labels">
    <div class="col-md-6">
        <p class="text-left">Dodaj etykiety</p>
        <div class="row">

            <div class="col-md-1 label-with-desc-pair">
                <label>
                    mas
                </label>
                <button title="{{ $labelsButtons[Label::WAREHOUSE_MARK]->name}}"
                        data-label-id="{{Label::WAREHOUSE_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[Label::WAREHOUSE_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[Label::WAREHOUSE_MARK]->icon_name }}"></i>
                </button>
                <h6 title="{{ $labelsButtons[Label::WAREHOUSE_MARK]->name}}"
                        data-label-id="{{Label::WAREHOUSE_MARK}}" class="add-label">
                </h6>
            </div>
            <div class="col-md-1 label-with-desc-pair">
                <label>
                    kon
                </label>
                <button title="{{ $labelsButtons[Label::SHIPPING_MARK]->name}}"
                        data-label-id="{{Label::SHIPPING_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[Label::SHIPPING_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[Label::SHIPPING_MARK]->icon_name }}"></i>
                </button>
                <h6 title="{{ $labelsButtons[Label::SHIPPING_MARK]->name}}"
                    data-label-id="{{Label::SHIPPING_MARK}}" class="add-label">
                </h6>
            </div>
            <div class="col-md-1 label-with-desc-pair">
                <label>
                    mag
                </label>
                <button title="{{ $labelsButtons[Label::CONSULTANT_MARK]->name}}"
                        data-label-id="{{Label::CONSULTANT_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[Label::CONSULTANT_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[Label::CONSULTANT_MARK]->icon_name }}"></i>
                </button>
                <h6 title="{{ $labelsButtons[Label::CONSULTANT_MARK]->name}}"
                    data-label-id="{{Label::CONSULTANT_MARK}}" class="add-label">
                </h6>
            </div>
            <div class="col-md-1 label-with-desc-pair">
                <label>
                    fin
                </label>
                <button title="{{ $labelsButtons[Label::MASTER_MARK]->name}}"
                        data-label-id="{{Label::MASTER_MARK}}" class="add-label"><i
                        style="color: {{ ($labelsButtons[Label::MASTER_MARK])->color }};
                            margin-top: 5px;"
                        class="{{ $labelsButtons[Label::MASTER_MARK]->icon_name }}"></i>
                </button>
                <h6 title="{{ $labelsButtons[Label::MASTER_MARK]->name}}"
                    data-label-id="{{Label::MASTER_MARK}}" class="add-label">
                </h6>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <p class="text-center">Usuń etykiety</p>
        <div class="row">

            @if($order->labels->firstWhere('id', Label::WAREHOUSE_MARK))
                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        mas
                    </label>
                    <button title="{{ $labelsButtons[Label::WAREHOUSE_MARK]->name}}"
                            data-label-id="{{Label::WAREHOUSE_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[Label::WAREHOUSE_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[Label::WAREHOUSE_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
            @if($order->labels->firstWhere('id', Label::SHIPPING_MARK))

                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        kon
                    </label>
                    <button title="{{ $labelsButtons[Label::SHIPPING_MARK]->name}}"
                            data-label-id="{{Label::SHIPPING_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[Label::SHIPPING_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[Label::SHIPPING_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
            @if($order->labels->firstWhere('id', Label::CONSULTANT_MARK))

                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        mag
                    </label>
                    <button title="{{ $labelsButtons[Label::CONSULTANT_MARK]->name}}"
                            data-label-id="{{Label::CONSULTANT_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[Label::CONSULTANT_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[Label::CONSULTANT_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
            @if($order->labels->firstWhere('id', Label::MASTER_MARK))

                <div class="col-md-1 label-with-desc-pair">
                    <label>
                        fin
                    </label>
                    <button title="{{ $labelsButtons[Label::MASTER_MARK]->name}}"
                            data-label-id="{{Label::MASTER_MARK}}" class="remove-label"><i
                            style="color: {{ ($labelsButtons[Label::MASTER_MARK])->color }};
                                margin-top: 5px;"
                            class="{{ $labelsButtons[Label::MASTER_MARK]->icon_name }}"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
