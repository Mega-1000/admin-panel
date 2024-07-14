<?php

namespace App\Jobs;

use App\Entities\Firm;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Facades\Mailer;
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
use App\Mail\XmlForNexoMail;
use App\Repositories\OrderRepository;
use App\Services\Label\AddLabelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use ZipArchive;

/**
 * Generate xml for nexo import.
 */
class GenerateXmlForNexoBuyingJob implements ShouldQueue
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
    public function handle(): void
    {
        $orders = $this->orderRepository->whereHas('labels', function ($query) {
            $query->where('label_id', 215);
        })->get();
        $fileNames = [];

        $files = Storage::disk('xmlForNexoDisk')->files();
        foreach ($files as $file) {
            Storage::disk('xmlForNexoDisk')->delete($file);
        }

        foreach ($orders as $order) {
            try {
                $address = $order->warehouse->firm->address;
                $preAddress = new PreAdres();
                $preAddress
                    ->setUlica($address->address . ' ' . $address->flat_number)
                    ->setMiasto($address->city)
                    ->setKod($address->postal_code)
                    ->setPanstwo('Polska');

                $postalCodeWithOnlyNumbers = preg_replace("/[^0-9]/", "", $address->postal_code);

                $preKlient = new PreKlient();
                $preKlient
                    ->setTyp((empty($address->nip)) ? EPreKlientTyp::OSOBA : EPreKlientTyp::FIRMA)
                    ->setSymbol((empty($address->nip)) ? strtoupper(PdfCharactersHelper::changePolishCharactersToNonAccented($address->lastname . $address->firstname . $postalCodeWithOnlyNumbers)) : $address->nip)
                    ->setNazwa((empty($address->firmname)) ? $address->firstname . ' ' . $address->lastname : $address->firmname)
                    ->setNazwaPelna((empty($address->firmname)) ? $address->firstname . ' ' . $address->lastname : $address->firmname)
                    ->setNIP($address->nip)
                    ->setEmail($address->firm->email)
                    ->setTelefon($address->phone)
                    ->setRodzajNaDok(EPreKlientRodzajNaDok::NABYWCA)
                    ->setAdresGlowny($preAddress)
                    ->setChceFV('true');

                $preDokument = new PreDokument();
                $preDokument
                    ->setKlient($preKlient)
                    ->setUwagi($order->id . ((empty($order->allegro_payment_id)) ? '' : ' ' . $order->allegro_payment_id))
                    ->setRodzajPlatnosci('Przelew')
                    ->setWaluta('PLN')
                    ->setTypDokumentu('FZ')
                    ->setKategoria('Zakup')
                    ->setUslugaTransportuCenaBrutto(0)
                    ->setUslugaTransportuCenaNetto(0)
                    ->setDataDostawy($this->getOrderInvoiceDate($order))
                    ->setDataUtworzenia($this->getOrderInvoiceDate($order))
                    ->setTerminPlatnosci($this->getOrderInvoiceDate($order))
                    ->setWartoscPoRabacieNetto(0)
                    ->setWartoscPoRabacieBrutto(0)
                    ->setWartoscNetto(0)
                    ->setWartoscBrutto(0)
                    ->setWartoscWplacona(0)
                    ->setNumer(0)
                    ->setNumerPelny(0)
                    ->setMagazyn((isset($order->warehouse) && $order->warehouse->id === 16) ? 'STA' : 'MAG');

                foreach ($order->items as $item) {
                    $towar = new PreTowar();
                    $towar
                        ->setRodzaj(ERodzajTowaru::TOWAR)
                        ->setSymbol($item->product->getSimpleSymbol())
                        ->setCenaKartotekowaNetto(0)
                        ->setCenaNetto($item->net_purchase_price_commercial_unit ?? 0)
                        ->setJM($item->product->packing->unit_commercial)
                        ->setVat($item->product->price->vat ?? 23)
                        ->setWysokosc(0)
                        ->setSzerokosc(0)
                        ->setDlugosc(0)
                        ->setWaga($item->product->weight_trade_unit ?? 0);
                    $prePozycja = new PrePozycja();
                    $prePozycja
                        ->setTowar($towar)
                        ->setIlosc($item->quantity)
                        ->setRabatProcent(0)
                        ->setVat(23)
                        ->setCenaNettoPrzedRabatem(0)
                        ->setCenaNettoPoRabacie(0)
                        ->setCenaBruttoPrzedRabatem(0)
                        ->setCenaBruttoPoRabacie($item->net_purchase_price_commercial_unit * 1.23)
                        ->setWartoscCalejPozycjiNetto(0)
                        ->setWartoscCalejPozycjiBrutto(0)
                        ->setWartoscCalejPozycjiNettoZRabatem(0)
                        ->setWartoscCalejPozycjiBruttoZRabatem($item->net_purchase_price_commercial_unit * 1.23 * $item->quantity);

                    $preDokument
                        ->setProdukty(array_merge($preDokument->getProdukty(), [$prePozycja]));
                }

                $preDokument->setProdukty(array_filter(array_merge($preDokument->getProdukty(), [
                ])));

                $xml = self::generateValidXmlFromObj($preDokument);
                $xmlFileName = Carbon::create($order->preferred_invoice_date)->format('Y-m-d') . '-' . $order->id . '_FS_' . Carbon::now()->format('d-m-Y') . '.xml';

                Storage::disk('xmlForNexoDisk')->put($xmlFileName, mb_convert_encoding($xml, "UTF-8", "auto"));
                $preventionArray = [];

                $fileNames[] = $xmlFileName;

                AddLabelService::addLabels($order, [Label::XML_INVOICE_GENERATED], $preventionArray, [], Auth::user()?->id);
            } catch (Throwable $ex) {
                dd($ex->getMessage());
                Log::error($ex->getMessage(), [
                    'productId' => (isset($item)) ? $item->product->id : null,
                    'orderItemId' => (isset($item)) ? $item->id : null,
                    'orderId' => $order->id,
                ]);
                continue;
            }
        }

        dd($fileNames);
        if (count($fileNames) > 0) {
            $zipName = 'XMLFS_' . Carbon::now()->format('d-m-Y_H-i-s') . '.zip';
            $zip = new ZipArchive();
            $zip->open(storage_path('app/public/nexo-buying' . $zipName), ZipArchive::CREATE);
            foreach ($fileNames as $fileName) {
                $zip->addFile(storage_path('app/public/nexo-buying' . $fileName), $fileName);
            }
            $zip->close();

            foreach ($fileNames as $fileName) {
                Storage::disk('xmlForNexoDisk')->delete($fileName);
            }

            Mailer::create()
                ->to('ksiegowosc@ephpolska.pl')
                ->send(new XmlForNexoMail($zipName));
        }
    }

    /**
     *
     * @param Order $order
     *
     * @return string
     */
    private function getOrderInvoiceDate(Order $order): string
    {
        $preferredInvoiceDate = new Carbon($order->preferred_invoice_date);

        return $preferredInvoiceDate->toDateTimeLocalString();
    }

    /**
     * @param $order
     *
     * @return PrePozycja
     */
    private function getDKOPosition($order): PrePozycja
    {
        $towar = new PreTowar();
        $towar
            ->setRodzaj(ERodzajTowaru::USLUGA)
            ->setSymbol('DKO')
            ->setCenaKartotekowaNetto(0)
            ->setCenaNetto(0)
            ->setJM('szt')
            ->setVat(23)
            ->setWysokosc(0)
            ->setSzerokosc(0)
            ->setDlugosc(0)
            ->setWaga(0);
        $prePozycja = new PrePozycja();
        $prePozycja
            ->setTowar($towar)
            ->setIlosc(1)
            ->setRabatProcent(0)
            ->setVat(23)
            ->setCenaNettoPrzedRabatem(0)
            ->setCenaNettoPoRabacie(0)
            ->setCenaBruttoPrzedRabatem(0)
            ->setCenaBruttoPoRabacie(0)
            ->setWartoscCalejPozycjiNetto(0)
            ->setWartoscCalejPozycjiBrutto(0)
            ->setWartoscCalejPozycjiNettoZRabatem(0)
            ->setWartoscCalejPozycjiBruttoZRabatem($order->additional_service_cost ?? 0);
        return $prePozycja;
    }

    /**
     * @param $order
     *
     * @return PrePozycja
     */
    private function getDKPPosition($order): PrePozycja
    {
        $towar = new PreTowar();
        $towar
            ->setRodzaj(ERodzajTowaru::USLUGA)
            ->setSymbol('DKP')
            ->setCenaKartotekowaNetto(0)
            ->setCenaNetto(0)
            ->setJM('szt')
            ->setVat(23)
            ->setWysokosc(0)
            ->setSzerokosc(0)
            ->setDlugosc(0)
            ->setWaga(0);
        $prePozycja = new PrePozycja();
        $prePozycja
            ->setTowar($towar)
            ->setIlosc(1)
            ->setRabatProcent(0)
            ->setVat(23)
            ->setCenaNettoPrzedRabatem(0)
            ->setCenaNettoPoRabacie(0)
            ->setCenaBruttoPrzedRabatem(0)
            ->setCenaBruttoPoRabacie(0)
            ->setWartoscCalejPozycjiNetto(0)
            ->setWartoscCalejPozycjiBrutto(0)
            ->setWartoscCalejPozycjiNettoZRabatem(0)
            ->setWartoscCalejPozycjiBruttoZRabatem($order->additional_cash_on_delivery_cost ?? 0);
        return $prePozycja;
    }

    /**
     * @param $order
     *
     * @return PrePozycja
     */
    private function getUTPosition($order): PrePozycja
    {
        $towar = new PreTowar();
        $towar
            ->setRodzaj(ERodzajTowaru::USLUGA)
            ->setSymbol('UT')
            ->setCenaKartotekowaNetto(0)
            ->setCenaNetto(0)
            ->setJM('szt')
            ->setVat(23)
            ->setWysokosc(0)
            ->setSzerokosc(0)
            ->setDlugosc(0)
            ->setWaga(0);
        $prePozycja = new PrePozycja();
        $prePozycja
            ->setTowar($towar)
            ->setIlosc(1)
            ->setRabatProcent(0)
            ->setVat(23)
            ->setCenaNettoPrzedRabatem(0)
            ->setCenaNettoPoRabacie(0)
            ->setCenaBruttoPrzedRabatem(0)
            ->setCenaBruttoPoRabacie(0)
            ->setWartoscCalejPozycjiNetto(0)
            ->setWartoscCalejPozycjiBrutto(0)
            ->setWartoscCalejPozycjiNettoZRabatem(0)
            ->setWartoscCalejPozycjiBruttoZRabatem($order->shipment_price_for_client ?? 0);
        return $prePozycja;
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
    private static function generateXmlFromArray($array, $node_name): string
    {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }
                if (!empty($value) || $value === 0 || $value === '0' || $value === 0.0) {
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

    /**
     *
     * @param Order $order
     *
     * @return string
     */
    private function getOrderDate(Order $order): string
    {
        $now = Carbon::now();
        $settledAdvanceDeclared = $order->paymentsWithTrash->filter(function (OrderPayment $payment) {
            return $payment->deleted_at !== null;
        })->last();

        if (!empty($order->allegro_operation_date)) {
            $operationDate = Carbon::parse($order->allegro_operation_date);
            if ($operationDate->lessThan($now->firstOfMonth())) {
                return $operationDate->lastOfMonth()->toDateTimeLocalString();
            } else {
                return Carbon::now()->toDateTimeLocalString();
            }
        } elseif (isset($settledAdvanceDeclared->deleted_at)) {
            $operationDate = Carbon::parse($settledAdvanceDeclared->deleted_at);
            return $operationDate->lastOfMonth()->toDateTimeLocalString();
        } else {
            return Carbon::now()->toDateTimeLocalString();
        }
    }
}
