@if (!isset($isAuctionOfferCreation) && !$isAuctionOfferCreation)
    <table class="price-table">
        <tr>
            <th>Jednostka handlowa ({{ $product->product->packing->unit_commercial }})</th>
            <th>Jednostka podstawowa ({{$product->product->packing->unit_basic}})</th>
            <th>Jednostka obliczeniowa ({{$product->product->packing->calculation_unit}})</th>
            <th>Jednostka zbiorcza ({{$product->product->packing->unit_of_collective}})</th>
        </tr>
        <tr>
            <td colspan="4">Cena zakupowa netto</td>
        </tr>
        <tr>
            <td>
                <div class="flex">
                    <input class="price net_purchase_price_commercial_unit" name="commercial_price_net"
                           value="{{ $productPrices['commercial_price_net'] ?? $product->net_purchase_price_commercial_unit_after_discounts }}"/>
                    <p>PLN</p>
                </div>
            </td>
            <td>
                <div class="flex">
                    <input class="price net_purchase_price_basic_unit" name="basic_price_net"
                           value="{{ $productPrices['basic_price_net'] ?? $product->net_purchase_price_basic_unit_after_discounts }}"/>
                    <p>PLN</p>
                </div>
            </td>
            <td>
                <div class="flex">
                    <input class="price net_purchase_price_calculated_unit" name="calculated_price_net"
                           value="{{ $productPrices['calculated_price_net'] ?? $product->net_purchase_price_calculated_unit_after_discounts }}"/>
                    <p>PLN</p>
                </div>
            </td>
            <td>
                <div class="flex">
                    <input class="price net_purchase_price_aggregate_unit" name="aggregate_price_net"
                           value="{{ $productPrices['aggregate_price_net'] ??$product->net_purchase_price_aggregate_unit_after_discounts }}"/>
                    <p>PLN</p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4">Cena zakupowa brutto</td>
        </tr>
        <tr>
            <td>
                <div class="flex">
                    <input class="price gross_purchase_price_commercial_unit" name="commercial_price_gross"
                           value="{{$productPrices['commercial_price_gross'] ?? number_format($product->net_purchase_price_commercial_unit_after_discounts * 1.23, 2)}}"/>
                    <p>PLN</p>
                </div>
            </td>
            <td>
                <div class="flex">
                    <input class="price gross_purchase_price_basic_unit" name="basic_price_gross"
                           value="{{ $productPrices['basic_price_gross'] ?? number_format($product->net_purchase_price_basic_unit_after_discounts * 1.23, 2)}}"/>
                    <p>PLN</p>
                </div>
            </td>
            <td>
                <div class="flex">
                    <input class="price gross_purchase_price_calculated_unit" name="calculated_price_gross"
                           value="{{ $productPrices['calculated_price_gross'] ?? number_format($product->net_purchase_price_calculated_unit_after_discounts * 1.23, 2)}}"/>
                    <p>PLN</p>
                </div>
            </td>
            <td>
                <div class="flex">
                    <input class="price gross_purchase_price_aggregate_unit" name="aggregate_price_gross"
                           value="{{ $productPrices['aggregate_price_gross'] ?? number_format($product->net_purchase_price_aggregate_unit_after_discounts * 1.23, 2)}}"/>
                    <p>PLN</p>
                </div>
            </td>
        </tr>
    </table>
@else
    <div class="pricing-table">
        <input class="price net_purchase_price_commercial_unit hidden" form="main" name="commercial_price_net.{{ $product->id }}"
               value="{{ $productPrices['commercial_price_net'] ?? $product->net_purchase_price_commercial_unit_after_discounts }}" style="display: none;">

        <div class="form-group">
            <label for="basic_price_net.{{ $product->id }}">Cena brutto za m3:</label>
            <input class="form-control net_purchase_price_basic_unit" form="main" name="basic_price_net.{{ $product->id }}"
                   value="{{ $productPrices['basic_price_net'] ?? $product->net_purchase_price_basic_unit_after_discounts }}">
        </div>

        <input class="price net_purchase_price_calculated_unit hidden" form="main" name="calculated_price_net.{{ $product->id }}"
               value="{{ $productPrices['calculated_price_net'] ?? $product->net_purchase_price_calculated_unit_after_discounts }}" style="display: none;">

        <input class="price net_purchase_price_aggregate_unit hidden" form="main" name="aggregate_price_net.{{ $product->id }}"
               value="{{ $productPrices['aggregate_price_net'] ??$product->net_purchase_price_aggregate_unit_after_discounts }}" style="display: none;">

        <input class="price gross_purchase_price_commercial_unit hidden" form="main" name="commercial_price_gross.{{ $product->id }}"
               value="{{$productPrices['commercial_price_gross'] ?? number_format($product->net_purchase_price_commercial_unit_after_discounts * 1.23, 2)}}" style="display: none;">

        <input class="price gross_purchase_price_basic_unit hidden" form="main" name="basic_price_gross.{{ $product->id }}"
               value="{{ $productPrices['basic_price_gross'] ?? number_format($product->net_purchase_price_basic_unit_after_discounts * 1.23, 2)}}" style="display: none;">

        <input class="price gross_purchase_price_calculated_unit hidden" form="main" name="calculated_price_gross.{{ $product->id }}"
               value="{{ $productPrices['calculated_price_gross'] ?? number_format($product->net_purchase_price_calculated_unit_after_discounts * 1.23, 2)}}" style="display: none;">

        <input class="price gross_purchase_price_aggregate_unit hidden" form="main" name="aggregate_price_gross.{{ $product->id }}"
               value="{{ $productPrices['aggregate_price_gross'] ?? number_format($product->net_purchase_price_aggregate_unit_after_discounts * 1.23, 2)}}" style="display: none;">
    </div>
@endif
