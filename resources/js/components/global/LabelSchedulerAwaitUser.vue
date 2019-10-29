<template>
    <div class="fullscreen" v-if="active">
        <h3>Wymagane podanie dat przypomnienia po dodaniu etykiet:</h3>

        <div class="">
            <div class="await-container" v-for="(item, i) in datesToSet">
                <p>{{ item.name }}</p>
                <datetime
                    v-model="datesToSet[i].date"
                    format="yyyy-MM-dd HH:mm:ss"
                    value-zone="Europe/Warsaw"
                    :type="'datetime'"
                    :input-class="{'form-control': true, 'label-scheduler-await-user': true}"
                >
                    <template slot="after" v-if="$v.datesToSet.$each[i].$error">
                        <p class="error" v-if="!$v.datesToSet.$each[i].date.required">To pole jest wymagane</p>
                        <p class="error" v-if="!$v.datesToSet.$each[i].date.oldDate">Data przypomnienia nie może być
                            starsza niż obecna</p>
                    </template>
                </datetime>
            </div>
        </div>
        <button class="btn btn-success" @click="sendDates">Zapisz</button>
    </div>
</template>

<script>
    import {required} from 'vuelidate/lib/validators'

    export default {
        name: "label-scheduler-await-user",
        props: [
            'userId'
        ],
        data() {
            return {
                active: false,
                datesToSet: []
            }
        },
        methods: {
            sendDates() {
                this.$v.$touch();

                if (this.$v.$invalid) {
                    return;
                }

                window.axios.post('/api/set-scheduled-times', {"sendDates": this.datesToSet})
                    .then(response => {
                        this.active = false;
                    });
            }
        },
        watch: {
            active(value) {
                if (value) {
                    window.scrollTo(0, 0);
                    document.body.style.overflow = "hidden"
                } else {
                    document.body.style.overflow = "auto"
                }
            }
        },
        mounted() {
            this.active = false;
            window.axios.get('/api/get-labels-scheduler-await/' + this.userId)
                .then(response => {
                    if (response.data.length) {
                        this.datesToSet = response.data;
                        this.active = true;
                    }
                });
        },
        validations() {
            return {
                datesToSet: {
                    $each: {
                        date: {
                            required,
                            oldDate(val) {
                                return this.$luxon.fromISO(val) > this.$luxon.local();
                            }
                        }
                    }
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .fullscreen {
        position: absolute;
        background-color: #fff;
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    h3 {
        margin-bottom: 20px;
    }

    .await-container {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

        p {
            color: #000;
            padding: 0;
            margin: 0;

            &.error {
                color: #db0400;
            }
        }

        /deep/ .label-scheduler-await-user {
            width: 250px;
            text-align: center;
        }
    }

</style>
