<?php

namespace App\Jobs;

use App\DTO\Messages\CreateMessageDTO;
use App\Entities\Firm;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Services\MessageService;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClauteChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ChatException
     */
    public function handle()
    {
        $message = $this->request['message'];
        $apiUrl = "https://api.anthropic.com/v1/messages";
        $apiKey = "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";
        $anthropicVersion = "2023-06-01";

        $helper = new MessagesHelper($this->request['token']);
        $chat = $helper->getChat();
        $previousMessages = [];
        $lastRole = null;

        foreach ($chat->messages as $item) {
            $role = $item->customer() ? 'user' : 'assistant';
            $message = $item->message;

            if ($role === 'user' && $lastRole === 'user') {
                // Concatenate with the last message
                $previousMessages[count($previousMessages) - 1]['content'] .= ' ' . $message;
            } else {
                // Add a new message
                $previousMessages[] = ['role' => $role, 'content' => $message];
            }

            // Update the lastRole to the current one
            $lastRole = $role;
        }

        function array_flatten($array) {
            $result = [];

            foreach ($array as $item) {
                if (is_array($item)) {
                    $result = array_merge($result, array_flatten($item));
                } else {
                    $result[] = $item;
                }
            }

            return $result;
        }

        $prompt = [[
            "role" => "user",
            "content" => '
            `You are part of my Laravel system. You have to detect if user wants to add employee of company to the chat if so provide me json response like this`
            `{ "AddCompany": "COMPANY NAME", "NoticeForUser": "change it to message for user", }`
            `if user wants to add some company which is not in list provide response like this`
            `{ "NoticeForUser": "change it to message for user", }`
            `There are only these companies: [
      "IZOLBET",
      "IZOTERM",
      "YETICO",
      "GENDERKA",
      "GRASTYR",
      "POLSTYR",
      "LUBAU",
      "STYROPMIN",
      "STYROPAK",
      "DOMSTYR",
      "STYROMAP",
      "EKOBUD",
      "TERMEX",
      "AUSTROTHERM",
      "TERMEX",
      "STYRMANN",
      "KRASBUD",
      "ALBATERM",
      "PANELTECH",
      "STYRHOP",
      "INTHERMO",
      "STYROPIANPLUS",
      "SWISSPOR",
      "TYRON",
      "ARSANIT",
      "SONAROL",
      "KNAUF",
      "STYROPIANEX",
      "EUROTERMIKA",
      "NTB",
      "EUROSTYR",
      "ENERPOR",
      "BESSER",
      "FWS",
      "EUROSTYROPIAN",
      "JUSTYR",
      "PIOTROWSKI",
      "THERMICA",
    ],
            `There is also possibility to change date of spedition in this case you have to return response like this`
            `{ "ChangeDates": "from: 25.05.2024 to: 30.05.2024", "NoticeForUser": "Zmieniłem daty klienta na: od 25.05.2024 do 30.05.2024", }`
            `If user wants to perform one of these actions otherwise return "No message" If you want to send message to user because user wants to perform one of actions but for example you need more info provide response replace notice for user with your message to get more info`
            `{ "NoticeForUser": "change it to message for user", }`
            All responses are samples which just represent the format of response not the actual response so change it accordingly.
            Today is ' . now() . '
            If ANYTHING is uncertain provide response "no response" it is very important! You have to be certain that user wants to perform one of these actions
            `Do not provide any other type of response it will break the system`
            NEVER provide response when you are not 100% certain about user ubtention to perform one of this actions NEVER REMEMBER IT WILL BREAK THE SYSTEM

            Here is previous messages from this chat: ' . implode(' ', array_flatten($previousMessages)) . '

            `prompt: "' . $message . '"`'
        ]];

        $data = [
            "model" => "claude-3-sonnet-20240229",
            "max_tokens" => 1024,
            "messages" => $prompt,
        ];

        $payload = json_encode($data);

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-key: $apiKey",
            "anthropic-version: $anthropicVersion",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            try {
                $response = json_decode(str_replace(',
}', '}', json_decode($response)->content[0]->text));

                if (isset($response->ChangeDates)) {
                    $dateRange = $response->ChangeDates;

                    if (preg_match('/from:\s*(\d{2}\.\d{2}\.\d{4})\s*to:\s*(\d{2}\.\d{2}\.\d{4})/', $dateRange, $matches)) {
                        $startDate = $matches[1];
                        $endDate = $matches[2];

                        $startDateTime = DateTime::createFromFormat('d.m.Y', $startDate);
                        $endDateTime = DateTime::createFromFormat('d.m.Y', $endDate);

                        $helper = new MessagesHelper($this->request['token']);
                        $order = $helper->getOrder();

                        $order->dates->update([
                            'customer_shipment_date_from' => $startDate,
                            'customer_shipment_date_to' => $endDate,
                            'customer_delivery_date_from' => $startDate,
                            'customer_delivery_date_to' => $endDate,
                        ]);
                    }
                }

                    if (isset($response->AddCompany)) {
                        $company = Firm::where('symbol', $response->AddCompany)->first();
                        $helper = new MessagesHelper($this->request['token']);
                        $order = $helper->getOrder();

                        foreach ($company->employees as $employee) {
                            $chatHelper = new MessagesHelper($order->chat->token);

                            $chatHelper->chatId = $order->chat->id;
                            $chatHelper->currentUserType = 'e';

                            $userId = MessageService::createNewCustomerOrEmployee($order->chat, new Request(['type' => 'Employee']), $employee);
                            $chatHelper->currentUserId = $userId;
                        }
                    }

                    if (isset($response->NoticeForUser)) {
                        $dto = CreateMessageDTO::fromRequest($this->request, $this->request['token']);
                        $dto->message = $response->NoticeForUser;

                        $helper = new MessagesHelper($this->request['token']);
                        $order = $helper->getOrder();

                        $message = app(MessagesHelper::class)->sendMessage(
                            $order->chat,
                            $dto->message,
                        );
                    }

            } catch (\Exception $exception) {
                dd($exception);
            }
        }

        curl_close($ch);
    }
}






$apiUrl = "https://api.anthropic.com/v1/messages";
$apiKey = "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";
$anthropicVersion = "2023-06-01";

$prompt = [[
    "role" => "user",
    "content" => `
"[{"id":86268,"id_from_front_db":null,"customer_id":59779,"status_id":3,"firm_source_id":null,"last_status_update_date":null,"total_price":"0.00","weight":"0.00","shipment_price_for_client":"0.00","shipment_price_for_us":null,"customer_notices":null,"cash_on_delivery_amount":null,"allegro_transaction_id":null,"employee_id":12,"created_at":"2024-07-14T10:15:53.000000Z","updated_at":"2024-07-14T10:28:33.000000Z","warehouse_id":null,"additional_service_cost":"50.00","invoice_warehouse_file":null,"document_number":null,"consultant_earning":null,"warehouse_cost":null,"printed":"","correction_description":null,"correction_amount":null,"packing_warehouse_cost":null,"rating":null,"rating_message":null,"shipping_abroad":0,"proposed_payment":null,"additional_cash_on_delivery_cost":null,"shipment_date":null,"shipment_start_days_variation":null,"consultant_notices":null,"remainder_date":null,"invoice_number":null,"additional_info":null,"print_order":0,"consultant_notice":null,"consultant_value":null,"warehouse_notice":null,"warehouse_value":null,"production_date":null,"master_order_id":null,"spedition_comment":"","token":"4gSTWz5wlw6vnE1XYZpcbj1pqHpDSgDt","payment_deadline":null,"sello_id":null,"initial_sending_date_client":null,"initial_sending_date_consultant":null,"initial_sending_date_magazine":null,"confirmed_sending_date_consultant":null,"initial_pickup_date_client":null,"confirmed_pickup_date_client":null,"confirmed_pickup_date_consultant":null,"initial_delivery_date_consultant":null,"confirmed_delivery_date":null,"proforma_filename":"aozU0QUPfeZU5Kviy2fJtwtkdBEkkyaOoZ6aGCG5.pdf","financial_comment":"","return_payment_id":"","to_refund":null,"refunded":null,"refund_id":null,"allegro_form_id":null,"allegro_deposit_value":null,"allegro_operation_date":null,"allegro_additional_service":null,"payment_channel":null,"labels_log":"\nDodano etykiet\u0119id:  2024-07-14 12:15:53 przez u\u017ctkownika: \nDodano etykiet\u0119id: 39 2024-07-14 12:16:07 przez u\u017ctkownika: \nDodano etykiet\u0119id: 39 2024-07-14 12:16:14 przez u\u017ctkownika: \nDodano etykiet\u0119id: 39 2024-07-14 12:16:22 przez u\u017ctkownika: \nDodano etykiet\u0119id: 39 2024-07-14 12:16:28 przez u\u017ctkownika: \nDodano etykiet\u0119id: 95 2024-07-14 12:16:28 przez u\u017ctkownika: \nDodano etykiet\u0119id: 39 2024-07-14 12:16:43 przez u\u017ctkownika: \nDodano etykiet\u0119id: 271 2024-07-14 12:16:43 przez u\u017ctkownika: \nDodano etykiet\u0119id: 224 2024-07-14 12:28:33 przez u\u017ctkownika: 005","confirmed_sending_date_warehouse":null,"confirmed_pickup_date_warehouse":null,"initial_delivery_date_warehouse":null,"data_verified_by_allegro_api":0,"allegro_payment_id":null,"preferred_invoice_date":null,"need_support":0,"reminder_date":null,"proposed_cash_on_delivery":"20.00","invoice_bilans":null,"is_buying_admin_side":0,"preliminary_buying_document_number":null,"buying_document_number":null,"packages_values":"null","is_hidden":0,"send_auto_messages":1,"date_accepted":null,"auction_order_placed":0,"start_of_spedition_period_sent":0,"near_end_of_spedition_period_sent":0,"customer_name":"jarbud@poczta.fm","last_confirmation":"0000-00-00 00:00:00","special_data_filled":0,"driver_phone":"","end_of_spedition_period_sent":0,"customer_acceptation_date":"0000-00-00 00:00:00","labels":[{"id":271,"label_group_id":4,"name":"271 Zapytanie stworzone za pomoc\u0105 formularza przetargowego","order":231312312,"color":"#87D11B","font_color":"#FFFFFF","icon_name":"fas fa-exclamation-triangle","manual_label_selection_to_add_after_removal":0,"status":"ACTIVE","created_at":"2024-06-26T13:32:51.000000Z","updated_at":"2024-07-13T06:06:34.000000Z","message":null,"timed":0,"pivot":{"order_id":86268,"label_id":271,"added_type":null,"created_at":"2024-07-14T10:16:43.000000Z"},"label_group":{"id":4,"name":"info dodatkowe","created_at":"2019-01-24T20:30:19.000000Z","updated_at":"2020-11-10T23:04:53.000000Z","order":10}},{"id":224,"label_group_id":4,"name":"224 przetarg zatwierdzony przez nas na styropian i uruchomiony juz do fabryk , etykieta ta w odpowiednim momecie zostanie zamienona na kolor wymuszajacy jakas czynnosc przez konsultanta","order":100000,"color":"#4C750F","font_color":"#FFFFFF","icon_name":"fas fa-industry","manual_label_selection_to_add_after_removal":0,"status":"ACTIVE","created_at":"2023-04-08T09:52:25.000000Z","updated_at":"2024-07-13T05:58:24.000000Z","message":null,"timed":0,"pivot":{"order_id":86268,"label_id":224,"added_type":null,"created_at":"2024-07-14T10:28:41.000000Z"},"label_group":{"id":4,"name":"info dodatkowe","created_at":"2019-01-24T20:30:19.000000Z","updated_at":"2020-11-10T23:04:53.000000Z","order":10}}],"invoice_values":[],"payments":[],"items":[{"id":256120,"order_id":86268,"product_id":8089,"price":"0.00","quantity":80,"created_at":"2024-07-14T10:16:15.000000Z","updated_at":"2024-07-14T10:16:15.000000Z","net_purchase_price_commercial_unit":"46.50","net_purchase_price_basic_unit":"155.0000","net_purchase_price_calculated_unit":"31.0000","net_purchase_price_aggregate_unit":"0.0000","net_purchase_price_the_largest_unit":"0.0000","net_selling_price_commercial_unit":"46.50","net_selling_price_basic_unit":"155.0000","net_selling_price_calculated_unit":"31.0000","net_selling_price_aggregate_unit":"46.5000","net_selling_price_the_largest_unit":"46.5000","net_purchase_price_commercial_unit_after_discounts":"46.50","net_purchase_price_basic_unit_after_discounts":"155.00","net_purchase_price_calculated_unit_after_discounts":"31.00","net_purchase_price_aggregate_unit_after_discounts":"46.50","net_purchase_price_the_largest_unit_after_discounts":"46.50","gross_selling_price_commercial_unit":"57.20","gross_selling_price_basic_unit":"190.65","gross_selling_price_calculated_unit":"38.13","gross_selling_price_aggregate_unit":"57.20","gross_selling_price_the_largest_unit":"57.20","product_stock_packet_id":null}],"allegro_general_expenses":[],"other_packages":[{"id":41621,"order_id":86268,"price":null,"type":"not_calculable","description":null}],"customer":{"id":59779,"id_from_old_db":null,"login":"jarbud@poczta.fm","nick_allegro":null,"status":"ACTIVE","is_staff":0,"created_at":"2024-07-14T10:15:53.000000Z","updated_at":"2024-07-14T10:15:53.000000Z","login_token":null,"login_token_expires_at":null,"id_of_parrent_referral":null,"balance_of_addictional_discount_account":0,"addresses":[{"id":93427,"customer_id":59779,"type":"STANDARD_ADDRESS","firstname":null,"lastname":null,"firmname":null,"nip":null,"phone":"698424551","address":null,"flat_number":null,"city":null,"postal_code":null,"email":null,"created_at":"2024-07-14T10:16:07.000000Z","updated_at":"2024-07-14T10:16:07.000000Z","country_id":null},{"id":93428,"customer_id":59779,"type":"DELIVERY_ADDRESS","firstname":null,"lastname":null,"firmname":null,"nip":null,"phone":"698424551","address":null,"flat_number":null,"city":null,"postal_code":null,"email":null,"created_at":"2024-07-14T10:16:07.000000Z","updated_at":"2024-07-14T10:16:07.000000Z","country_id":null}]},"files":[],"packages":[],"warehouse":null,"chat":{"id":11747,"created_at":"2024-07-14T10:16:00.000000Z","updated_at":"2024-07-14T10:22:55.000000Z","product_id":null,"order_id":86268,"need_intervention":0,"user_id":24,"complaint_form":"","questions_tree":"","is_active":0,"information_about_chat_inactiveness_sent":0,"messages":[],"auctions":[{"id":308,"end_of_auction":"2024-07-17 15:00:00","date_of_delivery":"0000-00-00 00:00:00","price":50,"quality":50,"chat_id":11747,"confirmed":1,"created_at":"2024-07-14T10:28:13.000000Z","updated_at":"2024-07-14T10:28:25.000000Z","notes":"","date_of_delivery_from":"0000-00-00","date_of_delivery_to":"0000-00-00","end_info_sent":0}]},"task":null,"addresses":[{"id":116232,"order_id":86268,"type":"DELIVERY_ADDRESS","firstname":"","lastname":"","firmname":null,"nip":"","phone_code":null,"phone":"698424551","address":"","flat_number":null,"city":null,"postal_code":"83-305","email":null,"created_at":"2024-07-14T10:16:07.000000Z","updated_at":"2024-07-14T10:16:28.000000Z","country_id":null,"isAbroad":null},{"id":116233,"order_id":86268,"type":"INVOICE_ADDRESS","firstname":"","lastname":"","firmname":null,"nip":"","phone_code":null,"phone":"698424551","address":"","flat_number":null,"city":null,"postal_code":"83-305","email":null,"created_at":"2024-07-14T10:16:07.000000Z","updated_at":"2024-07-14T10:16:28.000000Z","country_id":1,"isAbroad":null}],"invoices":[],"dates":{"id":48451,"order_id":86268,"customer_shipment_date_from":null,"customer_shipment_date_to":null,"customer_delivery_date_from":null,"customer_delivery_date_to":null,"consultant_shipment_date_from":null,"consultant_shipment_date_to":null,"consultant_delivery_date_from":null,"consultant_delivery_date_to":null,"warehouse_shipment_date_from":null,"warehouse_shipment_date_to":null,"warehouse_delivery_date_from":null,"warehouse_delivery_date_to":null,"customer_acceptance":0,"consultant_acceptance":0,"warehouse_acceptance":0,"message":"Prosz\u0119 o uzupe\u0142nienie dat","created_at":"2024-07-14T10:28:05.000000Z","updated_at":"2024-07-14T10:28:05.000000Z"}}

    i pasted my order from db convert it to xml format for invoice program so it will look like this

<?xml version="1.0"?>
<PreDokument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
 <Klient>
 <Typ>Firma</Typ>
 <Symbol>ARTUR</Symbol>
 <Nazwa>Kiosk ARTUR</Nazwa>
 <NazwaPelna>Kiosk ARTUR</NazwaPelna>
 <OsobaImie />
 <OsobaNazwisko />
 <NIP>836-84-63-635</NIP>
 <NIPUE />
 <Email>info@artur.insert.pl</Email>
 <Telefon>333-53-64</Telefon>
 <RodzajNaDok>Nabywca</RodzajNaDok>
 <NrRachunku>10202502-56648889798787878556</NrRachunku>
 <ChceFV>true</ChceFV>
 <AdresGlowny>
 <Nazwa>Kiosk ARTUR</Nazwa>
 <Ulica>Legnicka 57/2</Ulica>
 <Miasto>Lublin</Miasto>
 <Kod>96-534</Kod>
 <Panstwo>Polska</Panstwo>
 </AdresGlowny>
 </Klient>
 <UslugaTransportu />
 <UslugaTransportuCenaNetto>0</UslugaTransportuCenaNetto>
 <UslugaTransportuCenaBrutto>0</UslugaTransportuCenaBrutto>
 <Numer>2</Numer>
 <NumerPelny>ZK 2/SF/MAG/2017</NumerPelny>
 <NumerZewnetrzny />
 <NumerZewnetrzny2 />
 <DataUtworzenia>2017-02-15T00:00:00</DataUtworzenia>
 <DataDostawy xsi:nil="true" />
 <TerminPlatnosci>2017-02-15T00:00:00</TerminPlatnosci>
 <Produkty>
 <PrePozycja>
 <Towar>
 <Rodzaj>Towar</Rodzaj>
 <Symbol>PESO20</Symbol>
 <SymbolDostawcy />
 <NazwaDostawcy />
 <SymbolProducenta />
 <NazwaProducenta />
 <Nazwa>So perfumy 20ml</Nazwa>
 <CenaKartotekowaNetto>150</CenaKartotekowaNetto>
 <CenaNetto>300</CenaNetto>
 <JM>szt.</JM>
 <KodKreskowy>5902812179392</KodKreskowy>
 <Vat>8</Vat>
 <PKWiU />
 <Opis>Perfumy o mocnym i długotrwałym zapachu</Opis>
 <OpisPelny />
 <Uwagi />
 <AdresWWW />
 <SymboleSkladnikow />
 <IloscSkladnikow />
 <Zdjecia />
 <Wysokosc>0</Wysokosc>
 <Dlugosc>0</Dlugosc>
 <Szerokosc>0</Szerokosc>
 <Waga>0</Waga>
 <PoleWlasne />
 </Towar>
 <RabatProcent>0.0000</RabatProcent>
 <CenaNettoPrzedRabatem>270</CenaNettoPrzedRabatem>
 <CenaNettoPoRabacie>270</CenaNettoPoRabacie>
 <CenaBruttoPrzedRabatem>291.6</CenaBruttoPrzedRabatem>
 <CenaBruttoPoRabacie>291.6</CenaBruttoPoRabacie>
 <Ilosc>3</Ilosc>
 <Vat>8</Vat>
 <OpisPozycji />
 <KodDostawy />
 <WartoscCalejPozycjiNettoZRabatem>810</WartoscCalejPozycjiNettoZRabatem>
 <WartoscCalejPozycjiBruttoZRabatem>874.8</WartoscCalejPozycjiBruttoZRabatem>
 <WartoscCalejPozycjiNetto>810</WartoscCalejPozycjiNetto>
 <WartoscCalejPozycjiBrutto>874.8</WartoscCalejPozycjiBrutto>
 </PrePozycja>
 </Produkty>
 <Uwagi />
 <RodzajPlatnosci>Gotówka</RodzajPlatnosci>
 <Waluta>PLN</Waluta>
 <WartoscPoRabacieNetto>810</WartoscPoRabacieNetto>
 <WartoscPoRabacieBrutto>874.8</WartoscPoRabacieBrutto>
 <WartoscNetto>0</WartoscNetto>
 <WartoscBrutto>0</WartoscBrutto>
 <WartoscWplacona>0.0</WartoscWplacona>
 <TypDokumentu>ZK</TypDokumentu>
 <StatusDokumentuWERP />
 <Kategoria>Sprzedaż</Kategoria>
 <Magazyn>MAG</Magazyn>
 <MagazynDo />
</PreDokument>
This format

Invoice is buying and use "szt" not "szt."
    `
]];

$data = [
    "model" => "claude-3-5-sonnet-20240620",
    "max_tokens" => 1024,
    "messages" => $prompt,
];

$payload = json_encode($data);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-api-key: $apiKey",
    "anthropic-version: $anthropicVersion",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
