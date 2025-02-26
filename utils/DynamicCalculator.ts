export default class DynamicCalculator {
  selectedCommercial: number | undefined;
  selectedCalculation: any;
  selectedConsumption: any;
  bv: any;
  bw: any;
  bx: any;

  updateCalculator(value: number, name: string) {
    if (name === "selectedCommercial") {
      value = Math.ceil(value);
      return {
        selectedCommercial: value,
        selectedCalculation: this.calculateCalculation(
          value,
          this.bv,
          this.selectedConsumption
        ),
        selectedBasicUnit: this.calculateBasicUnit(value, this.bv),
        selectedCollective: this.calculateCollective(value, this.bw),
        selectedUnitBiggest: this.calculateUnitBiggest(value, this.bx),
        selectedConsumption: this.selectedConsumption,
      };
    } else if (name === "selectedCalculation") {
      return {
        selectedCommercial: this.calculateCommercialForConsumption(
          value,
          this.selectedConsumption,
          this.bv
        ),
        selectedBasicUnit: this.calculateBasicUnit(
          this.selectedCommercial!,
          this.bv
        ),
        selectedCalculation: this.calculateCalculation(
          this.selectedCommercial!,
          this.bv,
          this.selectedConsumption
        ),
        selectedCollective: this.calculateCollective(
          this.selectedCommercial!,
          this.bw
        ),
        selectedUnitBiggest: this.calculateUnitBiggest(
          this.selectedCommercial!,
          this.bx
        ),
        selectedConsumption: this.selectedConsumption,
      };
    } else if (name === "selectedBasicUnit") {
      return {
        selectedCommercial: this.calculateCommercialWithMultiplication(
          value,
          this.bv
        ),
        selectedCalculation: this.calculateCalculation(
          this.selectedCommercial!,
          this.bv,
          this.selectedConsumption
        ),
        selectedCollective: this.calculateCollective(
          this.selectedCommercial!,
          this.bw
        ),
        selectedUnitBiggest: this.calculateUnitBiggest(
          this.selectedCommercial!,
          this.bx
        ),
        selectedConsumption: this.selectedConsumption,
        selectedBasicUnit: this.calculateBasicUnit(
          this.selectedCommercial!,
          this.bv
        ),
      };
    } else if (name === "selectedCollective") {
      return {
        selectedCommercial: this.calculateCommercialWithReversedMultiplication(
          value,
          this.bw
        ),
        selectedCalculation: this.calculateCalculation(
          this.selectedCommercial!,
          this.bv,
          this.selectedConsumption
        ),
        selectedBasicUnit: this.calculateBasicUnit(
          this.selectedCommercial!,
          this.bv
        ),
        selectedUnitBiggest: this.calculateUnitBiggest(
          this.selectedCommercial!,
          this.bx
        ),
        selectedConsumption: this.selectedConsumption,
        selectedCollective: this.calculateCollective(
          this.selectedCommercial!,
          this.bw
        ),
      };
    } else if (name === "selectedUnitBiggest") {
      return {
        selectedCommercial: this.calculateCommercialWithReversedMultiplication(
          value,
          this.bx
        ),
        selectedCalculation: this.calculateCalculation(
          this.selectedCommercial!,
          this.bv,
          this.selectedConsumption
        ),
        selectedBasicUnit: this.calculateBasicUnit(
          this.selectedCommercial!,
          this.bv
        ),
        selectedCollective: this.calculateCollective(
          this.selectedCommercial!,
          this.bw
        ),
        selectedConsumption: this.selectedConsumption,
        selectedUnitBiggest: this.calculateUnitBiggest(
          this.selectedCommercial!,
          this.bx
        ),
      };
    } else if (name === "selectedConsumption") {
      return {
        selectedConsumption: value,
        selectedCommercial: this.calculateCommercialForConsumption(
          this.selectedCalculation,
          value,
          this.bv
        ),
        selectedCalculation: this.calculateCalculation(
          this.selectedCommercial!,
          this.bv,
          this.selectedConsumption
        ),
        selectedBasicUnit: this.calculateBasicUnit(
          this.selectedCommercial!,
          this.bv
        ),
        selectedCollective: this.calculateCollective(
          this.selectedCommercial!,
          this.bw
        ),
        selectedUnitBiggest: this.calculateUnitBiggest(
          this.selectedCommercial!,
          this.bx
        ),
      };
    }
  }

  calculateCommercialForConsumption(
    calculation: number,
    consumption: number,
    bv: number
  ) {
    this.selectedCommercial! = Math.ceil((calculation * consumption) / bv);
    return this.selectedCommercial!;
  }

  calculateCalculation(commercial: number, bv: number, consumption: number) {
    return commercial * parseFloat((bv / consumption).toFixed(2));
  }

  calculateBasicUnit(commercial: number, bv: number) {
    return commercial * bv;
  }

  calculateCollective(commercial: number, bw: number) {
    return (commercial / bw).toFixed(2);
  }

  calculateUnitBiggest(commercial: number, bj: number) {
    return (commercial / bj).toFixed(2);
  }

  calculateCommercialWithMultiplication(
    commercial: number,
    multiplication: number
  ) {
    this.selectedCommercial! = Math.ceil(commercial / multiplication);
    return this.selectedCommercial!;
  }

  calculateCommercialWithReversedMultiplication(
    commercial: number,
    multiplication: number
  ) {
    this.selectedCommercial! = Math.ceil(commercial * multiplication);
    return this.selectedCommercial!;
  }
}
