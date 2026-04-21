<template>
    <div class="filter-by-labels-in-group">
        <input
            type="text"
            v-model="selected"
            class="filter-by-labels-in-group-input"
            :class="'filter-by-labels-in-group-input--' + groupName | replaceSpaces">
        <span class="order-label"
              @click="toggleContaier"
              style="display: block; margin-top: 5px;"
              :style="{color: label.font_color, backgroundColor: label.color}">
            <i :class="label.icon_name"></i>
        </span>

        <div v-if="showContainer" class="filter-by-labels-in-group__container">
            <div class="filter-by-labels-in-group__container__items">
                <span v-for="(lab, index) in labels"
                      class="order-label filter-by-labels-in-group-input-change"
                      @click="selectCurrent(lab.id)"
                      :style="{color: lab.font_color, backgroundColor: lab.color}">
                    <i :class="lab.icon_name"></i>
                </span>
            </div>
        </div>
        <div v-show="showContainer" class="filter-by-labels-in-group__clear">
            <button class="btn btn-warning" @click="clearSelected">wyczyść</button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "filter-by-labels-in-group",
        props: [
            'groupName'
        ],
        data() {
            return {
                labels: [],
                selected: 0,
                showContainer: false
            }
        },
        computed: {
            label() {
                if (this.selected) {
                    return this.labels.find(lab => {
                        return lab.id == this.selected;
                    });
                }

                return {
                    id: 0,
                    color: "#d4d2e5",
                    font_color: "#ffffff",
                    icon_name: "fas fa-filter",
                }
            }
        },
        methods: {
            toggleContaier() {
                if (!this.showContainer) {
                    this.getAssociatedLabelsToOrderFromGroup();
                }

                this.showContainer = !this.showContainer;
            },
            getAssociatedLabelsToOrderFromGroup() {
                axios.get('/api/get-associated-labels-to-order-from-group/' + this.groupName)
                    .then(response => {
                        this.labels = response.data;
                    });
            },
            selectCurrent(id) {
                this.selected = id;

                table
                    .column('label_' + this.$options.filters.replaceSpaces(this.groupName) + ":name")
                    .search(this.selected)
                    .draw();
                this.toggleContaier();
            },
            clearSelected() {
                if (this.selected) {
                    this.selected = 0;

                    table
                        .column('label_' + this.$options.filters.replaceSpaces(this.groupName) + ":name")
                        .search(this.selected)
                        .draw();
                    this.toggleContaier();
                }
            }
        },
        filters: {
            replaceSpaces(value) {
                return value.replace(" ", "_");
            }
        }
    }
</script>

<style lang="scss" scoped>
    .filter-by-labels-in-group {
        position: absolute;
        height: 33px;

        &-input {
            display: none;
        }

        .order-label {
            width: 50px;
            cursor: pointer;
        }

        &__clear {
            position: absolute;
            display: block;
            top: 40px;
            right: -90px;
            z-index: 2;
        }

        &__container {
            border: 1px solid #D4D2E5;
            background-color: #fff;
            padding: 5px 5px 10px 10px;

            position: absolute;
            display: block;
            top: 40px;
            right: 0;
            z-index: 2;

            &__items {
                display: flex;
                flex-wrap: wrap;
                width: 260px;

                & .order-label {
                    margin-right: 5px;
                    margin-top: 5px;
                    width: 60px;
                }
            }
        }
    }
</style>
