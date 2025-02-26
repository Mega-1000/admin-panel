interface DownloadInvoiceResponse {
    data: Array<{
        name: string;
        url: string;
    }>;
}

export default DownloadInvoiceResponse;
