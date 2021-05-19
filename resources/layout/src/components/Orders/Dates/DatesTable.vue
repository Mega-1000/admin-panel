<template>
  <div class="row">
    <div class="col-lg-12 text-center">
      <table class="table">
        <thead>
        <tr>
          <th scope="col" style="width: 20%;"></th>
          <th scope="col" style="width: 5%;" class="text-center"></th>
          <th scope="col" style="width: 15%;" class="text-center">Klient</th>
          <th scope="col" style="width: 15%;" class="text-center">Konsultant</th>
          <th scope="col" style="width: 15%;" class="text-center">Magazyn</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <th scope="row" rowspan="2" style="vertical-align: middle;">Preferowana data nadania</th>
          <th scope="row">Od</th>
          <td>d</td>
          <td>e</td>
          <td>f</td>
        </tr>
        <tr>
          <th scope="row">Do</th>
          <td>a</td>
          <td>b</td>
          <td>c</td>
        </tr>
        <tr>
          <th scope="row" rowspan="2" style="vertical-align: middle;">Preferowana data dostawy</th>
          <th scope="row">Od</th>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <th scope="row">Do</th>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <th scope="row" colspan="2" style="text-align: center;">Akceptacja</th>
          <td>
                    <span
                      class="glyphicon glyphicon"></span>
          </td>
          <td>
                    <span
                      class="glyphicon glyphicon"></span>
          </td>
          <td>
                    <span
                      class="glyphicon glyphicon"></span>
          </td>
        </tr>
        <tr>
          <th scope="row" colspan="2" style="text-align: center;">Akcje</th>
          <td>
            <a
              class="btn btn-sm btn-primary">
              Modyfikuj
            </a>
            <a
              class="btn btn-sm btn-success">
              Akceptuj
            </a>
          </td>

          <td>
            <a class="btn btn-sm btn-primary">
              Modyfikuj
            </a>
            <a class="btn btn-sm btn-success">
              Akceptuj
            </a>
          </td>
          <td>
            <a class="btn btn-sm btn-primary">
              Modyfikuj
            </a>
            <a class="btn btn-sm btn-success">
              Akceptuj
            </a>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'

@Component({
  components: {}
})
export default class OrderDatesTable extends Vue {

  public get dates(): Dates[]{
    return this.$store?.getters['OrdersService/dates']
  }
}
</script>
