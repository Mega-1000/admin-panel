<template>
    <div class="form-group">
        <input type="hidden" :name="name" :value="timedLabels | toString">
        <label>Etykiety do dodania i usunięcia po dodaniu obecnej po określonym czasie (godziny)</label>
        <div>A: pominięcie weekendów i świąt oraz godzin 21-7</div>
        <div>B: pominięcie weekendów i świąt</div>
        <div>C: ustawione przez użytkownika</div>
        <div class="multiselect-wrapper">
            <div class="multiselect__header">
                <div class="multiselect__item-hours">Dodaj A</div>
                <div class="multiselect__item-hours">Usuń A</div>
                <div class="multiselect__item-hours">Dodaj B</div>
                <div class="multiselect__item-hours">Usuń B</div>
                <div class="multiselect__item-hours">Dodaj C</div>
                <div class="multiselect__item-hours">Usuń C</div>
            </div>
            <div class="multiselect__items">
                <div v-for="label in labels"
                     class="multiselect__item"
                     :class="{'multiselect__item--selected': hasTime(label.id)}"
                >
                    <input class="multiselect__item-hours" type="text" v-model="to_add_type_a[label.id]">
                    <input class="multiselect__item-hours" type="text" v-model="to_remove_type_a[label.id]">
                    <input class="multiselect__item-hours" type="text" v-model="to_add_type_b[label.id]">
                    <input class="multiselect__item-hours" type="text" v-model="to_remove_type_b[label.id]">
                    <input class="multiselect__item-hours" type="checkbox" v-model="to_add_type_c[label.id]">
                    <input class="multiselect__item-hours" type="checkbox" v-model="to_remove_type_c[label.id]">
                    <span class="multiselect__icon"
                          :style="{ color:  label.font_color, backgroundColor: label.color }">
                        <i :class="label.icon_name"></i>
                    </span>
                    <span class="multiselect__icon-name">{{label.name}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "timed-labels-config",
        props: [
            'labels',
            'existingTimedLabels',
            'name'
        ],
        data() {
            return {
                to_add_type_a: {},
                to_remove_type_a: {},
                to_add_type_b: {},
                to_remove_type_b: {},
                to_add_type_c: {},
                to_remove_type_c: {},
                currentlySelected: null
            }
        },
        computed: {
            timedLabels() {
                let val = {};
                let checker = (objToCheck, name) => {
                    for (let [currentLabelId, labelId] of Object.entries(objToCheck)) {
                        if (val[currentLabelId] == undefined) {
                            val[currentLabelId] = {}
                        }

                        if (labelId == "") {
                            delete val[currentLabelId][name];

                            if (!val[currentLabelId].length) {
                                delete val[currentLabelId];
                            }
                        } else {
                            val[currentLabelId][name] = labelId;
                        }
                    }
                };

                checker(this.to_add_type_a, 'to_add_type_a');
                checker(this.to_add_type_b, 'to_add_type_b');
                checker(this.to_add_type_c, 'to_add_type_c');
                checker(this.to_remove_type_a, 'to_remove_type_a');
                checker(this.to_remove_type_b, 'to_remove_type_b');
                checker(this.to_remove_type_c, 'to_remove_type_c');

                return val;
            }
        },
        methods: {
            hasTime(id) {

                if (!this.timedLabels[id]) {
                    return false
                }

                if (Object.keys(this.timedLabels[id]).length < 1) {
                    return false
                }

                return true
            }
        },
        filters: {
            toString(val) {
                return JSON.stringify(val)
            }
        },
        beforeMount() {
            if (this.existingTimedLabels != undefined) {
                for (let [labelId, values] of Object.entries(this.existingTimedLabels)) {
                    let id = String(labelId);
                    if (values['to_add_type_a']) {
                        this.$set(this.$data.to_add_type_a, id, values['to_add_type_a']);
                    }

                    if (values['to_remove_type_a']) {
                        this.$set(this.$data.to_remove_type_a, id, values['to_remove_type_a']);
                    }

                    if (values['to_add_type_b']) {
                        this.$set(this.$data.to_add_type_b, id, values['to_add_type_b']);
                    }

                    if (values['to_remove_type_b']) {
                        this.$set(this.$data.to_remove_type_b, id, values['to_remove_type_b']);
                    }

                    if (values['to_add_type_c']) {
                        this.$set(this.$data.to_add_type_c, id, values['to_add_type_c']);
                    }

                    if (values['to_remove_type_c']) {
                        this.$set(this.$data.to_remove_type_c, id, values['to_remove_type_c']);
                    }
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .multiselect-wrapper {
        border: 1px solid #e4eaec;
        border-radius: 3px;
        padding: 10px 0 6px 12px;
        margin-top: 10px;
        height: 200px;
        display: flex;
        flex-direction: column;
    }

    .multiselect {

        &__header {
            display: flex;
            margin-bottom: 10px;
        }

        &__items {
            overflow-y: scroll;
        }

        &__item-hours {
            margin-right: 15px;
            width: 70px;
            min-width: 70px;
            padding: 1px 3px;
            line-height: 16px;
            text-align: center;
        }

        &__item {
            margin: 3px 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;

            &--selected {
                background-color: #b1e9ff;
            }
        }

        &__icon {
            min-width: 50px;
            min-height: 26px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.3rem;
            margin-right: 15px;
        }

        &__icon-name {
            text-transform: uppercase;
        }
    }
</style>
