<script setup lang="ts">
import DynamicCalculator from "~~/utils/DynamicCalculator";
import emitter from "~/helpers/emitter";

const currentItem = ref();

const blurChange = ref<any>();

const dynamicCalculator = new DynamicCalculator();


const state = ref<any>({
  selectedCommercial: 1,
});

const recalculate = () => {
  if (!currentItem.value)
    state.value = {
      selectedCommercial: 1,
    };
  else {
    let state_: any = {};
    const bf =
        currentItem.value.calculation_unit == null
            ? 0
            : currentItem.value.calculation_unit;
    const bg =
        currentItem.value.unit_basic == null ? 0 : currentItem.value.unit_basic;
    const bh =
        currentItem.value.unit_commercial == null
            ? 0
            : currentItem.value.unit_commercial;
    const bi =
        currentItem.value.unit_of_collective == null
            ? 0
            : currentItem.value.unit_of_collective;
    const bj =
        currentItem.value.unit_biggest == null
            ? 0
            : currentItem.value.unit_biggest;

    const bu =
        currentItem.value.unit_consumption == null
            ? 0
            : currentItem.value.unit_consumption;
    const bv =
        currentItem.value.numbers_of_basic_commercial_units_in_pack == null
            ? 0
            : currentItem.value.numbers_of_basic_commercial_units_in_pack;
    const bw =
        currentItem.value.number_of_sale_units_in_the_pack == null
            ? 0
            : currentItem.value.number_of_sale_units_in_the_pack;
    const bx =
        currentItem.value.number_of_trade_items_in_the_largest_unit == null
            ? 0
            : currentItem.value.number_of_trade_items_in_the_largest_unit;

    const commercialAmmount = currentItem.value.amount || 1;
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
      commercialPrice: currentItem.value.gross_price_of_packing,
      calculationPrice: currentItem.value.gross_selling_price_calculated_unit,
      basicPrice: currentItem.value.gross_selling_price_basic_unit,
      collectivePrice: currentItem.value.gross_selling_price_aggregate_unit,
      biggestPrice: currentItem.value.gross_selling_price_the_largest_unit,
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
}

watch(currentItem, () => {
  recalculate();
})

onMounted(() => {
  currentItem.value = JSON.parse(localStorage.getItem('currentItem') as string)
  recalculate();
});

const handleChange = (event: any) => {
  blurChange.value = true;
  state.value[event.target.name] = event.target.value.replace(",", ".");
  blurChange.value = false;
};

const productAmount = useProductAmount();

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
  productAmount.value = resp!.selectedCommercial;

  var commercialLesser =
      state.value.selectedCommercial - 1 >= 0
          ? state.value.selectedCommercial - 1
          : 0;
  var respLesser = dynamicCalculator.updateCalculator(
      commercialLesser,
      "selectedCommercial"
  );
  state.value.lesserCommercial = respLesser!.selectedCommercial.toFixed(0);
  state.value.lesserCalculation = respLesser!.selectedCalculation.toFixed(2);
  state.value.lesserBasicUnit = respLesser!.selectedBasicUnit.toFixed(2);
  state.value.lesserCollective = parseFloat(
      respLesser!.selectedCollective
  ).toFixed(2);
  state.value.lesserUnitBiggest = parseFloat(
      respLesser!.selectedUnitBiggest
  ).toFixed(2);
};
</script>


<template>
  <div class="mt-2 hidden md:block">
    Wpisz ilość w jedno w pól aby przeliczyć ilości
  </div>
  <span class="md:hidden">
    Przesuwaj kalkulator w poziomie, przesuwając palcem lub kursorem w lewo i w prawo.
  </span>

  <div class="overflow-x-auto md:w-full shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
      <tr>
        <th />
        <th scope="col" class="px-3 py-3" v-if="state?.commercial">
          Handlowa [{{ state?.commercialName }}]
        </th>
        <th scope="col" class="px-3 py-3" v-if="state?.calculation">
          Obliczeniowa [{{ state?.calculationName }}]
        </th>
        <th scope="col" class="px-3 py-3" v-if="state?.basicUnit">
          Podstawowa [{{ state?.basicUnitName }}]
        </th>
        <th scope="col" class="px-3 py-3" v-if="state?.collective">
          Zbiorcza [{{ state?.collectiveName }}]
        </th>
        <th scope="col" class="px-3 py-3" v-if="state?.unitBiggest">
          Globalna [{{ state?.unitBiggestName }}]
        </th>
      </tr>
      </thead>
      <tbody>
      <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-3 px-1 font-medium text-gray-900 whitespace-nowrap"
        >
          Ilość zamawianego towaru
        </th>
        <td class="px-3 px-1" v-if="state?.commercial">
          <input
              class="border border-gray-400"
              name="selectedCommercial"
              :value="state?.selectedCommercial"
              :onBlur="handleBlur"
              :onChange="handleChange"
          />
        </td>
        <td class="px-3 px-1" v-if="state?.calculation">
          <input
              class="border border-gray-400"
              name="selectedCalculation"
              :value="state?.selectedCalculation"
              :onBlur="handleBlur"
              :onChange="handleChange"
          />
        </td>
        <td class="px-3 px-1" v-if="state?.basicUnit">
          <input
              class="border border-gray-400"
              name="selectedBasicUnit"
              :value="state?.selectedBasicUnit"
              :onBlur="handleBlur"
              :onChange="handleChange"
          />
        </td>
        <td class="px-3 px-1" v-if="state?.collective">
          <input
              class="border border-gray-400"
              name="selectedCollective"
              :value="state?.selectedCollective"
              :onBlur="handleBlur"
              :onChange="handleChange"
          />
        </td>
        <td class="px-3 px-1" v-if="state?.unitBiggest">
          <input
              class="border border-gray-400"
              name="selectedUnitBiggest"
              :value="state?.selectedUnitBiggest"
              :onBlur="handleBlur"
              :onChange="handleChange"
          />
        </td>
      </tr>
      <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-3 px-1 font-medium text-gray-900 whitespace-nowrap"
        >
          Ceny brutto sprzedaży [PLN]
        </th>
        <td class="px-3 px-1" v-if="state?.commercial">
          {{ state?.commercialPrice }}
        </td>
        <td class="px-3 px-1" v-if="state?.calculation">
          {{ state?.calculationPrice }}
        </td>
        <td class="px-3 px-1" v-if="state?.basicUnit">
          {{ state?.basicPrice }}
        </td>
        <td class="px-3 px-1" v-if="state?.collective">
          {{ state?.collectivePrice }}
        </td>
        <td class="px-3 px-1" v-if="state?.unitBiggest">
          {{ state?.biggestPrice }}
        </td>
      </tr>
      <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-3 px-1 font-medium text-gray-900 whitespace-nowrap"
        >
          Wartość brutto sprzedaży [PLN]
        </th>
        <td class="px-3 px-1" v-if="state?.commercial">
          {{
            (state?.commercialPrice * state?.selectedCommercial).toFixed(2)
          }}
        </td>
      </tr>
      <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-3 px-1 font-medium text-gray-900 whitespace-nowrap"
        >
          Ilość przy zamówieniu 1 <br />
          opakowania handlowego mniej
        </th>
        <td class="px-3 px-1" v-if="state?.commercial">
          {{ state?.lesserCommercial }}
        </td>
        <td class="px-3 px-1" v-if="state?.calculation">
          {{ state?.lesserCalculation }}
        </td>
        <td class="px-3 px-1" v-if="state?.basicUnit">
          {{ state?.lesserBasic }}
        </td>
        <td class="px-3 px-1" v-if="state?.collective">
          {{ state?.lesserCollective }}
        </td>
        <td class="px-3 px-1" v-if="state?.unitBiggest">
          {{ state?.lesserBiggest }}
        </td>
      </tr>
      <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-3 px-1 font-medium text-gray-900 whitespace-nowrap"
        >
          Wartość brutto sprzedaży przy<br />
          zamówieniu 1 opakowania<br />
          handlowego mniej [PLN]
        </th>
        <td class="px-3 px-1" v-if="state?.commercial">
          {{ (state?.commercialPrice * state?.lesserCommercial).toFixed(2) }}
        </td>
      </tr>
      <tr class="bg-white" v-if="state?.displayConsumption">
        <th
            scope="row"
            class="px-3 px-1 font-medium text-gray-900 whitespace-nowrap"
        >
          Obliczenia dokonanu przy <br />założeniu zużycia
        </th>
        <td class="px-3 px-1">
          <input
              class="border border-gray-400"
              name="selectedConsumption"
              :value="state?.selectedConsumption"
              :onBlur="handleBlur"
              :onChange="handleChange"
          />
          {{ currentItem.unit_basic }}/{{ currentItem.calculation_unit }}
        </td>
      </tr>
      </tbody>
    </table>

    <SubmitButton class="mt-4 hidden md:block">
      Przelicz ceny
    </SubmitButton>
  </div>
</template>
