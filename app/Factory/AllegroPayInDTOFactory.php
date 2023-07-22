<?php

namespace App\Factory;

use App\DTO\ImportPayIn\AllegroPayInDTO;
use Carbon\Carbon;

class AllegroPayInDTOFactory
{
    private static $paymentTypesMap = [
        "CONTRIBUTION" => "wpłata",
        "REFUND_CHARGE" => "zwrot",
        "SURCHARGE" => "dopłata"
    ];

    public static function fromAllegroCsvData(array $data): AllegroPayInDTO {
        return new AllegroPayInDTO(
            $data['data'],
            $data['operacja'],
            $data['identyfikator'],
            $data['operator'],
            $data['kupujacy'],
            $data['kwota'],
            $data['saldo'],
            $data['oferta'],
            $data['dostawa'],
            $data['data_zaksiegowania']
        );
    }

    public static function fromAllegroApiData(array $data): AllegroPayInDTO {
        $allegroIdentifier = "";
        if (array_key_exists('payment', $data)) {
            $allegroIdentifier = $data['payment']['id'];
        }

        $participantString = "";
        if (array_key_exists('participant', $data)) {
            $participant = $data['participant'];
            $participantString .= $participant['login'] . ";";
            if (array_key_exists('companyName', $participant) && !empty($participant['companyName']) && strlen($participant['companyName']) > 1) {
                $participantString .= $participant['companyName'] . ";";
            }
            $participantString .= $participant['firstName'] . " " . $participant['lastName'] . ";";
            if (array_key_exists("address", $participant)) {
                $participantString .= $participant['address']['street'];
                $participantString .= $participant['address']['postCode'] . ";";
                $participantString .= $participant['address']['city'] . ";";
            }
        }

        $offer = "";
        if (array_key_exists('offer', $data)) {
            $offer = $data['offer'];
        }
        $deliveryCost = "-";
        if (array_key_exists('deliveryCost', $data)) {
            $deliveryCost = $data['deliveryCost'];
        }

        return new AllegroPayInDTO(
            Carbon::parse($data['occurredAt'])->format('d.m.Y H:i'),
            self::$paymentTypesMap[$data['type']],
            $allegroIdentifier,
            $data['wallet']['paymentOperator'],
            $participantString,
            $data['value']['amount'] . " " . $data['value']['currency'],
            $data['wallet']['balance']['amount'] . " " . $data['wallet']['balance']['currency'],
            $offer,
            $deliveryCost,
        );
    }
}
