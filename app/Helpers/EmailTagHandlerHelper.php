<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 27.12.2018
 */

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\Tag;

class EmailTagHandlerHelper
{
    /** @var Order */
    protected $order;

    public function setOrder($order)
    {
        $this->order = $order;
    }

    //[KONSULTANT/MAGAZYNIER]
    public function consultantOrStorekeeper()
    {
        if (!empty($employee = $this->order->employee)) {
            return 'Identyfikator: ' . $employee->name . '<br/>' . 'Imie i nazwisko: ' . $employee->firstname . ' ' .
                $employee->lastname . '<br/>' . 'Telefon:' . $employee->phone . '<br/>' . 'Adres email: ' . $employee->email;
        }
    }

    //[DANE-KUPUJACEGO]
    public function buyerData()
    {
        $address = $this->order->addresses->where('type', 'DELIVERY_ADDRESS')->first();

        return ($address === null ? ' ' : $address->firstname)  . ' ' . ($address === null ? ' ' : $address->lastname) . '<br/> Email: ' . ($address === null ? ' ' : $address->email);
    }

    //[DANE-DO-DOSTAWY]
    public function shipmentData()
    {
        $address = $this->order->addresses->where('type', 'DELIVERY_ADDRESS')->first();

        return ($address === null ? ' ' : $address->firstname) . ' ' . ($address === null ?  ' ' : $address->lastname) . '<br/> Email: ' . ($address === null ? ' ' : $address->email) . '<br/>' .($address === null ? ' ' : $address->address) . ' ' . ($address === null ? ' ' : $address->flat_number) . '<br/>' . ($address === null ? ' ' : $address->postal_code) . '<br/>' . ($address === null ? ' ' : $address->city) . '<br/>';
    }

    //[DANE-DO-FAKTURY]
    public function invoiceData()
    {
        $address = $this->order->addresses->where('type', 'INVOICE_ADDRESS')->first();

        return ($address === null ? ' ' : $address->firstname) . ' ' . ($address === null ?  ' ' : $address->lastname) . '<br/> Email: ' . ($address === null ? ' ' : $address->email) . '<br/>' .($address === null ? ' ' : $address->address) . '<br/>' . ($address === null ? ' ' : $address->flat_number) . '<br/>' . ($address === null ? ' ' : $address->postal_code) . '<br/>' . ($address === null ? ' ' : $address->city) . '<br/>' . ($address === null ? ' ' : $address->nip);
    }

    //[WARTOSC-TOWARU-WRAZ-Z-TRANSPORTEM-DLA-NAS-W-CENACH-NETTO]
    public function warePriceWithShipmentForUsInNet()
    {
        $value = 0;
        if($this->order->items){
            foreach($this->order->items as $item) {
                $value += $item->product->net_purchase_price_commercial_unit;
            }
        }


        $value += $this->order->shipment_price;

        return $value;
    }

    //[WARTOSC-CALEGO-ZAMOWIENIA-W-CENACH-ZAKUPU-NETTO]
    public function completeOrderValueInPurchasePriceNet()
    {
        $value = 0;
        if($this->order->items){
            foreach($this->order->items as $item) {
                $value += $item->product->net_purchase_price_commercial_unit;
            }
        }


        return $value;
    }
    //[ZAMOWIENIE-W-CENACH-NETTO-ZAKUPU]
    public function orderPriceInNetPurchase()
    {
        return "zamowienie w netto";
    }

    //[ZAMOWIENIE-W-CENACH-NETTO-ZAKUPU-SPECJALNYCH]
    public function orderInNetSpecialPurchasePrice()
    {
        $value = 0;

        if($this->order->items) {
            foreach($this->order->items as $item) {
                $value += $item->product->net_special_price_commercial_unit;
            }
        }


        return $value;
    }

    //[NUMER-OFERTY]
    public function offerNumber()
    {
        return $this->order->id;
    }

    //[ZAMOWIENIE]
    public function order()
    {
        $content = "<table>";
        foreach ($this->order->items as $item) {
            $content .= "<tr>";
                $content .= "<td>";
                    $content .= "<img alt=\"{$item->product->name}\" style=\"width:80px\" src=\"" .$item->product->getImageUrl() . "\">";
                $content .= "</td>";
                $content .= "<td>";
                    $content .= "<b>Nazwa:</b> {$item->product->name}<br>";
                    $content .= "<b>Symbol:</b> {$item->product->symbol}<br>";
                    $content .= "<b>Ilość:</b> {$item->quantity}, <b>Cena jednostkowa netto:</b> {$item->net_selling_price_commercial_unit} / {$item->product->packing->calculation_unit}<br>";
                    $content .= "<b>Wartość:</b> " . $item->quantity * $item->net_selling_price_commercial_unit . " Netto";
                $content .= "</td>";
            $content .= "</tr>";
        }
        $content .= "</table>";

        return $content;
    }

    //[WARTOSC-ZAMOWIENIA]
    public function orderValue()
    {
        $itemsValue = 0;
        $items = $this->order->items;
        foreach($items as $item) {
            $itemsValue += $item->net_selling_price_commercial_unit * $item->quantity;
        }

        return ($itemsValue*1.23);
    }

    //[ADRES-DOSTAWY]
    public function shipmentAddress()
    {
        $address = $this->order->addresses->where('type', 'DELIVERY_ADDRESS')->first();

        return ($address === null ? ' ' : $address->firstname) . ' ' . ($address === null ?  ' ' : $address->lastname) . '<br/> Email: ' . ($address === null ? ' ' : $address->email) . '<br/>' .($address === null ? ' ' : $address->address) . '<br/>' . ($address === null ? ' ' : $address->flat_number) . '<br/>' . ($address === null ? ' ' : $address->postal_code) . '<br/>' . ($address === null ? ' ' : $address->city) . '<br/>';
    }

    //[KOSZT-TRANSPORTU]
    public function shipmentCharge()
    {
        return $this->order->shipment_price_for_client ? $this->order->shipment_price_for_client : 0;
    }

    //[NASZ-KOSZT-TRANSPORTU]
    public function ourShipmentCharge()
    {
        $shipmentCost = $this->order->packages->first();
        return $shipmentCost === null ? 0 : $shipmentCost->cost_for_company;
    }

    //[DODATKOWY-KOSZT-POBRANIA]
    public function additionalObtainmentCharge()
    {
        return "dodatkowy koszt pobrania";
    }

    //[DODATKOWY-KOSZT-OBSLUGI]
    public function additionalServiceCharge()
    {
        return $this->order->additional_service_cost ? $this->order->additional_service_cost : 0;
    }

    //[ZALICZKA-PROPONOWANA]
    public function instalment()
    {
        return $this->order->proposed_payment;
    }

    //[ZALICZKA-ZAKSIEGOWANA]
    public function instalmentBooked()
    {
        return "zaliczka zaksiegowana";
    }

    //[WARTOSC-ZAMOWIENIA-Z-KOSZTAMI-TRANSPORTU]
    public function orderValueWithShipmentCharge()
    {
        $itemsValue = 0;
        $items = $this->order->items;
        foreach($items as $item) {
            $itemsValue += $item->net_selling_price_commercial_unit * $item->quantity;
        }

        if($this->order->shipment_price_for_client == null) {
            $shipmentPrice = 0;
        } else {
            $shipmentPrice = $this->order->shipment_price_for_client;
        }

        return ($itemsValue*1.23) +  $shipmentPrice;
    }

    //[WARTOSC-ZAMOWIENIA-ZE-WSZYSTKIMI-KOSZTAMI]
    public function orderValueWithAllCosts()
    {
        $itemsValue = 0;
        $items = $this->order->items;
        foreach($items as $item) {
            $itemsValue += $item->net_selling_price_commercial_unit * $item->quantity;
        }

        if($this->order->shipment_price_for_client == null) {
            $shipmentPrice = 0;
        } else {
            $shipmentPrice = $this->order->shipment_price_for_client;
        }

        if($this->order->additional_service_cost == null) {
            $additionalServiceCost = 0;
        } else {
            $additionalServiceCost = $this->order->additional_service_cost;
        }


        return ($itemsValue*1.23) + $additionalServiceCost + $shipmentPrice;
    }

    //[DO-POBRANIA-PRZY-ROZLADUNKU]
    public function toChargeWhileUnloading()
    {
        return $this->order->cash_on_delivery_amount ? $this->order->cash_on_delivery_amount : 0;
    }

    //[DATA-NADANIA-PRZESYLKI]
    public function cargoSentDate()
    {
        $package = $this->order->packages->first();
        return $package === null ? null :  $package->shipment_date;
    }

    //[UWAGI-OSOBY-ZAMAWIAJACEJ]
    public function commentsOfPurchaser()
    {
        return $this->order->customer_notices ? $this->order->customer_notices : ' ';
    }

    //[UWAGI-DO-SPEDYCJI]
    public function commentsToShipping($glue = "<br>")
    {
        $messages = $this->order->messages()->where('type', 'LIKE', 'SHIPPING')->orderBy('created_at')->get();
        $content = "";

        if (count($messages) > 0) {
            $temp = [];
            foreach ($messages as $item) {
                $temp[] = $item->message;
            }
            $content = join($glue, $temp);
        }

        return $content;
    }

    //[UWAGI-DO-MAGAZYNU]
    public function commentsToStorehouse($glue = "<br>")
    {
        $messages = $this->order->messages()->where('type', 'LIKE', 'WAREHOUSE')->orderBy('created_at')->get();
        $content = "";

        if (count($messages) > 0) {
            $temp = [];
            foreach ($messages as $item) {
                $temp[] = $item->message;
            }
            $content = join($glue, $temp);
        }

        return $content;
    }

    //[TELEFON-KUPUJACEGO]
    public function purchaserPhoneNumber()
    {
        $phone = $this->order->addresses->where('type', 'DELIVERY_ADDRESS')->first();
        return $phone === null ? ' ' : $phone->phone;
    }

    //[EMAIL-KUPUJACEGO]
    public function purchaserEmail()
    {
        $email = $this->order->addresses->where('type', 'DELIVERY_ADDRESS')->first();
        return $email === null ? ' ' : $email->email;

    }

    //[DANE-MAGAZYNU]
    public function warehouseData()
    {
        $warehouse = $this->order->warehouse;

        if (empty($warehouse)) {
            return "";
        }

        $address = $warehouse->address;

        if(empty($address)) {
            return "";
        }

        return "Numer magazynu: {$address->warehouse_number}<br>Adres: {$address->address}<br>{$address->postal_code} {$address->city}";
    }

	//[LINK-DO-FORMULARZA-ADRESU]
	public function addressFormLink() {
		return rtrim(config('app.front_nuxt_url') . "/zamowienie/mozliwe-do-realizacji/brak-danych/{$this->order->id}");
	}

	//[LINK-DO-FORMULARZA-NIEZGODNOSCI]
	public function declineProformFormLink() {
		return rtrim(config('app.front_nuxt_url') . "/zamowienie/niezgodnosc-w-proformie/{$this->order->id}");
	}

	/**
     * [FAQ-LINK]
     * Insert FAQ link with encoded user credentials
     *
     * @return string $template
     */
	public function faqLink(): string {

        $workingAddress = null;
        foreach($this->order->customer?->addresses as $address) {
            if($address->phone !== null && $address->email !== null) {
                $workingAddress = $address;
                break;
            }
        }

        if($workingAddress === null) return '';

        $base64Email = base64_encode($workingAddress->email);
        $base64Phone = base64_encode($workingAddress->phone);

        // Jeżeli masz pytania zapoznaj się z naszym FAQ:<br>
        $template = config('app.front_url') . "/faq?phone=$base64Email&email=$base64Phone&showFaq=true";

		return $template;
	}

    /**
     * Parse existing tags from message using tags handlers
     *
     * @param  Order  $order
     * @param  string $message
     *
     * @return string $message
     */
	public function parseTags(Order $order, string $message): string {
        
        $this->order = $order;
        $matchedTagsCount = preg_match_all('/\[.*\]/', $message, $matchedTags);
        if( $matchedTagsCount < 1 ) return $message;

        $tags = Tag::whereIn('name', $matchedTags[0])->get();

        foreach ($tags as $tag) {
            if(method_exists($this, $tag->handler)) {
                $tagResult = call_user_func([$this, $tag->handler]);
                $message   = str_replace( $tag->name, $tagResult, $message);
            }
        }
        return $message;
	}
}
