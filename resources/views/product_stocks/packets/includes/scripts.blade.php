<script>
    {{--function checkStockQuantity() {--}}
    {{--    let productStockQuantity = {{ $productStock->quantity }};--}}
    {{--        @if($productStock->position->first())--}}
    {{--    let productStockFirstPositionQuantity = {{ $productStock->position->first()->position_quantity }};--}}
    {{--        @else--}}
    {{--    let productStockFirstPositionQuantity = null;--}}
    {{--        @endif--}}
    {{--    let packetQuantityResult = document.getElementById('packet_quantity').value * document.getElementById('packet_product_quantity').value;--}}
    {{--    if(!productStockFirstPositionQuantity) {--}}
    {{--        $('#stockPositionMissing').modal('show');--}}
    {{--        return false;--}}
    {{--    }--}}
    {{--    if(productStockQuantity < packetQuantityResult) {--}}
    {{--        $('#stockQuantityLow').modal('show');--}}
    {{--        return false;--}}
    {{--    }--}}
    {{--    if(productStockQuantity < packetQuantityResult) {--}}
    {{--        $('#stockPositionQuantityLow').modal('show');--}}
    {{--        return false;--}}
    {{--    }--}}
    {{--    return true;--}}
    {{--}--}}
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('packet_quantity').addEventListener('input', togglePacketProductAddForm);
        document.getElementById('product__assign').addEventListener('click', (e) => {
            e.preventDefault();
            addProductToPacket();
        })
        let storePacketButton = document.getElementById('store__packet');
        if (storePacketButton) {
            storePacketButton.addEventListener('click', (e) => {
                e.preventDefault();
                storePacket();
            })
        }
        let updatePacketButton = document.getElementById('update__packet');
        if(updatePacketButton) {
            updatePacketButton.addEventListener('click', (e) => {
                e.preventDefault();
                updatePacket();
            })
        }
    });

    function addProductToPacket() {
        let productSelectElement = document.getElementById("product__select");
        let selectedOptionValue = productSelectElement.value;
        let selectedOptionName = productSelectElement.options[productSelectElement.selectedIndex].text;
        let productQuantityInPacket = document.getElementById('packet_product_quantity').value * document.getElementById('packet_quantity').value;
        if(checkProductStock(selectedOptionValue, productQuantityInPacket, selectedOptionName)) {

        }
    }

    function renderProductRow(productId, productName, productQuantity) {
        let productListElement = document.getElementById('products__list');
        productListElement.insertAdjacentHTML('beforeend', '<div class="form-group">' +
            `<label for="product__${productId}">${productName}</label>` +
            `<input type="number" class="form-control product" data-product-id="${productId}" id="product__${productId}" value="${productQuantity}" name="packet_quantity" readonly>` +
            '</div>')
    }

    function checkProductStock(productId, productQuantity, productName) {
        return axios.post(laroute.route('product_stock_packets.product.stock.check'), {
            'productId': productId,
            'productQuantity': productQuantity
        }).then((response) => {
            switch(response.data.status) {
                case true:
                    toastr.success(response.data.message);
                    renderProductRow(productId, productName, productQuantity)
                    return true;
                case false:
                    toastr.error(response.data.message);
                    return false;
            }
        }).catch((error) => {
            console.error(error);
            return false;
        })
    }

    function togglePacketProductAddForm(element) {
        let productPacketForm = document.getElementById('product__packet--form');
        element.target.value ? productPacketForm.style.display = 'block' : productPacketForm.style.display = 'none';
    }

    function storePacket() {
        let packetsQuantity = document.getElementById('packet_quantity').value;
        let packetName = document.getElementById('packet_name').value;
        let products = [];
        document.querySelectorAll('.product').forEach(function(product) {
            let productId = product.dataset.productId;
            let productQuantity = product.value;
            products.push({
                [productId]: productQuantity
            });
        });

        axios.post(laroute.route('product_stock_packets.store'), {
            'packetName' : packetName,
            'packetsQuantity': packetsQuantity,
            'products': products
        }).then((response) => {
           console.log(response);
        }).catch((error) => {
            console.error(error);
            return false;
        })
    }

    function updatePacket() {
        let packetsQuantity = document.getElementById('packet_quantity').value;
        let packetName = document.getElementById('packet_name').value;
        let products = [];
        document.querySelectorAll('.product').forEach(function(product) {
            let productId = product.dataset.productId;
            let productQuantity = product.value;
            products.push({
                id: productId,
                quantity: productQuantity
            });
        });

        axios.put(laroute.route('product_stock_packets.update'), {
            'id': document.getElementById('packet_id').value,
            'packetName' : packetName,
            'packetsQuantity': packetsQuantity,
            'products': products
        }).then((response) => {
            console.log(response);
        }).catch((error) => {
            console.error(error);
            return false;
        })
    }
</script>
