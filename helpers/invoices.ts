import dowloadInvoiceResponse from "~/types/DowloadInvoiceResponse";

const dowloadInvoices = async (orderId: bigint) => {
    const { $shopApi: shopApi } = useNuxtApp();

    const { data: index } = await shopApi.get(`api/order/invoice/${orderId}`) as dowloadInvoiceResponse;

    index.forEach((invoice) => {
       window.open(invoice.url, "_blank");
    });
}

export {
    dowloadInvoices
}
