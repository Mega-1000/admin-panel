<script>
    function checkStockQuantity() {
        let productStockQuantity = {{ $productStock->quantity }};
            @if($productStock->position->first())
        let productStockFirstPositionQuantity = {{ $productStock->position->first()->position_quantity }};
            @else
        let productStockFirstPositionQuantity = null;
            @endif
        let packetQuantityResult = document.getElementById('packet_quantity').value * document.getElementById('packet_product_quantity').value;
        if(!productStockFirstPositionQuantity) {
            $('#stockPositionMissing').modal('show');
            return false;
        }
        if(productStockQuantity < packetQuantityResult) {
            $('#stockQuantityLow').modal('show');
            return false;
        }
        if(productStockQuantity < packetQuantityResult) {
            $('#stockPositionQuantityLow').modal('show');
            return false;
        }
        return true;
    }
</script>
