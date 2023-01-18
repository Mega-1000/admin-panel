<?php

namespace App\Factory\Schenker;

use App\DTO\Schenker\PackageDTO;
use App\DTO\Schenker\PayerDTO;
use App\DTO\Schenker\RecipientDTO;
use App\DTO\Schenker\Request\OrderRequestDTO;
use App\DTO\Schenker\SenderDTO;
use App\DTO\Schenker\ServiceDTO;
use App\DTO\Schenker\ServiceParameterDTO;
use App\Entities\OrderPackage;
use App\Enums\Schenker\DataSource;
use App\Enums\Schenker\ProductType;
use App\Enums\Schenker\SupportedService;

class OrderRequestFactory
{
    private $orderPackage;
    private $clientId;

    public function __construct(OrderPackage $orderPackage)
    {
        $this->orderPackage = $orderPackage;
        $this->clientId = config('integrations.schenker.client_id');
    }

    /**
     * a - szerokość
     * b - długość
     * c - wysokość
     */
    public function __invoke(): OrderRequestDTO
    {
        return new OrderRequestDTO(
            $this->clientId,
            null,
            DataSource::API,
            null,
            ProductType::SYSTEM,
            $this->orderPackage->delivery_date,
            $this->orderPackage->delivery_date->endOfDay(),
            $this->orderPackage->shipment_date,
            $this->orderPackage->shipment_date->endOfDay(),
            $this->orderPackage->notices,
            $this->orderPackage->notices,
            $this->prepareSenderData(),
            $this->prepareRecipientData(),
            $this->preparePayerData(),
            $this->preparePackageData(),
            false,
            null,
            null,
            $this->prepareServicesData(),
            null
        );
    }

    private function prepareSenderData(): SenderDTO
    {
        $senderConfigData = config('shipping.sender');
        return new SenderDTO(
            $this->clientId,
            null,
            $senderConfigData['name'],
            $senderConfigData['post_code'],
            $senderConfigData['city'],
            $senderConfigData['street'],
            $senderConfigData['house_number'],
            $senderConfigData['local_number'],
            $senderConfigData['phone'],
            $senderConfigData['tax_number'],
            $senderConfigData['contact_person'],
            $senderConfigData['email'],
            null
        );
    }

    private function prepareRecipientData(): RecipientDTO
    {
        $deliveryAddress = $this->orderPackage->order->deliveryAddress;
        return new RecipientDTO(
            '',
            null,
            $deliveryAddress->firmname ?? $deliveryAddress->firstname . ' ' . $deliveryAddress->lastname,
            $deliveryAddress->postal_code,
            $deliveryAddress->city,
            $deliveryAddress->address,
            $deliveryAddress->flat_number,
            null, // number is in flat_number variable
            $deliveryAddress->phone,
            $deliveryAddress->nip,
            $deliveryAddress->firstname . ' ' . $deliveryAddress->lastname,
            $deliveryAddress->email,
            null
        );
    }

    private function preparePayerData(): PayerDTO
    {
        $payerConfigData = config('shipping.payer');
        return new PayerDTO(
            $this->clientId,
            null,
            $payerConfigData['name'],
            $payerConfigData['post_code'],
            $payerConfigData['city'],
            $payerConfigData['street'],
            $payerConfigData['house_number'],
            $payerConfigData['local_number'],
            $payerConfigData['phone'],
            $payerConfigData['tax_number'],
            $payerConfigData['contact_person'],
            $payerConfigData['email'],
            null
        );
    }

    /**
     * @return PackageDTO[]
     */
    private function preparePackageData(): array
    {
        return [
            new PackageDTO(
                null,
                $this->orderPackage->id,
                $this->orderPackage->container_type,
                $this->orderPackage->quantity,
                '',
                $this->orderPackage->weight,
                $this->orderPackage->size_a * $this->orderPackage->size_b * $this->orderPackage->size_c,
                $this->orderPackage->size_a,
                $this->orderPackage->size_b,
                $this->orderPackage->size_c,
                '',
                $this->orderPackage->shape !== 'standard' ? $this->orderPackage->notices : ''
            ),
        ];
    }

    /**
     * @return ServiceDTO[]
     */
    private function prepareServicesData(): array
    {
        $services = [];

        $servicesParams = $this->generateAllServices();

        $order = $this->orderPackage->order;
        return ServiceFactory::getServicesFromString($this->orderPackage->services, $servicesParams);
    }

    /**
     * @return ServiceParameterDTO[]
     */
    private function generateAllServices(): array
    {
        $serviceParam = [];
        $order = $this->orderPackage->order;
        if (($order->deliveryAddress->email ?? '') !== '') {
            $serviceParam[SupportedService::TYPE_EMAIL_NOTIFICATIONS] = new ServiceParameterDTO($order->deliveryAddress->email);
        }
        if (($order->deliveryAddress->phone ?? '') !== '') {
            $serviceParam[SupportedService::TYPE_EMAIL_NOTIFICATIONS] = new ServiceParameterDTO($order->deliveryAddress->phone);
        }
        if ($this->orderPackage->cash_on_delivery > 0) {
            $serviceParam[SupportedService::TYPE_PAYMENT_ON_DELIVERY] = new ServiceParameterDTO($this->orderPackage->cash_on_delivery);
        }

        return $serviceParam;
    }

}
