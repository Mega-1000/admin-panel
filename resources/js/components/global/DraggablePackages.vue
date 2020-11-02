<template>
    <div class="row">
        <div class="col-4">
            <h3>Produkty w zleceniu</h3>

            <draggable
                id="first"
                data-source="juju"
                :list="list"
                class="list-group"
                draggable=".item"
                group="a"
            >
                <div
                    class="list-group-item item"
                    v-for="element in list"
                    :key="element.name"
                >
                    {{ element.name }}
                </div>

                <div
                    slot="footer"
                    class="btn-group list-group-item"
                    role="group"
                    aria-label="Basic example"
                >
                </div>
            </draggable>
        </div>

        <div class="col-4">
            <h3>Paczki</h3>

            <draggable :list="list2" class="list-group" draggable=".item" group="a">
                <div
                    class="list-group-item item"
                    v-for="element in list2"
                    :key="element.name"
                >
                    {{ element.name }}
                </div>

                <div
                    slot="footer"
                    class="btn-group list-group-item"
                    role="group"
                    aria-label="Basic example"
                >
                </div>
            </draggable>
        </div>

        <rawDisplayer class="col-2" :value="packages" title="Order packages" />

        <rawDisplayer class="col-2" :value="products" title="Order products" />
    </div>
</template>
<script>
    import { draggable, rawDisplayer } from "vuedraggable";
    export default {
        components: {
            draggable,
            rawDisplayer,
        },
        data() {
            return {
                packages: [],
                products: [],
                order: [],
                sendStatus: false,
                error: [],
            };
        },
        methods: {
            async fetchOrderWithPackages() {
                window.axios.get('/getSpecificOrderWithPackages')
                    .then(response => {
                        this.packages = response.data.packages;
                        this.products = response.data.orderProducts;
                        this.order = response.data.order;
                    });
            },
            async savePackagesItems() {
                window.axios.post('/saveSpecificOrderWithPackages', { packages: this.packages, products: this.products, orderId: this.order.id })
                    .then(response => {
                        this.sendStatus = true;
                    }).catch(error => {
                        this.error = error;
                    });
            }
        },
        async mounted() {
            await this.fetchOrderWithPackages();
        }
    };
</script>
<style>

</style>
