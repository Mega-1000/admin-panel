<?php

namespace App\Http\Controllers;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\Chat;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Entities\ChatAuctionOffer;
use App\Entities\ContactApproach;
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
use App\Helpers\SMSHelper;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Requests\CreateAuctionRequest;
use App\Http\Requests\CreateChatAuctionOfferRequest;
use App\Http\Requests\UpdateChatAuctionRequest;
use App\Mail\AuctionCreationConfirmation;
use App\Mail\NotificationAboutFirmPanelMail;
use App\Mail\RealizationStartedConfirmation;
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

        foreach ($auctions['data'] as &$auction) {
            $auction['editPricesLink'] = route('auctions.offer.create', [
                'token' => ChatAuctionFirm::where('chat_auction_id', $auction['id'])
                    ->where('firm_id', $firm->id)
                    ->first()
                    ->token
            ]);

            try {
                $auction['date_of_delivery'] = 'Od: ' . $auction['chat']['order']['dates']['customer_delivery_date_from'] . ' Do: ' .
                $auction['chat']['order']['dates']['customer_delivery_date_to'];
            } catch (Exception $e) {
                $auction['date_of_delivery'] = 'Brak daty dostawy';
            }
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
        $customersZipCode = request()->query('zip-code');

        // Generate a unique cache key based on the zip code
        $cacheKey = "prices_table_{$customersZipCode}";

        // Attempt to retrieve cached data or compute if not found
        $viewData = Cache::remember($cacheKey, 50 * 60, function () use ($customersZipCode) {
            $products = Product::where('variation_group', 'styropiany')
                ->whereHas('children')
                ->get();

            $productGroups = [];
            $filteredProducts = collect();

            foreach ($products as $product) {
                $group = AuctionsHelper::getTrimmedProductGroupName($product);

                if (!in_array($group, $productGroups)) {
                    $productGroups[] = $group;
                    $filteredProducts->push($product);
                }
            }

            $coordinatesOfUser = DB::table('postal_code_lat_lon')->where('postal_code', $customersZipCode)->first();

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

            return [
                'products' => $filteredProducts,
                'firms' => $firms,
            ];
        });

        return view('auctions.pre-data-prices-table', $viewData);
    }

    public function getStyrofoamTypes(): JsonResponse
    {
        $styrofoamTypes = Product::where('variation_group', 'styropiany')
            ->whereHas('children')
            ->get();

        $productGroups = [];

        foreach ($styrofoamTypes as $product) {
            $parentCategoryName = $product->category->parentCategory->name ?? 'Uncategorized';
            $group = AuctionsHelper::getTrimmedProductGroupName($product);

            if (!isset($productGroups[$parentCategoryName])) {
                $productGroups[$parentCategoryName] = [];
            }

            if (!in_array($group, $productGroups[$parentCategoryName])) {
                $productGroups[$parentCategoryName][] = $group;
            }
        }

        return response()->json($productGroups);
    }

    public function getQuotesByStyrofoamType(string $type): JsonResponse
    {
        $products = Product::where('name', 'like', '%' .  $type . '%')
            ->with('price')
            ->get()
            ->unique('product_name_supplier');

        foreach ($products as $k => $product) {
            if (!LocationHelper::getAvaiabilityOfProductForZipCode($product->firm, \request()->query('zipCode')))
            {
                $products->forget($k);
            }
        }

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

        $order->approved_at = now();
        $order->save();

        $arr = [];
        AddLabelService::addLabels($order, [206], $arr, []);

        $order->customer_acceptation_date = now();
        $order->save();

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
                ->whereHas('product', function ($q) use ($product) {
                    $q->where('product_group', $product->product_group)
                        ->where('additional_info1', $product->additional_info1);
                })
                ->first();

            $orderBuilder->assignItemsToOrder(
                $order,
                [
                    $product->toArray() + [
                        'amount' => $quantity,
                        'gross_selling_price_commercial_unit' => $offer?->basic_price_gross ?? $product->price->gross_selling_price_commercial_unit
                    ],
                ],
                false
            );

            $item = $order->items()->where('order_id', $order->id)->where('product_id', $product->id)->first();
            $item->gross_selling_price_commercial_unit = ($offer?->basic_price_net * 1.23 ?? $product->price->gross_selling_price_basic_unit) * $product->packing->numbers_of_basic_commercial_units_in_pack;
            $item->net_selling_price_basic_unit = $offer?->basic_price_net ?? $product->price->gross_selling_price_basic_unit / 1.23;
            $item->gross_selling_price_basic_unit = $offer?->basic_price_net * 1.23 ?? $product->price->gross_selling_price_basic_unit;
            $item->net_selling_price_commercial_unit = ($offer?->basic_price_net ?? $product->price->gross_selling_price_basic_unit / 1.23) * $product->packing->numbers_of_basic_commercial_units_in_pack;

            $base_price_net = ($offer?->basic_price_net ?? $product->price->gross_selling_price_basic_unit / 1.23) - 1;

            $item->net_purchase_price_basic_unit = $base_price_net;
            $item->net_purchase_price_commercial_unit = $base_price_net * $product->packing->numbers_of_basic_commercial_units_in_pack;
            $item->net_purchase_price_commercial_unit_after_discounts = $base_price_net * $product->packing->numbers_of_basic_commercial_units_in_pack;
            $item->net_purchase_price_basic_unit_after_discounts = $base_price_net;
            $item->net_purchase_price_calculated_unit_after_discounts = $base_price_net * $product->packing->numbers_of_basic_commercial_units_in_pack;
            $item->net_purchase_price_aggregate_unit_after_discounts = $base_price_net * $product->packing->numbers_of_basic_commercial_units_in_pack;

            $item->save();


            $company = $order->items()->first()->product->firm;


            $chat = $order->chat;

            if (in_array($company->id, $companies)) {
                continue;
            }

            $lowestDistance = PHP_INT_MAX;
            $closestEmployee = null;

            foreach ($company->employees as $employee) {
                $employee->distance = LocationHelper::getDistanceOfClientToEmployee($employee, $order->customer);

                if ($employee->distance < $lowestDistance) {
                    $lowestDistance = $employee->distance;
                    $closestEmployee = $employee;
                }
            }

            MessageService::createNewCustomerOrEmployee($chat, new Request(['type' => 'Employee']), $closestEmployee);

            $companies[] = $company->id;
        }

        $order->warehouse_id = LocationHelper::nearestWarehouse($order->customer, $order->items()->first()->product->firm)->id;
        $order->additional_service_cost = 50;
        $order->auction_order_placed = true;
        $order->save();

        SMSHelper::sendSms(
            576205389,
            "EPH Polska",
            "Dzień dobry, rozpocząłeś realizacje zamówienia na platformie eph polska. Prosimy o opłacenie faktury proformy pod następującym linkiem: https://mega1000.pl/payment?token=$order->token&total={$order->getValue()}&credentials={$order->customer->login}:{$order->customer->phone}",
        );

        if ($order->getValue() !== $request->get('totalPrice') + 50) {
            AddLabelService::addLabels($order, [269], $arr, [], null);
        }


        Mailer::create()
            ->to($order->customer->login)
            ->send(new RealizationStartedConfirmation(
                $order
            ));
    }

    public function confirm(ChatAuction $auction): JsonResponse
    {
        $this->chatAuctionsService->confirmAuction($auction);
    }

    public function markAsNotActive($orderId): string
    {
        $order = Order::find($orderId);

        $arr = [];
        RemoveLabelService::removeLabels($order, [269], $arr, [], null);
        AddLabelService::addLabels($order, [225], $arr, []);

        return '<script>alert("Zapisaliśmy brak zainteresowania ofertą z Państwa strony!")</script>';
    }

    public function SubmitOfferAskForm(Request $request): JsonResponse
    {
        $data = $request->all();

        ContactApproach::create([
            'phone_number' => $data['phone'],
            'referred_by_user_id' => 29321,
            'done' => false,
            'notes' => implode(' ', $data),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
