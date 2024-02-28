<?php

namespace App\Http\Controllers;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\Chat;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Entities\Firm;
use App\Entities\Order;
use App\Entities\Product;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Facades\Mailer;
use App\Helpers\AuctionsHelper;
use App\Helpers\Exceptions\ChatException;
use App\Http\Requests\CreateAuctionRequest;
use App\Http\Requests\CreateChatAuctionOfferRequest;
use App\Http\Requests\UpdateChatAuctionRequest;
use App\Mail\NotificationAboutFirmPanelMail;
use App\Repositories\ChatAuctionFirms;
use App\Repositories\Employees;
use App\Services\ChatAuctionOfferService;
use App\Services\ChatAuctionsService;
use App\Services\ProductService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Matrix\Builder;

class AuctionsController extends Controller
{
    public function __construct(
        private readonly ChatAuctionsService     $chatAuctionsService,
        private readonly ChatAuctionOfferService $chatAuctionOfferService,
        private readonly ChatAuctionFirms        $chatAuctionFirmsRepository,
    ) {}

    /**
     * Show the form for creating a new resource.
     *
     * @param Chat $chat
     * @return View
     */
    public function create(Chat $chat): View
    {
        return view('auctions.create', [
            'chat' => $chat,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Chat $chat
     * @param CreateAuctionRequest $request
     * @return Redirector
     */
    public function store(Chat $chat, CreateAuctionRequest $request)
    {
        $this->chatAuctionsService->createAuction(CreateChatAuctionDTO::fromRequest($chat, $request->validated()));

        return redirect($request->query('backUrl'));
    }

    /**
     * Display the specified resource.
     *
     * @param ChatAuction $auction
     * @return Application|Factory|View
     */
    public function show(ChatAuction $auction): View|Factory|Application
    {
        return view('auctions.show', [
            'auction' => $auction,
        ]);
    }

    /**
     * Show success page
     *
     * @return View
     */
    public function success(): View
    {
        return view('auctions.success');
    }

    /**
     * @throws DeliverAddressNotFoundException
     */
    public function confirm(ChatAuction $auction): RedirectResponse
    {
        $this->chatAuctionsService->confirmAuction($auction);

        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $token
     * @return View
     */
    public function createOffer(string $token): View
    {
        return view('auctions.create-offer', [
            'chat_auction_firm' => ChatAuctionFirms::getChatAuctionFirmByToken($token),
            'products' => ChatAuctionFirms::getItemsByToken($token),
            'current_firm_offers' => ChatAuctionFirms::getCurrentFirmOffersByToken($token),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $token
     * @param CreateChatAuctionOfferRequest $request
     * @return RedirectResponse
     */
    public function storeOffer(string $token, CreateChatAuctionOfferRequest $request): RedirectResponse
    {
        $firm = ChatAuctionFirm::query()->where('token', $token)->firstorfail();

        $this->chatAuctionOfferService->createOffer(CreateChatAuctionOfferDTO::fromRequest($request->validated() + [
            'firm_id' => $firm->firm_id,
            'chat_auction_id' => $firm->chat_auction_id
        ]));

        return redirect()->back()->with('success', 'Pomyślnie dodano ofertę');
    }

    /**
     * @param ChatAuction $auction
     * @return View
     */
    public function end(ChatAuction $auction): View
    {
        $order = $auction->chat->order;
        $firms = $this->chatAuctionFirmsRepository->getFirmsByChatAuction($auction->id);

        return view('chat.auction-end', [
            'products' => $order->items,
            'offers' => $auction->offers,
        ], compact('order', 'firms', 'auction'));
    }

    /**
     * @param Request $request
     * @param ChatAuction $auction
     * @return RedirectResponse
     * @throws ChatException|Exception
     */
    public function endCreateOrders(Request $request, ChatAuction $auction): RedirectResponse
    {
        $customer = $auction->chat->customers()->first();

        $this->chatAuctionsService->endAuction($auction, $request->get('order'), $customer);

        return redirect()->route('success');
    }

    /**
     * @param UpdateChatAuctionRequest $request
     * @param ChatAuction $auction
     * @return RedirectResponse
     */
    public function update(UpdateChatAuctionRequest $request, ChatAuction $auction): RedirectResponse
    {
        $auction->update(
            $request->validated()
        );

        return redirect()->back();
    }

    public function getAuctions(string $token): JsonResponse
    {
        $firm = Firm::where('access_token', $token)->firstOrFail();

        $auctions = $this->chatAuctionsService->getAuctions(
            $firm,
        );

        return response()->json(
            [
                $firm,
                $auctions,
            ]
        );
    }

    public function sendNotificationAboutFirmPanel(Firm $firm): RedirectResponse
    {
        $employees = $firm->employees()->whereHas('employeeRoles', function ($q) {
            $q->where('name', 'zmiana cen');
        })->get();

        foreach ($employees as $employee) {

            Mailer::create()
                ->to($employee->email)
                ->send(new NotificationAboutFirmPanelMail(
                    $firm,
                ));
        }

        return redirect()->back()->with([
            'message' => 'Pomyślnie wysłano wiadomość e-mail',
            'alert-type' => 'success'
        ]);
    }

    public function displayPreDataPricesTable(Chat $chat): View
    {
        $variations = app(ProductService::class)->getVariations($chat->order);

        $firms = array_unique(app(ChatAuctionsService::class)->getFirms($variations));

        return view('auctions.pre-data-prices-table', [
            'order' => $chat->order,
            'firms' => $firms
        ]);
    }

    public function displayPricesTable(): View
    {
        $products = Product::where('variation_group', 'styropiany')
            ->whereHas('children')
            ->get();

        $productGroups = [];
        $filteredProducts = collect(); // Initialize an empty collection for filtered products

        foreach ($products as $product) {
            $group = AuctionsHelper::getTrimmedProductGroupName($product);

            if (!in_array($group, $productGroups)) {
                $productGroups[] = $group;
                $filteredProducts->push($product); // Add product to the filtered collection
            }
        }


        $customersZipCode = request()->query('zip-code');
        $coordinatesOfUser = DB::table('postal_code_lat_lon')->where('postal_code', $customersZipCode)->get()->first();

        $firms = Firm::whereHas('products', function ($q) {
            $q->where('variation_group', 'styropiany');
        })->get();

        if ($coordinatesOfUser) {
            foreach ($firms as $key => $firm) {
                $raw = DB::selectOne(
                    'SELECT w.id, pc.latitude, pc.longitude, 1.609344 * SQRT(
                        POW(69.1 * (pc.latitude - :latitude), 2) +
                        POW(69.1 * (:longitude - pc.longitude) * COS(pc.latitude / 57.3), 2)) AS distance
                        FROM postal_code_lat_lon pc
                             JOIN warehouse_addresses wa on pc.postal_code = wa.postal_code
                             JOIN warehouses w on wa.warehouse_id = w.id
                        WHERE w.firm_id = :firmId AND w.status = \'ACTIVE\'
                        ORDER BY distance
                    limit 1',
                    [
                        'latitude' => $coordinatesOfUser->latitude,
                        'longitude' => $coordinatesOfUser->longitude,
                        'firmId' => $firm->id
                    ]
                );

                $radius = $raw?->distance;

                if ($radius ?? 100000 > $firm->warehouses()->first()->radius) {
                    $firms->forget($key);
                }
            }
        }


        return view('auctions.pre-data-prices-table', [
            'products' => $filteredProducts,
            'firms' => $firms,
        ]);
    }

    public function getStyrofoamTypes(): JsonResponse
    {
        $styrofoamTypes = Product::where('variation_group', 'styropiany')
            ->whereHas('children')
            ->get();

        $productGroups = [];

        foreach ($styrofoamTypes as $product) {
            $group = AuctionsHelper::getTrimmedProductGroupName($product);

            if (!in_array($group, $productGroups)) {
                $productGroups[] = $group;
                continue;
            }
        }

        return response()->json($productGroups);
    }

    public function getQuotesByStyrofoamType(string $type): JsonResponse
    {
        $products = Product::where('product_group', 'like', '%' .  $type . '%')
            ->with('price')
            ->get()
            ->unique('product_name_supplier');

        return response()->json($products);
    }
}
