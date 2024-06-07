<?php

namespace App\Http\Controllers;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\Chat;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Entities\ChatAuctionOffer;
use App\Entities\Firm;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Facades\Mailer;
use App\Factory\OrderBuilderFactory;
use App\Helpers\AuctionsHelper;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\LocationHelper;
use App\Helpers\MessagesHelper;
use App\Http\Requests\CreateAuctionRequest;
use App\Http\Requests\CreateChatAuctionOfferRequest;
use App\Http\Requests\UpdateChatAuctionRequest;
use App\Mail\AuctionCreationConfirmation;
use App\Mail\NotificationAboutFirmPanelMail;
use App\Repositories\ChatAuctionFirms;
use App\Repositories\ChatAuctionOffers;
use App\Repositories\Employees;
use App\Services\ChatAuctionOfferService;
use App\Services\ChatAuctionsService;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\MessageService;
use App\Services\ProductService;
use Carbon\Carbon;
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
use Illuminate\Support\Facades\Log;
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
     * @return View|RedirectResponse
     */
    public function create(Chat $chat): View|RedirectResponse
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
     * @param MessagesHelper $helper
     * @return RedirectResponse
     * @throws DeliverAddressNotFoundException
     */
    public function store(Chat $chat, CreateAuctionRequest $request, MessagesHelper $helper): RedirectResponse
    {
        $request->validate([
            'end_of_auction' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
            'price' => 'required|numeric|between:0,100',
            'quality' => 'required|numeric|between:0,100'
        ], [
            'end_of_auction.required' => 'Pole data zakończenia przetargu jest wymagane.',
            'end_of_auction.date' => 'Pole data zakończenia przetargu musi być prawidłową datą.',
            'end_of_auction.after' => 'Pole data zakończenia przetargu musi być datą przyszłą.',
            'notes.string' => 'Pole dodatkowe informacje musi być tekstem.',
            'notes.max' => 'Pole dodatkowe informacje nie może mieć więcej niż 1000 znaków.',
            'price.required' => 'Pole cena jest wymagane.',
            'price.numeric' => 'Pole cena musi być liczbą.',
            'price.between' => 'Pole cena musi być wartością między 0 a 100.',
            'quality.required' => 'Pole jakość jest wymagane.',
            'quality.numeric' => 'Pole jakość musi być liczbą.',
            'quality.between' => 'Pole jakość musi być wartością między 0 a 100.',
        ]);

        $chat = Chat::where('order_id', '=', $chat->order->id)->first();
        $helper = new MessagesHelper();

        if (!$chat) {
            $helper->orderId = $chat->order->id;
        } else {
            $helper->chatId = $chat->id;
        }

        $helper->currentUserId = $chat->order->id;
        $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
        $userToken = $helper->encrypt();

        if ($chat->auctions->first()) {
            return redirect()->route('chat.show', ['token' => $userToken])->with('auctionCreationSuccess', true);
        }

        $auction = $this->chatAuctionsService->createAuction(
            CreateChatAuctionDTO::fromRequest($chat, $request->validated())
        );


        Mailer::create()
            ->to($chat->order->customer->login)
            ->send(new AuctionCreationConfirmation(
                $auction
            ));


        $showAuctionInstructions = request()->query('showAuctionInstructions');

        $this->chatAuctionsService->confirmAuction($auction);

        return redirect()->route('chat.show', ['token' => $userToken, 'showAuctionInstructions' => $showAuctionInstructions])->with('auctionCreationSuccess', true);
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

        $pricingData = [];

        // Iterate over all items in the request data
        foreach ($request->except('_token') as $key => $value) {
            // Example key: commercial_price_net_251638
            if (preg_match('/(.+)_(\d+)/', $key, $matches)) {
                $priceType = $matches[1]; // e.g., 'commercial_price_net'
                $itemId = $matches[2];    // e.g., '251638'

                // Initialize the nested array for the item ID if not already initialized
                if (!isset($pricingData[$itemId])) {
                    $pricingData[$itemId] = [];
                }

                // Assign the value to the appropriate type
                $pricingData[$itemId][$priceType] = $value;
            }
        }

        foreach ($pricingData as $k => $item) {
            $product = Product::find($k);

            $this->chatAuctionOfferService->createOffer(CreateChatAuctionOfferDTO::fromRequest($item + [
                'firm_id' => $firm->firm_id,
                'chat_auction_id' => $firm->chat_auction_id,
                'product_id' => $product->id,
            ]));
        }

        return redirect()->to(url()->previous() . '?success=true');
    }

    /**
     * @param ChatAuction $auction
     * @return View
     */
    public function end(ChatAuction $auction): View
    {
        $order = $auction->chat->order;
        $firms = $this->chatAuctionFirmsRepository->getFirmsByChatAuction($auction->id);

        if ($firms->count() == 0) {
            $productService = app(ProductService::class);
            $items = collect($productService->getVariations($order));
            $firms = collect();

            foreach($items as $item) {
                foreach ($item as $i) {
                    $i = Product::find($i['id']);
                    $firms->push(Firm::where('symbol', $i->product_name_supplier)->first());
                }
            }

            foreach ($firms as $firm) {
                 $firm->firm = $firm;
            }
        }

        $coordinatesOfUser = DB::table('postal_code_lat_lon')->where('postal_code', $order->getDeliveryAddress()->postal_code)->get()->first();

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
                        'firmId' => $firm?->firm?->id ?? $firm->id
                    ]
                );

                $radius = $raw?->distance;

                $firm->distance = round($raw?->distance, 2);

                if ($radius && $radius > $firm->firm?->warehouses()->first()->radius ?? $firm?->warehouses()->first()->radius) {
                    $firms->forget($key);
                }
            }
        }

        if (request()->query('isFirm')) {
            return view('auctions.prices-for-firm', [
                'products' => $order->items,
                'offers' => $auction->offers,
            ], compact('order', 'firms', 'auction'));
        }

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

        foreach ($auctions as $auction) {
            $auction->date_of_delivery = 'Od: ' . $auction->chat?->order->dates->customer_delivery_date_from . ' Do: ' . $auction->chat?->order->dates->customer_delivery_date_to;
        }

        return response()->json(
            [
                $firm,
                $auctions,
                ['haveToFillPrices' => $firm->products->where('date_of_price_change', '<', now())->count() > 0]
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

        foreach ($firms as $firm) {
            $firm->distance = LocationHelper::getDistanceOfProductForZipCode($firm, $chat->order->addresses->first()->postal_code);
        }

        return view('auctions.pre-data-prices-table', [
            'order' => $chat->order,
            'firms' => $firms,
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

                $firm->distance = $raw?->distance;

                if ($radius && $radius > $firm->warehouses()->first()->radius) {
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

    public function endAuctionStore(int $auctionId): RedirectResponse
    {
        ChatAuction::find($auctionId)->update([
            'end_of_auction' => now()->toDate()
        ]);

        return redirect()->back()->with([
            'message' => 'Pomyślnie zakończono aukcję! Otrzymasz wiadomość na swój adres email z potwierdzeniem',
            'alert-type' => 'success',
        ]);
    }

    public function styrofoamVariationsView(Order $order, ProductService $productService): View
    {
        $items = collect($productService->getVariations($order));
        $firms = collect();

        foreach($items as $item) {
            foreach ($item as $i) {
                $i = Product::find($i['id']);
                $firms->push(Firm::where('symbol', $i->product_name_supplier)->first());
            }
        }

        return view('chat.auction-end', [
            'products' => $order->items,
        ], compact('order', 'firms'));
    }

    public function makeOrder(Firm $firm, Order $order): View
    {
        $finalItems = collect();
        $items = $order->items;

        foreach ($items as $item) {
            $products = Product::where('product_group', $item->product->product_group)->where('product_name_supplier', $firm->symbol)->get();
            foreach ($products as $p) {
                $p->quantity = $item->quantity;
            }

            $finalItems->push($products);
        }

        return view('auctions.create-order-auction', compact('order', 'firm', 'finalItems'));
    }

    /**
     * @throws Exception
     */
    public function submitOrder(Order $order, Request $request): void
    {
        $requestData = $request->all();
        $products = $requestData['productData'];

        $arr = [];
        AddLabelService::addLabels($order, [206], $arr, []);

        if ($request->get('cashOnDelivery')) {
            $arr = [];
            AddLabelService::addLabels($order, [267], $arr, []);
        }

        $arr = [];
        RemoveLabelService::removeLabels($order, [224, 266], $arr, [], null);

        $orderBuilder = OrderBuilderFactory::create();
        $order->items()->delete();
        $companies = [];

        foreach ($products as $product) {
            $productId = $product['productId'];
            $quantity = $product['quantity'];

            $product = Product::find($productId);
            $offer = ChatAuctionOffer::where('firm_id', $product->firm->id)
                ->where('order_item_id', $order->items()?->where('product_id', $product?->id)?->first()?->id)
                ->first();

            $orderBuilder->assignItemsToOrder(
                $order,
                [
                    $product->toArray() + [
                        'amount' => $quantity,
                        'gross_selling_price_commercial_unit' => $offer?->basic_price_gross ?? $product->gross_selling_price_commercial_unit
                    ],
                ],
                false
            );

            Log::notice($offer?->basic_price_gross ?? $product->gross_selling_price_commercial_unit);

            $company = Firm::first();
            $chat = $order->chat;

            if (in_array($company->id, $companies)) {
                continue;
            }

            foreach ($company->employees as $employee) {
                MessageService::createNewCustomerOrEmployee($chat, new Request(['type' => 'Employee']), $employee);
            }

            $companies[] = $company->id;
        }

        $order->additional_service_cost = 50;
        $order->auction_order_placed = true;
        $order->save();
    }

    public function confirm(ChatAuction $auction): JsonResponse
    {
        $this->chatAuctionsService->confirmAuction($auction);
    }
}
