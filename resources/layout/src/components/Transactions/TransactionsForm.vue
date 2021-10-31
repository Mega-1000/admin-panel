<template>
  <div class="c-transactionsForm">
    <div class="row">
      <div class="col-md-10">
        <h4>Dodawanie transakcji</h4>
      </div>
    </div>
    <form class="needs-validation" ref="form" @submit.prevent novalidate>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group ">
            <label for="operationKind" class="col-md-5 col-form-label">Rodzaj operacji</label>
            <div class="col-md-5">
              <select v-model="operationKind.value" required id="operationKind" class="form-control">
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
              <input type="text" id="paymentId" name="paymentId" class="form-control" v-model="paymentId.value">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="paymentId" class="col-md-5 col-form-label">Wartość operacji</label>
            <div class="col-md-5">
              <input type="text" required id="operationValue" class="form-control" name="operationValue"
                     v-model="operationValue.value">
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group ">
            <label for="registrationInBankDate" class="col-md-5 col-form-label">Operator płatności</label>
            <div class="col-md-5">
              <input type="text" id="operator" name="operator" class="form-control" v-model="operator.value">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group ">
            <label for="orderId" class="col-md-5 col-form-label">Identyfikator zamówienia</label>
            <div class="col-md-5">
              <select v-model="orderId.value" required id="orderId" class="form-control">
                <option selected value="">-- wybierz --</option>
                <option v-for="(orderId,index) in customer.orderIds" :key="index" :value="orderId">
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
                      v-model="registrationInSystemDate.value"
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
                      v-model="registrationInBankDate.value"
              ></date-picker>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="accountingNotes" class="col-md-5 col-form-label">Notatki księgowe</label>
            <div class="col-md-7">
            <textarea id="accountingNotes" class="form-control" name="accountingNotes"
                      v-model="accountingNotes.value" rows="8"></textarea>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="transactionNotes" class="col-md-5 col-form-label">Notatki transakcyjne</label>
            <div class="col-md-7">
            <textarea id="transactionNotes" class="form-control" name="transactionNotes"
                      v-model="transactionNotes.value" rows="8"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary" @click="saveTransaction">Zapisz</button>
        </div>
      </div>
    </form>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { CreateTransactionParams, Customer, Transaction } from '@/types/TransactionsTypes'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/pl'
import { CreateSetParams } from '@/types/SetsTypes'

@Component({
  components: {
    DatePicker
  }
})
export default class TransactionsForm extends Vue {
  public operations = {
    paymentIn: 'Wpłata',
    paymentOut: 'Wypłata',
    charge: 'Obciążenie',
    credit: 'Uznanie'
  };

  public registrationInSystemDate = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public registrationInBankDate = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public paymentId = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public operator = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public operationValue = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public operationKind = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public orderId = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public accountingNotes = {
    value: '',
    error: false,
    errorMessage: ''
  }
  public transactionNotes = {
    value: '',
    error: false,
    errorMessage: ''
  }

  public get customer (): Customer {
    return this.$store?.getters['TransactionsService/customer']
  }

  public async saveTransaction () {
    const params: CreateTransactionParams = {
      postedInSystemDate: this.registrationInSystemDate.value,
      postedInBankDate: this.registrationInBankDate.value,
      paymentId: this.paymentId.value,
      kindOfOperation: this.operationKind.value,
      orderId: this.orderId.value,
      operator: this.operator.value,
      operation_value: this.operationValue.value,
      accounting_notes: this.accountingNotes.value,
      transaction_notes: this.transactionNotes.value
    }
    const response = await this.$store.dispatch('TransactionsService/storeTransaction', params)
  }
}
</script>

<style scoped lang="scss">
@import "@/assets/styles/main";

</style>
