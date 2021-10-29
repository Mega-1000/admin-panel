<template>
  <div class="c-transactionsForm">
    <div class="row">
      <div class="col-md-10">
        <h4>Dodawanie transakcji</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group ">
          <label for="operationKind" class="col-md-5 col-form-label">Rodzaj operacji</label>
          <div class="col-md-5">
            <select v-model="operationKind" id="operationKind" class="form-control">
              <option selected value="">-- wybierz --</option>
              <option v-for="(operation,index) in operations" :key="index" :value="operation">
                {{ operation }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="paymentId" class="col-md-5 col-form-label">Identyfikator płatności</label>
          <div class="col-md-5">
            <input type="text" id="paymentId" name="paymentId" class="form-control" v-model="paymentId">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="paymentId" class="col-md-5 col-form-label">Wartość operacji</label>
          <div class="col-md-5">
            <input type="text" id="operationValue" class="form-control" name="operationValue" v-model="operationValue">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group ">
          <label for="registrationInBankDate" class="col-md-5 col-form-label">Operator płatności</label>
          <div class="col-md-5">
            <input type="text" id="operator" name="operator" class="form-control" v-model="operator">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group ">
          <label for="orderId" class="col-md-5 col-form-label">Identyfikator zamówienia</label>
          <div class="col-md-5">
            <select v-model="orderId" id="orderId" class="form-control">
              <option selected value="">-- wybierz --</option>
              <option v-for="(orderId,index) in orderIds" :key="index" :value="orderId">
                {{ orderId }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group ">
          <label for="registrationInSystemDate" class="col-md-5 col-form-label">Data rejestracji w systemie</label>
          <div class="col-md-5">
            <date-picker
                    name="registrationInSystemDate"
                    format="YYYY-MM-DD"
                    id="registrationInSystemDate"
                    type="date"
                    valueType="format"
                    v-model="registrationInSystemDate"
            ></date-picker>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="registrationInBankDate" class="col-md-5 col-form-label">Data rejestracji w banku</label>
          <div class="col-md-5">
            <date-picker
                    name="registrationInBankDate"
                    format="YYYY-MM-DD"
                    id="registrationInBankDate"
                    type="date"
                    valueType="format"
                    v-model="registrationInBankDate"
            ></date-picker>
          </div>
        </div>
      </div>
    </div>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Customer } from '@/types/TransactionsTypes'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/pl'

@Component({
  components: {
    DatePicker
  }
})
export default class TransactionsForm extends Vue {
  public operations: string[] = ['Wpłata', 'Wypłata', 'Obciążenie', 'Uznanie'];

  public registrationInSystemDate = ''
  public registrationInBankDate = ''
  public paymentId = ''
  public operator = ''
  public operationValue = ''
  public operationKind = ''
  public orderId = ''
  public orderIds = []

  public get customer (): Customer {
    return this.$store?.getters['TransactionsService/customer']
  }
  public async mounted (): Promise<void> {

  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

</style>
