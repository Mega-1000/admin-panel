<script setup lang="ts">
import DynamicCalculator from "~~/utils/DynamicCalculator";
import emmiter from "~/helpers/emitter";

const props = defineProps<{
  product: any;
  handleProductAmount: (val: any) => any;
}>();

const blurChange = ref<any>();

const dynamicCalculator = new DynamicCalculator();

const state = ref<any>({
  selectedCommercial: 1,
});

onMounted(() => {
  if (!props.product)
    state.value = {
      selectedCommercial: 1,
    };
  else {
    let state_: any = {};
    const bf =
      props.product.calculation_unit == null
        ? 0
        : props.product.calculation_unit;
    const bg = props.product.unit_basic == null ? 0 : props.product.unit_basic;
    const bh =
      props.product.unit_commercial == null ? 0 : props.product.unit_commercial;
    const bi =
      props.product.unit_of_collective == null
        ? 0
        : props.product.unit_of_collective;
    const bj =
      props.product.unit_biggest == null ? 0 : props.product.unit_biggest;

    const bu =
      props.product.unit_consumption == null
        ? 0
        : props.product.unit_consumption;
    const bv =
      props.product.numbers_of_basic_commercial_units_in_pack == null
        ? 0
        : props.product.numbers_of_basic_commercial_units_in_pack;
    const bw =
      props.product.number_of_sale_units_in_the_pack == null
        ? 0
        : props.product.number_of_sale_units_in_the_pack;
    const bx =
      props.product.number_of_trade_items_in_the_largest_unit == null
        ? 0
        : props.product.number_of_trade_items_in_the_largest_unit;

    const commercialAmmount = props.product.amount || 1;
    state_ = {
      commercialName: bh == 0 ? "" : bh,
      calculationName: bf == 0 ? "" : bf,
      basicUnitName: bg == 0 ? "" : bg,
      collectiveName: bi == 0 ? "" : bi,
      unitBiggestName: bj == 0 ? "" : bj,
      bv: bv,
      bw: bw,
      bx: bx,
      selectedCommercial: commercialAmmount,
      lesserCommercial: 0,
      lesserCalculation: 0,
      lesserBasicUnit: 0,
      lesserCollective: 0,
      lesserUnitBiggest: 0,
      selectedCalculation: (
        commercialAmmount * parseFloat((bv / bu).toFixed(2))
      ).toFixed(2),
      selectedBasicUnit: commercialAmmount * bv,
      selectedCollective: (commercialAmmount / bw).toFixed(2),
      selectedUnitBiggest: (commercialAmmount / bx).toFixed(2),
      selectedConsumption: bu,
      displayConsumption: bu != 1,
      commercialPrice: props.product.gross_price_of_packing,
      calculationPrice: props.product.gross_selling_price_calculated_unit,
      basicPrice: props.product.gross_selling_price_basic_unit,
      collectivePrice: props.product.gross_selling_price_aggregate_unit,
      biggestPrice: props.product.gross_selling_price_the_largest_unit,
    };

    if (
      bf == bg &&
      bg == bh &&
      bi == "0" &&
      bj == "0" &&
      bu == 1 &&
      bv == 1 &&
      bw == 0 &&
      bx == 0
    ) {
      state_.commercial = true;
    } else if (
      bf == bg &&
      bg == bh &&
      bi != "0" &&
      bj == "0" &&
      bu == 1 &&
      bv == 1 &&
      bw != 0 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.collective = true;
    } else if (
      bf != bg &&
      bg != bh &&
      bi == "0" &&
      bj == "0" &&
      bu != 1 &&
      bv != 1 &&
      bw == 0 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
      state_.calculation = true;
    } else if (
      bf == bg &&
      bg != bh &&
      bh != bi &&
      bi != "0" &&
      bj == "0" &&
      bu == 1 &&
      bv != 1 &&
      bw != 1 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
      state_.collective = true;
    } else if (
      bf == bg &&
      bg != bh &&
      bh != bi &&
      bi != bj &&
      bj != "0" &&
      bu == 1 &&
      bv != 1 &&
      bw != 1 &&
      bx != 1
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
      state_.collective = true;
      state_.unitbiggest = true;
    } else if (
      bf == bg &&
      bg != bh &&
      bh != bi &&
      bj == "0" &&
      bu != 1 &&
      bv != 1 &&
      bw != 1 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
      state_.calculation = true;
      state_.collective = true;
    } else if (
      bf != bg &&
      bg != bh &&
      bh != bi &&
      bi != "0" &&
      bj == "0" &&
      bu != 1 &&
      bv != 1 &&
      bw != 1 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
      state_.calculation = true;
      state_.collective = true;
    } else if (
      bf == bg &&
      bg == bh &&
      bh != bi &&
      bi != bj &&
      bj != "0" &&
      bu == 1 &&
      bv == 1 &&
      bw != 1 &&
      bx != 1
    ) {
      state_.commercial = true;
      state_.collective = true;
      state_.unitbiggest = true;
    } else if (
      bf != bg &&
      bg != bh &&
      bh != bi &&
      bi != "0" &&
      bj == "0" &&
      bu == 1 &&
      bv != 1 &&
      bw != 1 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
      state_.collective = true;
    } else if (
      bf != bg &&
      bg != bh &&
      bh != bi &&
      bi == "0" &&
      bj == "0" &&
      bu == 1 &&
      bv != 1 &&
      bw == 0 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
    } else if (
      bf != bg &&
      bg == bh &&
      bh != bi &&
      bi != bj &&
      bj != "0" &&
      bu != 1 &&
      bv == 1 &&
      bw != 1 &&
      bx != 1 &&
      bx != 0
    ) {
      state_.commercial = true;
      state_.calculation = true;
      state_.collective = true;
      state_.unitbiggest = true;
    } else if (
      bf != bg &&
      bg == bh &&
      bh != bi &&
      bi != bj &&
      bj == "0" &&
      bu != 1 &&
      bv == 1 &&
      bw != 1 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.calculation = true;
      state_.collective = true;
    } else if (
      bf != bg &&
      bg == bh &&
      bh != bi &&
      bi != bj &&
      bj == "0" &&
      bu != 1 &&
      bv == 1 &&
      bw != 1 &&
      bx == 0
    ) {
      state_.commercial = true;
      state_.basicUnit = true;
      state_.calculation = true;
      state_.collective = true;
      state_.unitbiggest = true;
    }
    dynamicCalculator.bv = state_.bv;
    dynamicCalculator.bw = state_.bw;
    dynamicCalculator.bx = state_.bx;
    dynamicCalculator.selectedConsumption = state_.selectedConsumption;

    state.value = state_;
  }
});

const handleChange = (event: any) => {
  blurChange.value = true;
  state.value[event.target.name] = event.target.value.replace(",", ".");
  blurChange.value = false;

  emmiter.emit("cart:change");
};

const handleBlur = (event: any) => {
  if (blurChange.value) {
    return;
  }
  const target = event.target;
  let value = target.value;
  const name = target.name;
  if (parseFloat(value) > parseFloat(target.max)) {
    return;
  }
  if (parseFloat(value) < 0) {
    value = 0;
  }
  dynamicCalculator.selectedCommercial = state.value.selectedCommercial;
  dynamicCalculator.selectedCalculation = state.value.selectedCalculation;
  dynamicCalculator.selectedConsumption = state.value.selectedConsumption;
  var resp = dynamicCalculator.updateCalculator(value, name);
  state.value.selectedCommercial = resp!.selectedCommercial.toFixed(0);
  state.value.selectedCalculation = resp!.selectedCalculation.toFixed(2);
  state.value.selectedBasicUnit = resp!.selectedBasicUnit.toFixed(2);
  state.value.selectedCollective = parseFloat(resp!.selectedCollective).toFixed(
    2
  );
  state.value.selectedUnitBiggest = parseFloat(
    resp!.selectedUnitBiggest
  ).toFixed(2);
  state.value.selectedConsumption = parseFloat(
    resp!.selectedConsumption
  ).toFixed(2);
  props.handleProductAmount(resp!.selectedCommercial);

  emmiter.emit("cart:change");
};
</script>

<template>
  <div>
    <div
      class="shadow-md sm:rounded-lg max-w-[80vw] sm:max-w-fit overflow-x-auto"
    >
      <table
        class="w-full text-xs text-left text-black break-words sm:break-normal"
      >
        <thead class="text-xs text-gray-700 sm:uppercase bg-gray-50">
          <tr>
            <th />
            <th scope="col" class="px-1 py-1" v-if="state?.commercial">
              Handlowa [{{ state?.commercialName }}]
            </th>
            <th scope="col" class="px-1 py-1" v-if="state?.calculation">
              Obliczeniowa [{{ state?.calculationName }}]
            </th>
            <th scope="col" class="px-1 py-1" v-if="state?.basicUnit">
              Podstawowa [{{ state?.basicUnitName }}]
            </th>
            <th scope="col" class="px-1 py-1" v-if="state?.collective">
              Zbiorcza [{{ state?.collectiveName }}]
            </th>
            <th scope="col" class="px-1 py-1" v-if="state?.unitBiggest">
              Globalna [{{ state?.unitBiggestName }}]
            </th>
          </tr>
        </thead>
        <tbody>
          <tr class="bg-white border-b">
            <th scope="row" class="px-1 py-1 font-medium text-black">
              Ilość zamawianego towaru
            </th>
            <td class="px-1 py-1" v-if="state?.commercial">
              <input
                class="border border-gray-400 w-10 sm:w-20"
                name="selectedCommercial"
                :value="state?.selectedCommercial"
                :onBlur="handleBlur"
                :onChange="handleChange"
              />
            </td>
            <td class="px-1 py-1" v-if="state?.calculation">
              <input
                class="border border-gray-400 w-10 sm:w-20"
                name="selectedCalculation"
                :value="state?.selectedCalculation"
                :onBlur="handleBlur"
                :onChange="handleChange"
              />
            </td>
            <td class="px-1 py-1" v-if="state?.basicUnit">
              <input
                class="border border-gray-400 w-10 sm:w-20"
                name="selectedBasicUnit"
                :value="state?.selectedBasicUnit"
                :onBlur="handleBlur"
                :onChange="handleChange"
              />
            </td>
            <td class="px-1 py-1" v-if="state?.collective">
              <input
                class="border border-gray-400 w-10 sm:w-20"
                name="selectedCollective"
                :value="state?.selectedCollective"
                :onBlur="handleBlur"
                :onChange="handleChange"
              />
            </td>
            <td class="px-1 py-1" v-if="state?.unitBiggest">
              <input
                class="border border-gray-400 w-10 sm:w-20"
                name="selectedUnitBiggest"
                :value="state?.selectedUnitBiggest"
                :onBlur="handleBlur"
                :onChange="handleChange"
              />
            </td>
          </tr>
<!--          <tr class="bg-white border-b">-->
<!--            <th scope="row" class="px-1 py-1 font-medium text-black">-->
<!--              Ceny netto<br />-->
<!--              sprzedaży [PLN]-->
<!--            </th>-->
<!--            <td class="px-1 py-1" v-if="state?.commercial">-->
<!--              {{-->
<!--                parseFloat(-->
<!--                  props.product.net_selling_price_commercial_unit-->
<!--                ).toFixed(2)-->
<!--              }}-->
<!--            </td>-->
<!--            <td class="px-1 py-1" v-if="state?.calculation">-->
<!--              {{-->
<!--                parseFloat(-->
<!--                  props.product.net_selling_price_calculated_unit-->
<!--                ).toFixed(2)-->
<!--              }}-->
<!--            </td>-->
<!--            <td class="px-1 py-1" v-if="state?.basicUnit">-->
<!--              {{-->
<!--                parseFloat(props.product.net_selling_price_basic_unit).toFixed(-->
<!--                  2-->
<!--                )-->
<!--              }}-->
<!--            </td>-->
<!--            <td class="px-1 py-1" v-if="state?.collective">-->
<!--              {{-->
<!--                parseFloat(-->
<!--                  props.product.net_selling_price_aggregate_unit-->
<!--                ).toFixed(2)-->
<!--              }}-->
<!--            </td>-->
<!--            <td class="px-1 py-1" v-if="state?.unitBiggest">-->
<!--              {{-->
<!--                parseFloat(-->
<!--                  props.product.net_selling_price_the_largest_unit-->
<!--                ).toFixed(2)-->
<!--              }}-->
<!--            </td>-->
<!--          </tr>-->
          <tr class="bg-white border-b">
            <th scope="row" class="px-1 py-1 font-medium text-black">
              Ceny brutto<br />
              sprzedaży [PLN]
            </th>
            <td class="px-1 py-1" v-if="state?.commercial">
              {{ state?.commercialPrice }}
            </td>
            <td class="px-1 py-1" v-if="state?.calculation">
              {{ state?.calculationPrice }}
            </td>
            <td class="px-1 py-1" v-if="state?.basicUnit">
              {{ state?.basicPrice }}
            </td>
            <td class="px-1 py-1" v-if="state?.collective">
              {{ state?.collectivePrice }}
            </td>
            <td class="px-1 py-1" v-if="state?.unitBiggest">
              {{ state?.biggestPrice }}
            </td>
          </tr>
          <tr class="bg-white border-b">
            <th scope="row" class="px-1 py-1 font-medium text-black">
              Wartość brutto <br />sprzedaży [PLN]
            </th>
            <td class="px-1 py-1" v-if="state?.commercial">
              {{
                (state?.commercialPrice * state?.selectedCommercial).toFixed(2)
              }}
            </td>
          </tr>
<!--          <tr class="bg-white" v-if="state?.displayConsumption">-->
<!--            <th scope="row" class="px-1 py-1 font-medium text-black">-->
<!--              Obliczenia dokonanu przy <br />założeniu zużycia-->
<!--            </th>-->
<!--            <td class="px-1 py-1">-->
<!--              <input-->
<!--                class="border border-gray-400 w-10 sm:w-20"-->
<!--                name="selectedConsumption"-->
<!--                :value="state?.selectedConsumption"-->
<!--                :onBlur="handleBlur"-->
<!--                :onChange="handleChange"-->
<!--              />-->
<!--              {{ props.product.unit_basic }}/{{-->
<!--                props.product.calculation_unit-->
<!--              }}-->
<!--            </td>-->
<!--          </tr>-->
        </tbody>
      </table>
    </div>
  </div>
</template>
