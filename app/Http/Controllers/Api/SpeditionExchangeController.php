<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ExchangeRequest\GenerateLinkRequest;
use App\Http\Requests\Api\ExchangeRequest\NewOfferRequest;
use App\Jobs\SendOfferToSpedition;
use App\Mail\SpeditionExchange\AcceptOfferMail;
use App\Mail\SpeditionExchange\RejectOfferMail;
use App\Repositories\FirmRepository;
use App\Repositories\SpeditionExchangeItemRepository;
use App\Repositories\SpeditionExchangeOfferRepository;
use App\Repositories\SpeditionExchangeRepository;
use Carbon\Carbon;
use Mailer;

class SpeditionExchangeController extends Controller
{
    use ApiResponsesTrait;

    /** @var SpeditionExchangeRepository */
    protected $speditionExchangeRepository;

    /** @var SpeditionExchangeItemRepository */
    protected $speditionExchangeItemRepository;

    /** @var SpeditionExchangeOfferRepository */
    protected $speditionExchangeOffer;

    /** @var FirmRepository */
    protected $firmRepository;

    /**
     * SpeditionExchangeController constructor.
     * @param SpeditionExchangeRepository $speditionExchangeRepository
     * @param SpeditionExchangeItemRepository $speditionExchangeItemRepository
     * @param SpeditionExchangeOfferRepository $speditionExchangeOffer
     * @param FirmRepository $firmRepository
     */
    public function __construct(
        SpeditionExchangeRepository      $speditionExchangeRepository,
        SpeditionExchangeItemRepository  $speditionExchangeItemRepository,
        SpeditionExchangeOfferRepository $speditionExchangeOffer,
        FirmRepository                   $firmRepository
    )
    {
        $this->speditionExchangeRepository = $speditionExchangeRepository;
        $this->speditionExchangeItemRepository = $speditionExchangeItemRepository;
        $this->speditionExchangeOffer = $speditionExchangeOffer;
        $this->firmRepository = $firmRepository;
    }

    public function generateLink(GenerateLinkRequest $request)
    {
        $data = json_decode($request->validated()['data']);

        if (empty($data)) {
            return "Nie wybrano żadnego zamówienia";
        }

        $hash = uniqid();
        $speditionExchange = $this->speditionExchangeRepository->create([
            'hash' => $hash,
        ]);

        foreach ($data as $item) {
            $this->speditionExchangeItemRepository->create([
                'spedition_exchange_id' => $speditionExchange->id,
                'order_id' => $item->id,
                'invoiced' => $item->type == "invoiced" ? 1 : 0,
            ]);
        }

        return $this->generateLinkForExchange($hash);
    }

    public function getDetails($hash)
    {
        return $this->speditionExchangeRepository
            ->with(['items', 'items.order.speditionPayments', 'items.order', 'items.order.addresses' => function ($q) {
                    $q->where('type', '=', 'DELIVERY_ADDRESS');
                }, 'items.order.warehouse', 'items.order.warehouse.address', 'items.order.warehouse.property',
                    'items.order.packages' => function ($q) {
                        $q->where('service_courier_name', '=', 'GIELDA');
                    }]
            )
            ->findByField('hash', $hash)
            ->first();
    }

    public function newOffer(NewOfferRequest $request, $hash)
    {
        $data = $request->validated();
        $data['driver_approx_arrival_time'] = (new Carbon($data['driver_approx_arrival_time']))->format("H:i");
        $speditionExchange = $this->speditionExchangeRepository->findByField('hash', $hash)->first();

        $data['spedition_exchange_id'] = $speditionExchange->id;
        $this->speditionExchangeOffer->create($data);

        $firm = $this->firmRepository->updateOrCreate(['nip' => $data['nip']], [
            'name' => $data['firm_name'],
            'email' => $data['email'],
            'nip' => $data['nip'],
            'account_number' => $data['account_number'],
            'phone' => $data['phone_number'],
            'notices' => $data['comments'] ?? null,
            'status' => 'ACTIVE',
            'firm_type' => 'DELIVERY',
        ]);

        $firm->address()->updateOrCreate(['firm_id' => $firm->id], [
            'city' => $data['city'],
            'flat_number' => $data['number'],
            'address' => $data['street'],
            'postal_code' => $data['postal_code'],
        ]);

        dispatch_now(new SendOfferToSpedition($data['orderId'], $data['email']));

        $this->createdResponse();
    }

    public function acceptOffer($offerId)
    {
        $offer = $this->speditionExchangeOffer->find($offerId);

        $exchange = $offer->speditionExchange()->first();
        $exchange->chosen_spedition_offer_id = $offer->id;
        $exchange->save();

        Mailer::create()
            ->to($offer->email)
            ->send(new AcceptOfferMail("Potwierdzenie spedycji", $this->generateLinkForExchange($offer->speditionExchange->hash)));

        $rejectedOffers = $exchange->speditionOffers()->where('id', '<>', $offerId)->get();
        foreach ((array)$rejectedOffers as $rejectedOffer) {
            Mailer::create()
                ->to($rejectedOffer[0]->email)
                ->send(new RejectOfferMail("Spedycja nieaktualna"));
        }

        return $this->okResponse();
    }

    /** Helper function */
    protected function generateLinkForExchange($hash)
    {
        return rtrim(config('app.front_nuxt_url'), "/") . "/gielda/transport/{$hash}";
    }
}
