<template>
  <div class="v-transactionsList">
    <table class="table">
      <thead>
      <tr>
        <th>Id</th>
        <th>Login</th>
        <th>Nick allegro</th>
        <th>Imię</th>
        <th>Nazwisko</th>
        <th>Nazwa firmy</th>
        <th>Numer telefonu</th>
        <th>Adres</th>
        <th>Email</th>
        <th>Status</th>
        <th>Akcje</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td>1</td>
        <td>tesad</td>
        <td>ssd</td>
        <td>ssd</td>
        <td>ssd</td>
        <td>ssd</td>
        <td>ssd</td>
        <td>ssd</td>
        <td>ssd</td>
        <td>ssd</td>
        <td>ssd</td>
      </tr>
      </tbody>
    </table>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'

@Component({
  components: {}
})
export default class TransactionsList extends Vue {
  public async mounted (): Promise<void> {
    await this.$store?.dispatch('TransactionService/loadTransaction')
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .v-setsList {
    font-family: $font-primary;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: $cl-blue2c;
  }

  .close {
    position: absolute;
    right: 15px;
    top: 15px;
  }

  span {
    font-weight: 600;
  }

  .error {
    position: fixed;
    top: 15px;
    right: 10px;
    background: $cl-rede4;
    border-radius: 10px;
    padding: 15px 40px 15px 20px;
    z-index: $index-alert;
    color: $cl-whiteff;
  }

  .message {
    margin-bottom: 0;
  }

  .btn-success {
    float: left;
    margin-bottom: 25px;
  }

  .voyager-pen,
  .voyager-trash {
    @media (min-width: 992px) {
      margin-right: 7px;
    }
  }

  .voyager-double-down,
  .voyager-double-up {
    @media (min-width: 992px) {
      margin-right: 4px;
    }
  }
</style>
