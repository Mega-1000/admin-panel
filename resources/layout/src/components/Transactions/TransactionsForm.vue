<template>
  <div class="c-transactionsForm">
    <div class="row">
      <div class="col-md-10">
        <h4>Dodawanie transakcji</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" v-if="error.length">
          <span>{{ error }}</span>
        </div>
      </div>
    </div>
    <form class="needs-validation" ref="form" @submit.prevent novalidate>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': operationKind.error===true},{'has-success': operationKind.error===false && operationKind.value !== ''}]">
            <label for="operationKind" class="col-md-5 col-form-label">Rodzaj operacji</label>
            <div class="col-md-5">
              <select v-model="operationKind.value" required @change="operationValue.value = ''"
                      id="operationKind" class="form-control">
                <option selected value="">-- wybierz --</option>
                <option v-for="(operation,index) in operations" :key="index" :value="operation">
                  {{ operation }}
                </option>
              </select>
              <span class="form-control-feedback">{{ operationKind.errorMessage }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': paymentId.error===true},{'has-success': paymentId.error===false && paymentId.value !== ''}]">
            <label for="paymentId" class="col-md-5 col-form-label">Identyfikator płatności</label>
            <div class="col-md-5">
              <input type="text" id="paymentId" name="paymentId" class="form-control" v-model="paymentId.value">
              <span class="form-control-feedback">{{ paymentId.errorMessage }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': operationValue.error===true},{'has-success': operationValue.error===false && operationValue.value !== ''}]">
            <label for="paymentId" class="col-md-5 col-form-label">Wartość operacji</label>
            <div class="col-md-5">
              <input type="text" required id="operationValue" class="form-control" name="operationValue"
                     v-model="operationValue.value">
              <span class="form-control-feedback">{{ operationValue.errorMessage }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': operator.error===true},{'has-success': operator.error===false && operator.value !== ''}]">
            <label for="registrationInBankDate"
                   class="col-md-5">Operator płatności</label>
            <div class="col-md-5">
              <input type="text" id="operator" name="operator"
                     class="form-control" v-model="operator.value">
              <span class="form-control-feedback">{{ operator.errorMessage }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': orderId.error===true},{'has-success': orderId.error===false && orderId.value !== ''}]">
            <label for="orderId" class="col-md-5 col-form-label">Identyfikator zamówienia</label>
            <div class="col-md-5">
              <select v-model="orderId.value" required id="orderId" class="form-control">
                <option value="" selected>-- wybierz --</option>
                <option value="0">Nie przypisana</option>
                <option v-for="(orderId,index) in customerIds" :key="index" :value="orderId">
                  {{ orderId }}
                </option>
              </select>
              <span class="form-control-feedback">{{ orderId.errorMessage }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-6" v-if="customer === null">
          <div class="form-group"
               :class="[{'has-error': customerId.error===true},{'has-success': customerId.error===false && customerId.value !== ''}]">
            <label for="customerId" class="col-md-5 col-form-label">Identyfikator klienta
              <span @click="showCustomerSearcher=true" class="icon voyager-search"></span>
            </label>
            <div class="col-md-5 form-row">
              <input type="text" required id="customerId" class="form-control" name="customerId"
                     v-model="customerId.value">
              <span class="form-control-feedback">{{ customerId.errorMessage }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': registrationInSystemDate.error===true},{'has-success': registrationInSystemDate.error===false && registrationInSystemDate.value !== ''}]">
            <label for="registrationInSystemDate" class="col-md-5 col-form-label">Data rejestracji w systemie</label>
            <div class="col-md-7">
              <date-picker
                      name="registrationInSystemDate"
                      format="YYYY-MM-DD"
                      id="registrationInSystemDate"
                      type="date"
                      valueType="format"
                      v-model="registrationInSystemDate.value"
                      width="100%"
              ></date-picker>
              <span class="form-control-feedback">{{ registrationInSystemDate.errorMessage }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': registrationInBankDate.error===true},{'has-success': registrationInBankDate.error===false && registrationInBankDate.value !== ''}]">
            <label for="registrationInBankDate" class="col-md-5 col-form-label">Data rejestracji w banku</label>
            <div class="col-md-7">
              <date-picker
                      name="registrationInBankDate"
                      format="YYYY-MM-DD"
                      id="registrationInBankDate"
                      type="date"
                      valueType="format"
                      v-model="registrationInBankDate.value"
                      width="100%"
              ></date-picker>
              <span class="form-control-feedback">{{ registrationInBankDate.errorMessage }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': accountingNotes.error===true},{'has-success': accountingNotes.error===false && accountingNotes.value !== ''}]">
            <label for="accountingNotes" class="col-md-5 col-form-label">Notatki księgowe</label>
            <div class="col-md-7">
            <textarea id="accountingNotes" class="form-control" name="accountingNotes"
                      v-model="accountingNotes.value" rows="8"></textarea>
              <span class="form-control-feedback">{{ accountingNotes.errorMessage }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group"
               :class="[{'has-error': transactionNotes.error===true},{'has-success': transactionNotes.error===false && transactionNotes.value !== ''}]">
            <label for="transactionNotes" class="col-md-5 col-form-label">Notatki transakcyjne</label>
            <div class="col-md-7">
            <textarea id="transactionNotes" class="form-control" name="transactionNotes"
                      v-model="transactionNotes.value" rows="8"></textarea>
              <span class="form-control-feedback">{{ transactionNotes.errorMessage }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 btn-group">
          <button v-if="transaction === null" type="submit" class="btn btn-primary" @click="saveTransaction">Zapisz
          </button>
          <button v-else type="submit" class="btn btn-primary" @click="updateTransaction">Zaktualizuj
          </button>
          <button class="btn btn-default" @click="$emit('back')">Powrót</button>
        </div>
      </div>
    </form>
    <searcher v-if="showCustomerSearcher" @selected="selectCustomer" @close="toggleShowModal()"></searcher>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { CreateTransactionParams, Customer, Transaction } from '@/types/TransactionsTypes'
import { Customer as SearchedCustomer } from '@/types/CustomersTypes'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/pl'
import Searcher from '@/components/Customers/Searcher.vue'

@Component({
  components: {
    Searcher,
    DatePicker
  }
})
export default class TransactionsForm extends Vue {
  private operations = {
    paymentIn: 'wpłata',
    paymentOut: 'wypłata',
    charge: 'obciążenie',
    credit: 'uznanie',
    transfer: 'przeksięgowanie'
  };

  private showCustomerSearcher = false

  private registrationInSystemDate = {
    value: (new Date()).toISOString(),
    error: false,
    errorMessage: ''
  }

  private registrationInBankDate = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private paymentId = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private operator = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private operationValue = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private operationKind = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private orderId = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private accountingNotes = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private transactionNotes = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private customerId = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private orderIds: number[] | null = []

  public get customer (): Customer {
    return this.$store?.getters['TransactionsService/customer']
  }

  public get transaction (): Transaction {
    return this.$store?.getters['TransactionsService/transaction']
  }

  public get error (): string {
    return this.$store.getters['TransactionsService/error']
  }

  public get customerIds (): number[] {
    if (this.customer === null) {
      return this.orderIds ?? []
    } else {
      return this.customer.orderIds
    }
  }

  public async saveTransaction (): Promise<void> {
    const params: CreateTransactionParams = {
      id: null,
      registrationInSystemDate: this.registrationInSystemDate.value,
      registrationInBankDate: this.registrationInBankDate.value,
      paymentId: this.paymentId.value,
      operationKind: this.operationKind.value,
      customerId: (this.customer === null) ? parseInt(this.customerId.value) : this.customer.id,
      orderId: this.orderId.value,
      operator: this.operator.value,
      operationValue: this.operationValue.value,
      accountingNotes: this.accountingNotes.value,
      transactionNotes: this.transactionNotes.value
    }
    const response = await this.$store.dispatch('TransactionsService/storeTransaction', params)
    if (response.errorCode === 442) {
      for (const [key, variable] of Object.entries(this.$data)) {
        if (Object.prototype.hasOwnProperty.call(variable, 'error')) {
          if (Object.keys(response.errors).includes(key)) {
            variable.error = true
            variable.errorMessage = response.errors[key][0]
          } else if (variable.value === '') {
            variable.error = null
            variable.errorMessage = ''
          } else {
            variable.error = false
            variable.errorMessage = ''
          }
        }
      }
    } else {
      this.$emit('transactionAdded')
    }
  }

  public async updateTransaction (): Promise<void> {
    const params: CreateTransactionParams = {
      id: this.transaction.id,
      registrationInSystemDate: this.registrationInSystemDate.value,
      registrationInBankDate: this.registrationInBankDate.value,
      paymentId: this.paymentId.value,
      operationKind: this.operationKind.value,
      customerId: (this.customer === null) ? parseInt(this.customerId.value) : this.customer.id,
      orderId: this.orderId.value,
      operator: this.operator.value,
      operationValue: this.operationValue.value,
      accountingNotes: this.accountingNotes.value,
      transactionNotes: this.transactionNotes.value
    }
    const response = await this.$store.dispatch('TransactionsService/updateTransaction', params)
    if (response.errorCode === 442) {
      for (const [key, variable] of Object.entries(this.$data)) {
        if (Object.prototype.hasOwnProperty.call(variable, 'error')) {
          if (Object.keys(response.errors).includes(key)) {
            variable.error = true
            variable.errorMessage = response.errors[key][0]
          } else if (variable.value === '') {
            variable.error = null
            variable.errorMessage = ''
          } else {
            variable.error = false
            variable.errorMessage = ''
          }
        }
      }
    } else {
      await this.$store?.dispatch('TransactionsService/setTransaction', null)
      this.$emit('transactionAdded')
    }
  }

  public async selectCustomer (customer: SearchedCustomer): Promise<void> {
    this.customerId.value = customer.id.toString()
    this.orderIds = customer.ordersIds ?? []
    this.toggleShowModal()
  }

  public toggleShowModal (): void {
    this.showCustomerSearcher = !this.showCustomerSearcher
  }

  public async mounted (): Promise<void> {
    if (this.transaction !== null) {
      this.accountingNotes.value = this.transaction.accountingNotes
      this.operationKind.value = this.transaction.operationKind
      this.operationValue.value = this.transaction.operationValue.toString().replace('-', '')
      this.operator.value = this.transaction.operator
      this.orderId.value = this.transaction.orderId.toString()
      this.paymentId.value = this.transaction.paymentId
      this.registrationInBankDate.value = this.transaction.registrationInBankDate
      this.registrationInSystemDate.value = this.transaction.registrationInSystemDate
      this.transactionNotes.value = this.transaction.transactionNotes
      /**
       * W wolnej chwili do modyfikacji
       */
      // for (const [key, variable] of Object.entries(this.$data)) {
      //   if (Object.prototype.hasOwnProperty.call(variable, 'value')) {
      //     variable.value = Object.entries(this.transaction)[key]
      //   }
      // }
    }
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .form-control-feedback {
    position: static;
    display: inline;
  }

  .voyager-search {
    cursor: pointer;
  }
</style>
