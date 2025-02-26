import PackageCalculator from "~/helpers/PackageCalculator";
const shipmentCostBrutto = (items: any) => {
    const { GLSkd, GLSks, DPDd } = PackageCalculator(items);

    return Math.ceil(GLSkd) * 18 + Math.ceil(GLSks) * 18 + Math.ceil(DPDd) * 48;
};


export default shipmentCostBrutto;
