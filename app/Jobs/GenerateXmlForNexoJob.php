<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Helpers\PdfCharactersHelper;
use App\Integrations\Artoit\EPreKlientRodzajNaDok;
use App\Integrations\Artoit\EPreKlientTyp;
use App\Integrations\Artoit\ERodzajTowaru;
use App\Integrations\Artoit\ETypDokumentu_HandloMag;
use App\Integrations\Artoit\PreAdres;
use App\Integrations\Artoit\PreDokument;
use App\Integrations\Artoit\PreKlient;
use App\Integrations\Artoit\PrePozycja;
use App\Integrations\Artoit\PreTowar;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Generate xml for nexo import.
 */
class GenerateXmlForNexoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->orderRepository = app(OrderRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = $this->orderRepository->whereHas('labels', function ($query) {
            $query->where('label_id', Label::INVOICE_TO_ISSUE);
        })->whereHas('labels', function ($query) {
            $query->where('label_id', Label::ORDER_RECEIVED_INVOICE_TODAY);
        })->whereHas('labels', function ($query) {
            $query->where('label_id', Label::ORDER_ITEMS_REDEEMED_LABEL);
        })->whereDoesntHave('labels', function ($query) {
            $query->where('label_id', Label::XML_INVOICE_GENERATED);
        })->limit(5)->get();

        foreach ($orders as $order) {
            try {
                $preDokument = new PreDokument();
                $address = $order->getDeliveryAddress();
                $preAddress = new PreAdres();
                $preAddress
                    ->setUlica($address->address . ' ' . $address->flat_number)
                    ->setMiasto($address->city)
                    ->setKod($address->postal_code)
                    ->setPanstwo('Polska');

                $preKlient = new PreKlient();
                $preKlient
                    ->setTyp((empty($address->nip)) ? EPreKlientTyp::OSOBA : EPreKlientTyp::FIRMA)
                    ->setSymbol((empty($address->nip)) ? strtoupper(PdfCharactersHelper::changePolishCharactersToNonAccented($address->lastname . $address->firstname . $address->flat_number)) : $address->nip)
                    ->setNazwa((empty($address->firmname)) ? $address->firstname . ' ' . $address->lastname : $address->firmname)
                    ->setNazwaPelna((empty($address->firmname)) ? $address->firstname . ' ' . $address->lastname : $address->firmname)
                    ->setOsobaImie($address->firstname)
                    ->setOsobaNazwisko($address->lastname)
                    ->setNIP($address->nip)
                    ->setEmail($address->email)
                    ->setTelefon($address->phone)
                    ->setRodzajNaDok(EPreKlientRodzajNaDok::NABYWCA)
                    ->setAdresGlowny($preAddress)
                    ->setChceFV('true');

                $preDokument
                    ->setKlient($preKlient)
                    ->setUwagi($order->id)
                    ->setRodzajPlatnosci('Przelew')
                    ->setWaluta('PLN')
                    ->setTypDokumentu(ETypDokumentu_HandloMag::FS)
                    ->setKategoria('Sprzedaż')
                    ->setUslugaTransportuCenaBrutto(0)
                    ->setUslugaTransportuCenaNetto(0)
                    ->setDataDostawy(Carbon::now()->toDateTimeLocalString())
                    ->setDataUtworzenia(Carbon::now()->toDateTimeLocalString())
                    ->setTerminPlatnosci(Carbon::now()->toDateTimeLocalString())
                    ->setWartoscPoRabacieNetto(0)
                    ->setWartoscPoRabacieBrutto(0)
                    ->setWartoscNetto(0)
                    ->setWartoscBrutto(0)
                    ->setWartoscWplacona(0)
                    ->setNumer(0)
                    ->setNumerPelny(0)
                    ->setMagazyn(($order->warehouse->id === 16) ? 'STA' : 'MAG');

                foreach ($order->items as $item) {
                    $towar = new PreTowar();
                    $towar
                        ->setRodzaj(ERodzajTowaru::TOWAR)
                        ->setSymbol($item->product->getSimpleSymbol())
                        ->setCenaKartotekowaNetto(0)
                        ->setCenaNetto($item->net_purchase_price_commercial_unit)
                        ->setJM($item->product->packing->calculation_unit)
                        ->setVat($item->product->price->vat ?? 23)
                        ->setWysokosc(0)
                        ->setSzerokosc(0)
                        ->setDlugosc(0)
                        ->setWaga(0)
                        ->setWaga($item->product->weight_trade_unit);
                    $prePozycja = new PrePozycja();
                    $prePozycja
                        ->setTowar($towar)
                        ->setIlosc($item->quantity)
                        ->setRabatProcent(0)
                        ->setVat($item->product->price->vat ?? 23)
                        ->setCenaNettoPrzedRabatem(0)
                        ->setCenaNettoPoRabacie(0)
                        ->setCenaBruttoPrzedRabatem(0)
                        ->setCenaBruttoPoRabacie($item->gross_selling_price_commercial_unit)
                        ->setWartoscCalejPozycjiNetto(0)
                        ->setWartoscCalejPozycjiBrutto(0)
                        ->setWartoscCalejPozycjiNettoZRabatem(0)
                        ->setWartoscCalejPozycjiBruttoZRabatem($item->gross_selling_price_commercial_unit * $item->quantity);

                    $preDokument
                        ->setProdukty(array_merge($preDokument->getProdukty(), [$prePozycja]));
                }

                $xml = self::generateValidXmlFromObj($preDokument);
                Storage::disk('local')->put('public/XMLFS/' . $order->id . '_FS_' . Carbon::now()->format('d-m-Y') . '.xml', mb_convert_encoding($xml, "UTF-8", "auto"));
                dispatch_now(new AddLabelJob($order, [Label::XML_INVOICE_GENERATED]));
            } catch (\Throwable $ex) {dd($ex);
                Log::error($ex->getMessage());
                \Session::flash('flash-message', ['type' => 'error', 'message' => $ex->getMessage()]);
            }
        }
    }

    /**
     * @param $obj
     * @param $node_block
     * @param $node_name
     *
     * @return string
     */
    public static function generateValidXmlFromObj($obj, $node_block = 'PreDokument', $node_name = 'PrePozycja')
    {
        $arr = get_object_vars($obj);
        return self::generateValidXmlFromArray($arr, $node_block, $node_name);
    }

    /**
     * @param $array
     * @param $node_block
     * @param $node_name
     *
     * @return string
     */
    public static function generateValidXmlFromArray($array, $node_block = 'nodes', $node_name = 'node')
    {
        $xml = '<?xml version="1.0"?>';

        $xml .= '<' . $node_block . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">';
        $xml .= self::generateXmlFromArray($array, $node_name);
        $xml .= '</' . $node_block . '>';

        return $xml;
    }

    /**
     * @param $array
     * @param $node_name
     *
     * @return string
     */
    private static function generateXmlFromArray($array, $node_name)
    {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }
                if (!empty($value) || $value === 0) {
                    $xml .= '<' . ucfirst($key) . '>' . self::generateXmlFromArray($value, $node_name) . '</' . ucfirst($key) . '>';
                } else {
                    $xml .= '<' . ucfirst($key) . '/>';
                }
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }
}
