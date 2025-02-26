import Package from '../types/Package';

const calculatePackages = (products: any[]): Package => {
    let GLSks = 0;
    let GLSkd = 0;
    let DPDd = 0;

    products.forEach((item: any) => {
        switch(item.delivery_type) {
            case 'GLS':
                GLSks += item.amount / item.assortment_quantity;
                break;
            case 'GLSd':
                GLSkd += item.amount / item.assortment_quantity;
                break;
            case 'DPDd':
                DPDd += item.amount / item.assortment_quantity;
                break;
        }
    });

    return {
        GLSks: GLSks,
        GLSkd: GLSkd,
        DPDd: DPDd
    }
}

export default calculatePackages;
