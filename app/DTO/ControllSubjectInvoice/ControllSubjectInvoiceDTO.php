<?php

namespace App\DTO\ControllSubjectInvoice;

readonly final class ControllSubjectInvoiceDTO
{
    public string $k;
    public string $deliveryEndDate;
    public string $warehouseDate;
    public string $issueDate;
    public int $commentsCount;
    public string $number;
    public string $value;
    public string $flag;
    public string $buyerSymbol;
    public string $notes;
    public string $buyer;
    public string $net;
    public string $vatValue;
    public string $buyerEmail;
    public string $s;
    public string $t;
    public string $gross;
    public string $remainingPayment;
    public string $currency;
    public string $category;
    public string $title;
    public string $paymentMethod;
    public string $buyerVATIN;
    public string $accountingDocument;
    public string $mpp;
    public string $originalOrders;
    public string $orders;
    public string $flagComment;
    public string $toNonExistent;

    public function __construct(array $data)
    {
        $this->k = $data['k'];
        $this->deliveryEndDate = $data['deliveryEndDate'];
        $this->warehouseDate = $data['warehouseDate'];
        $this->issueDate = $data['issueDate'];
        $this->commentsCount = $data['commentsCount'];
        $this->number = $data['number'];
        $this->value = str_replace(' ', '', $data['value']);
        $this->flag = $data['flag'];
        $this->buyerSymbol = $data['buyerSymbol'];
        $this->notes = $data['notes'];
        $this->buyer = $data['buyer'];
        $this->net = $data['net'];
        $this->vatValue = $data['vatValue'];
        $this->buyerEmail = $data['buyerEmail'];
        $this->s = $data['s'];
        $this->t = $data['t'];
        $this->gross = $data['gross'];
        $this->remainingPayment = $data['remainingPayment'];
        $this->currency = $data['currency'];
        $this->category = $data['category'];
        $this->title = $data['title'];
        $this->paymentMethod = $data['paymentMethod'];
        $this->buyerVATIN = $data['buyerVATIN'];
        $this->accountingDocument = $data['accountingDocument'];
        $this->mpp = $data['mpp'];
        $this->originalOrders = $data['originalOrders'];
        $this->orders = $data['orders'];
        $this->flagComment = $data['flagComment'];
        $this->toNonExistent = $data['toNonExistent'];
    }
}
