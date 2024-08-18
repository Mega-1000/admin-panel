<?php

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\Entities\Category;
use App\Entities\Chat;
use App\Entities\Customer;
use App\Entities\FirmSource;
use App\Entities\Order;
use App\Entities\ShippingPayInReport;
use App\Entities\Status;
use App\Facades\Mailer;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\TransportSumCalculator;
use App\Http\Controllers\AllegroMessageController;
use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\FirmsController;
use App\Http\Controllers\Api\OrderPackageController;
use App\Http\Controllers\Api\ProductOpinionController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\AuctionsController;
use App\Http\Controllers\ContactApproachController;
use App\Http\Controllers\ShipmentPayInReportByInvoiceNumber;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\OrderStatusChangedNotificationJob;
use App\Jobs\ReferFriendNotificationJob;
use App\Mail\AuctionCreationConfirmation;
use App\Mail\NewStyroOfferMade;
use App\Services\ChatAuctionsService;
use App\Services\Label\AddLabelService;
use App\Services\OrderAddressesService;
use App\Services\ProductService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */
Route::middleware('auth:api')->group(function () {
    Route::prefix('user')->name('api.customers.')->group(function () {
        Route::get('/', 'Api\CustomersController@getDetails')->name('show');
        Route::post('change-password', 'Api\CustomersController@changePassword')->name('change-password');
        Route::put('update', 'Api\CustomersController@update')->name('update');
        Route::get('orders', 'Api\CustomersController@getOrders')->name('get-orders');
        Route::post('unregister', 'Api\CustomersController@unregister')->name('unregister');
    });

    Route::prefix('orders')->name('api.orders.')->group(function () {
        Route::get('getAll', 'Api\OrdersController@getAll')->name('getall');
        Route::post('uploadProofOfPayment', 'Api\OrdersController@uploadProofOfPayment')->name('proof-of-payment');
        Route::post('update-order-address/{orderId}', 'Api\OrdersController@updateOrderAddressEndpoint')->name('update-order-addresses');
        Route::post('move-to-unactive/{order}', 'Api\OrdersController@moveToUnactive')->name('moveToUnactive');
        Route::post('remind-about-offer/{order}', 'Api\OrdersController@scheduleOrderReminder')->name('remindAboutOffer');
    });
    Route::get('chat/getHistory', 'Api\MessagesController@getHistory')->name('api.messages.get-history');
    Route::get('invoices/get/{id}', 'Api\InvoicesController@getInvoice')->name('api.invoices.get');
    Route::post('create_contact_chat', 'Api\MessagesController@createContactChat')->name('api.orders.create_contact_chat');
    Route::post('createCustomerComplaintChat/{order}', 'Api\MessagesController@createCustomerComplaintChat')->name('api.orders.createCustomerComplaintChat');

    Route::middleware('staff.api')->group(function () {
        Route::group(['prefix' => 'faqs'], function () {
            Route::post('/', 'Api\FaqController@store')->name('api.faq.save');
            Route::post('/ask', 'Api\FaqController@askQuestion')->name('api.faq.ask');
            Route::post('/categories-positions', 'Api\FaqController@setCategoryPosition')->name('api.faq.categories-positions');
            Route::post('/questions-positions', 'Api\FaqController@setQuestionsPosition')->name('api.faq.questions-positions');
            Route::delete('/{id}', 'Api\FaqController@destroy')->name('api.faq.destroy');
        });

        Route::get('staff/isStaff', function () { return true; })->name('api.staff.isStaff');
        Route::post('change-image', 'Api\CategoriesController@changeImage')->name('api.categories.change-image');
        Route::post('update-category', 'Api\CategoriesController@updateCategory')->name('api.categories.update-category');
        Route::post('categories/create', 'Api\CategoriesController@create')->name('api.categories.create');
        Route::delete('categories/delete/{category}', 'Api\CategoriesController@delete')->name('api.categories.delete');

        Route::post('products/{product}', [ProductsController::class, 'update'])->name('api.products.update');
    });

    Route::post('/create-message', AllegroMessageController::class);
});
Route::get('get-auctions/{firmToken}', [AuctionsController::class, 'getAuctions'])->name('api.auctions.get-auctions');

Route::get('order/invoice/{order}', 'Api\InvoiceController@getInvoicesForOrder')->name('api.orders.invoice');

Route::post('oauth/token/from-email/{email}', 'Api\CustomersController@getTokenFromEmail')->name('api.customers.get-token-from-email');

Route::post('/register', 'Api\CustomersController@register')->name('api.customers.register');

Route::group(['prefix' => 'faqs'], function () {
    Route::put('/{id}', 'Api\FaqController@update')->name('api.faq.update');
    Route::get('/categories', 'Api\FaqController@getCategories')->name('api.faq.categories');
    Route::get('/get', 'Api\FaqController@getQuestions')->name('api.faq.get');
    Route::get('/', 'Api\FaqController@index')->name('api.faq.index');
    Route::get('/{id}', 'Api\FaqController@show')->name('api.faq.show');
});

Route::get('custom/pages', 'Api\CustomPagesController@getPages')->name('api.custompages.get');

Route::post('packages/count', 'Api\PackageController@countPackages')->name('api.packages.count');

Route::post('orders', 'Api\OrdersController@store')->name('api.orders.store');
Route::post('customers', 'Api\CustomersController@store')->name('api.customers.store');
Route::get('customers/emailExists/{email}', 'Api\CustomersController@emailExists')->name('api.customers.email-exists');
Route::post('new_order', 'Api\OrdersController@newOrder')->name('api.orders.new');

Route::middleware('client')->group(function () {
    Route::post('orders/message', 'Api\OrdersController@storeMessage')->name('api.orders.message.store');
    Route::get('orders/messages/{frontDbOrderId}', 'Api\OrdersController@getMessages')->name('api.orders.messages.get');
    Route::get('orders/{frontDbOrderId}', 'Api\OrdersController@getOrder')->name('api.orders.get');
});

Route::get('company-info/by-nip/{nip}', 'Api\CompanyInfoController@byNip')
    ->name('api.company-info.by-nip');

Route::post('customers/{orderId}/update-delivery-address', 'Api\CustomersController@updateCustomerDeliveryAddress')->name('api.orders.update-customer-delivery-addresses');
Route::post('customers/{orderId}/update-invoice-address', 'Api\CustomersController@updateCustomerInvoiceAddress')->name('api.orders.update-customer-invoice-addresses');
Route::get('orders/{orderId}/customer-delivery-address', 'Api\OrdersController@getCustomerDeliveryAddress')->name('api.orders.get-customer-delivery-address');
Route::get('orders/{orderId}/customer-invoice-address', 'Api\OrdersController@getCustomerInvoiceAddress')->name('api.orders.get-customer-invoice-address');
Route::get('orders/{orderId}/customer-standard-address', 'Api\OrdersController@getCustomerStandardAddress')->name('api.orders.get-customer-standard-address');
Route::get('orders/{orderId}/ready-to-ship-form-autocomplete-data', 'Api\OrdersController@getReadyToShipFormAutocompleteData')->name('api.orders.get-ready-to-ship-form-autocomplete-data');
Route::post('orders/{orderId}/update-order-delivery-and-invoice-addresses', 'Api\OrdersController@updateOrderDeliveryAndInvoiceAddresses')->name('api.orders.update-order-delivery-and-invoice-addresses');
Route::post('orders/{orderId}/decline-proform', 'Api\OrdersController@declineProform')->name('api.orders.decline-proform');
Route::post('orders/{orderId}/accept-delivery-invoice-data', 'Api\OrdersController@acceptDeliveryInvoiceData')->name('api.orders.accept-delivery-invoice-data');
Route::post('orders/{orderId}/accept-receiving', 'Api\OrdersController@acceptReceivingOrder')->name('api.orders.accept-receiving');
Route::get('orders/{order}/latests-orders-delivery-info', 'Api\OrdersController@getLatestDeliveryInfo')->name('api.orders.get-latest-delivery-info');
Route::get('orders/{order}/latests-orders-invoice-info', 'Api\OrdersController@getLatestInvoiceInfo')->name('api.orders.get-latest-invoice-info');

Route::get('orders/getByToken/{token}', 'Api\OrdersController@getByToken');

Route::post('order-warehouse-notification/deny/{notificationId}', 'Api\OrderWarehouseNotificationController@deny')
    ->name('api.order-warehouse-notification.deny');
Route::post('order-warehouse-notification/accept/{notificationId}', 'Api\OrderWarehouseNotificationController@accept')
    ->name('api.order-warehouse-notification.accept');
Route::get('order-warehouse-notification/{notificationId}', 'Api\OrderWarehouseNotificationController@getNotification')
    ->name('api.order-warehouse-notification.get');
Route::post('order-warehouse-notification/accept/{notificationId}/sendInvoice', 'Api\OrderWarehouseNotificationController@sendInvoice')
    ->name('api.order-warehouse-notification.accept.sendInvoice');
Route::post('order-warehouse-notification/accept/{notificationId}/changeStatus', 'Api\OrderWarehouseNotificationController@changeStatus')
    ->name('api.order-warehouse-notification.accept.changeStatus');
Route::get('order-shipping-cancelled/{package_id}', 'Api\OrdersController@orderPackagesCancelled')->name('api.order-shipping-cancelled');
Route::get('searchProduct/{query}', [ProductsController::class, 'searchProduct'])->name('searchProduct');

Route::post('/productOpinion/create', [ProductOpinionController::class, 'create']);


Route::get('get-associated-labels-to-order-from-group/{labelGroupName}', 'Api\LabelsController@getAssociatedLabelsToOrderFromGroup')->name('api.labels.get-associated-labels-to-order-from-group');

Route::get('get-labels-scheduler-await/{userId}', 'Api\LabelsController@getLabelsSchedulerAwait')->name('api.labels.get-labels-scheduler-await');
Route::post('set-scheduled-times', 'Api\LabelsController@setScheduledTimes')->name('api.labels.set-scheduled-times');
Route::post('scheduled-time-reset-type-c', 'Api\LabelsController@scheduledTimeResetTypeC')->name('api.labels.scheduled-time-reset-type-c');

Route::get('products/get-hidden', 'Api\ProductsController@getHiddenProducts')->name('api.get-hidden-products');
Route::get('products/price-changes/{id}/get', 'Api\ProductsController@getProductsForPriceUpdates')->name('api.get-products-for-price-updates');
Route::post('products/send-products-new-price/{id}/send', 'Api\ProductsController@updateProductsPrice')->name('api.update-products-price');
Route::get('products/categories/get', 'Api\ProductsController@getProductsByCategory')->name('api.get-products-by-category');
Route::get('products/categories/mobile/get', 'Api\ProductsController@getProductsByCategoryForMobile')->name('api.get-products-by-category-mobile');
Route::get('products/categories', 'Api\ProductsController@getCategoriesTree')->name('api.get-product-categories');
Route::get('products/categories/{id}', 'Api\ProductsController@getCategory')->name('api.get-category');
Route::get('products/chimney', 'Api\ProductsController@getProductsForChimney')->name('api.get-products-for-chimney');
Route::get('products/{id}', 'Api\ProductsController@getProduct')->name('api.get-product');
Route::get('products/', 'Api\ProductsController@getProducts')->name('api.get-products');

Route::post('firms/updateData/{firm_id}', 'Api\FirmsController@updateData')->name('api.update-data');
Route::get('firm/{firm:symbol}', [FirmsController::class, 'getFirmBySymbol']);

Route::post('spedition-exchange/generate-link', 'Api\SpeditionExchangeController@generateLink')->name('api.spedition-exchange.generate-link');
Route::get('spedition-exchange/get-details/{hash}', 'Api\SpeditionExchangeController@getDetails')->name('api.spedition-exchange.get-details');
Route::post('spedition-exchange/new-offer/{hash}', 'Api\SpeditionExchangeController@newOffer')->name('api.spedition-exchange.new-offer');
Route::get('spedition-exchange/accept-offer/{offerId}', 'Api\SpeditionExchangeController@acceptOffer')->name('api.spedition-exchange.accept-offer');

Route::post('banks', 'Api\BankController@getBanks')->name('api.banks');
Route::post('categories/details', 'Api\CategoriesController@getCategoriesDetails')->name('api.categories.details');
Route::get('categories/details/search', 'Api\CategoriesController@getCategoryDetails')->name('api.categories.search');

Route::post('chat/newMessage/{token}', 'Api\MessagesController@postNewMessage')->name('api.messages.post-new-message');
Route::get('chat/getMessages/{token}', 'Api\MessagesController@getMessages')->name('api.messages.get-messages');
Route::post('/chat/getUrl', 'Api\MessagesController@getUrl')->name('messages.get-url');
Route::post('chat/askForIntervention/{token}', 'Api\MessagesController@askForIntervention')->name('api.messages.ask-for-intervention');
Route::post('chat/addUser/{token}', 'Api\MessagesController@addUser')->name('api.messages.add-new-user');
Route::post('chat/removeUser/{token}', 'Api\MessagesController@removeUser')->name('api.messages.remove-user');
Route::post('chat/editPrices/{token}', 'Api\MessagesController@editPrices')->name('api.messages.edit-prices');
Route::post('chat/closeChat/{token}', 'Api\MessagesController@closeChat')->name('api.messages.closeChat');
Route::post('chat/callComplaint/{token}', 'Api\MessagesController@callComplaint')->name('api.messages.callComplaint');

Route::post('auth/code/{id}', 'Api\AutheticationController@getToken')->name('api.authenticate.get-token');

Route::group(['prefix' => 'sets', 'as' => 'sets_api.'], __DIR__ . '/api/ProductsSetsRoutes.php');
Route::group(['prefix' => 'tracker', 'as' => 'tracker_api.'], __DIR__ . '/api/TrackerLogsRoutes.php');
Route::group(['prefix' => 'orders', 'as' => 'orders.'], __DIR__ . '/api/OrdersRoutes.php');
Route::group(['prefix' => 'transactions', 'as' => 'transactions_api.'], __DIR__ . '/api/TransactionsRoutes.php');
Route::group(['prefix' => 'customers', 'as' => 'customers.'], __DIR__ . '/api/CustomersRoutes.php');
Route::group(['prefix' => 'working-events', 'as' => 'workingEvents_api.'], __DIR__ . '/api/WorkingEventsRoutes.php');
Route::group(['prefix' => 'countries', 'as' => 'countries.'], __DIR__ . '/api/CountriesRoutes.php');

Route::get('/orders/{id}/sendOfferToCustomer', 'Api\OrdersController@sendOfferToCustomer')->name('api.orders.sendOfferToCustomer');

Route::prefix('discounts')->group(function () {
    Route::get('/get-by-category/{category:name}', [DiscountController::class, 'getByCategory'])
        ->name('discounts.get-by-category');
    Route::get('/get-categories', [DiscountController::class, 'getCategories'])
        ->name('discounts.get-categories');
});

Route::get('styro-warehouses', function () {
    $warehouses = \App\Entities\Firm::whereHas('products', function ($query) {
        $query->where('variation_group', 'styropiany');
    })
        ->with('warehouses')
        ->get()
        ->pluck('warehouses')
        ->flatten();

    foreach ($warehouses as $warehouse) {
        $warehouse->firm_symbol = \App\Entities\Firm::find($warehouse->firm_id)->symbol;
        $warehouse->link = 'http://mega1000.pl/' . $warehouse->firm_symbol . '/' . Category::where('name', $warehouse->firm_symbol)->first()?->id;
    }

    return response()->json($warehouses);
});

Route::get('/get-packages-for-order/{order}', [OrderPackageController::class, 'getByOrder'])
    ->name('api.get-packages-for-order');

Route::get('/shipment-pay-in-report', ShipmentPayInReportByInvoiceNumber::class)->name('shipment-pay-in-report');
Route::get('orders/get-payments-for-order/{token}', 'Api\OrdersController@getPaymentDetailsForOrder')->name('getPayments');
Route::get('orders/get-warehouses-for-order/{token}', 'Api\OrdersController@getWarehousesForOrder');
Route::post('/set-warehouse/{id}/{token}', 'Api\OrdersController@setWarehouse');
Route::get('get-product/{product}', [ProductsController::class, 'getSingleProduct']);

Route::post('/register', [CustomersController::class, 'registerAccount']);

Route::get('contact-approach/{userId}', [ContactApproachController::class, 'getApproachesByUser']);
Route::post('contact-approach/create', [ContactApproachController::class, 'store']);

Route::get('handle-soft-synergy-contact-form', [ContactApproachController::class, 'softSyng']);
Route::get('get-blurred-categories/{category}', [ProductsController::class, 'getBlurredCategories']);
Route::post('submit-offer-ask-form', [AuctionsController::class, 'SubmitOfferAskForm']);

Route::post('styro-help', function (Request $request) {
    $apiUrl = "https://api.anthropic.com/v1/messages";
    $apiKey = "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";
    $anthropicVersion = "2023-06-01";


    $prompt = [
        [
        "role" => "user",
        "content" =>  [
            [
                'type' => 'text',
                'text' => $request->get('message'),
            ]
        ]
    ]
    ];

    $data = [
        "model" => "claude-3-sonnet-20240229",
        "system" => 'Name: styropianplus fasada 044, Price: 42.90; Name: styropianplus fasada 042, Price: 45.60; Name: styropianplus fasada 040, Price: 48.30; Name: styropianplus fasada 033, Price: 53.40; Name: styropianplus fasada 032, Price: 58.50; Name: styropianplus fasada 031, Price: 61.50; Name: styropianplus akustyczny, Price: 44.40; Name: styrmann fasada 042, Price: 48.00; Name: styrmann fasada 040, Price: 54.00; Name: styrmann fasada 033, Price: 66.00; Name: styrmann fasada 032, Price: 71.10; Name: styrmann fasada 031, Price: 75.00; Name: styropmin fasada 042, Price: 50.40; Name: styropmin fasada 040, Price: 53.40; Name: styropak fasada 045, Price: 34.50; Name: styropak fasada 042, Price: 40.80; Name: styropak fasada 040, Price: 46.80; Name: styropak fasada 033, Price: 59.70; Name: styropak fasada 031, Price: 66.60; Name: styropak akustyczny, Price: 46.50; Name: izoterm fasada 045, Price: 42.00; Name: izoterm fasada 042, Price: 42.60; Name: izoterm fasada 040, Price: 45.60; Name: izoterm fasada 032, Price: 54.30; Name: polstyr fasada 045, Price: 41.10; Name: polstyr fasada 044, Price: 42.30; Name: polstyr fasada 042, Price: 43.80; Name: polstyr fasada 040, Price: 48.00; Name: polstyr fasada 038, Price: 51.60; Name: polstyr fasada 033, Price: 51.90; Name: polstyr fasada 032, Price: 52.20; Name: polstyr fasada 031, Price: 58.50; Name: polstyr akustyczny, Price: 42.00; Name: swisspor fasada 045, Price: 44.10; Name: swisspor fasada 042, Price: 47.10; Name: swisspor fasada 040, Price: 50.10; Name: termex fasada 042, Price: 49.80; Name: termex fasada 038, Price: 57.90; Name: knauf fasada 042, Price: 48.90; Name: knauf fasada 040, Price: 51.90; Name: termex fasada 045, Price: 46.20; Name: termex fasada 040, Price: 56.10; Name: swisspor fasada 032, Price: 51.60; Name: termex fasada 032, Price: 62.70; Name: knauf fasada 032, Price: 62.70; Name: izoterm fasada EPS70 040, Price: 48.60; Name: izoterm fasada EPS70 038, Price: 50.10; Name: izoterm EPS80, Price: 53.10; Name: izoterm EPS100 038, Price: 56.70; Name: izoterm EPS200, Price: 85.50; Name: polstyr fasada EPS70 040, Price: 51.30; Name: polstyr fasada EPS70 038, Price: 51.60; Name: polstyr EPS60, Price: 51.90; Name: polstyr EPS80, Price: 54.90; Name: polstyr EPS100 038, Price: 61.20; Name: polstyr EPS200, Price: 90.60; Name: polstyr wodoodporny EPS120, Price: 70.50; Name: genderka fasada 045, Price: 42.00; Name: genderka fasada 042, Price: 45.60; Name: genderka fasada 040, Price: 49.50; Name: genderka fasada 038, Price: 51.00; Name: genderka fasada EPS70 038, Price: 54.60; Name: genderka fasada 032, Price: 57.90; Name: genderka EPS80, Price: 54.60; Name: genderka EPS80 031, Price: 72.30; Name: genderka EPS100 031, Price: 84.90; Name: genderka EPS150, Price: 87.00; Name: genderka EPS200, Price: 96.00; Name: genderka wodoodporny EPS100, Price: 72.90; Name: genderka wodoodporny EPS200, Price: 78.00; Name: domstyr fasada 044, Price: 42.60; Name: domstyr fasada 042, Price: 43.50; Name: domstyr fasada 040, Price: 46.80; Name: domstyr fasada 038, Price: 48.90; Name: domstyr fasada EPS70 040, Price: 50.70; Name: domstyr fasada 032, Price: 57.00; Name: domstyr EPS60, Price: 50.40; Name: domstyr EPS100 038, Price: 60.00; Name: domstyr EPS100 031, Price: 84.00; Name: domstyr EPS150, Price: 75.90; Name: domstyr EPS200, Price: 90.90; Name: domstyr wodoodporny EPS100, Price: 69.30; Name: domstyr wodoodporny EPS200, Price: 0.00; Name: domstyr wodoodporny EPS100 wtryskarka, Price: 72.30; Name: domstyr wodoodporny EPS200wtryskarka, Price: 0.00; Name: styropmin EPS60, Price: 58.80; Name: styropmin EPS70, Price: 60.30; Name: styropmin EPS80, Price: 62.40; Name: styropmin EPS80 031, Price: 80.10; Name: styropmin EPS100 038, Price: 67.20; Name: styropmin EPS100 031, Price: 91.50; Name: styropmin EPS150, Price: 100.80; Name: styropmin EPS200, Price: 113.10; Name: styropmin wodoodporny EPS100, Price: 88.80; Name: styropmin wodoodporny EPS100 wtryskarka, Price: 123.30; Name: izolbet fasada 044, Price: 42.90; Name: izolbet fasada 042, Price: 43.50; Name: izolbet fasada 040, Price: 47.10; Name: izolbet fasada 032, Price: 58.20; Name: izolbet EPS60, Price: 51.30; Name: izolbet EPS80, Price: 57.00; Name: izolbet EPS150, Price: 77.40; Name: izolbet EPS200, Price: 93.00; Name: izolbet wodoodporny EPS100, Price: 70.50; Name: izolbet wodoodporny EPS100 wtryskarka, Price: 90.00; Name: austrotherm fasada 042, Price: 47.40; Name: austrotherm fasada 040, Price: 51.60; Name: austrotherm EPS70, Price: 57.60; Name: austrotherm EPS80, Price: 60.00; Name: austrotherm EPS80 031, Price: 300.00; Name: austrotherm EPS100 038, Price: 66.00; Name: austrotherm wodoodporny EPS120, Price: 75.00; Name: swisspor fasada EPS70 038, Price: 50.40; Name: swisspor EPS60, Price: 51.90; Name: swisspor EPS60 031, Price: 66.30; Name: swisspor EPS80, Price: 55.50; Name: swisspor EPS80 031, Price: 76.50; Name: swisspor EPS150, Price: 94.50; Name: swisspor EPS200, Price: 109.50; Name: swisspor wodoodporny EPS100, Price: 97.50; Name: termex fasada EPS70 038, Price: 58.50; Name: termex EPS60, Price: 58.50; Name: termex EPS60 031, Price: 96.30; Name: termex EPS70, Price: 58.50; Name: termex EPS80, Price: 63.30; Name: termex EPS80 031, Price: 81.00; Name: termex EPS100 030, Price: 105.00; Name: termex EPS150, Price: 109.80; Name: termex EPS200, Price: 112.80; Name: termex wodoodporny EPS100, Price: 79.80; Name: knauf fasada EPS70 038, Price: 57.90; Name: knauf EPS70, Price: 60.00; Name: knauf EPS80, Price: 62.70; Name: knauf EPS80 031, Price: 81.90; Name: knauf EPS100 038, Price: 65.40; Name: knauf EPS200, Price: 96.00; Name: knauf wodoodporny EPS100, Price: 81.00; Name: styropianplus EPS60, Price: 49.20; Name: styropianplus EPS60 031, Price: 70.50; Name: styropianplus EPS70, Price: 53.10; Name: styropianplus EPS80, Price: 54.90; Name: styropianplus EPS80 031, Price: 68.40; Name: styropianplus EPS100 038, Price: 61.20; Name: styropianplus EPS150, Price: 87.00; Name: styropianplus EPS200, Price: 100.50; Name: styropianplus wodoodporny EPS150, Price: 89.70; Name: styropianplus wodoodporny EPS150 wtryskarka, Price: 96.00; Name: albaterm fasada 044, Price: 42.60; Name: albaterm fasada 042, Price: 43.20; Name: albaterm fasada 040, Price: 46.50; Name: albaterm fasada EPS70 040, Price: 50.10; Name: albaterm fasada 032, Price: 55.20; Name: albaterm EPS60, Price: 50.40; Name: albaterm EPS100 038, Price: 60.60; Name: albaterm EPS200, Price: 91.50; Name: albaterm wodoodporny EPS100, Price: 72.00; Name: paneltech fasada 045, Price: 42.00; Name: paneltech fasada 042, Price: 44.10; Name: paneltech fasada 040, Price: 47.10; Name: paneltech fasada 038, Price: 50.10; Name: paneltech fasada 032, Price: 57.90; Name: paneltech EPS60, Price: 51.60; Name: paneltech EPS80, Price: 56.70; Name: paneltech EPS80 031, Price: 65.40; Name: paneltech EPS100 038, Price: 60.90; Name: paneltech EPS120, Price: 72.00; Name: paneltech EPS150, Price: 78.00; Name: paneltech EPS200, Price: 91.50; Name: paneltech wodoodporny EPS120, Price: 78.00; Name: paneltech wodoodporny EPS150, Price: 87.00; Name: paneltech wodoodporny EPS200, Price: 97.50; Name: arsanit fasada 045, Price: 42.00; Name: arsanit fasada 042, Price: 43.80; Name: arsanit fasada 040, Price: 46.80; Name: arsanit fasada 038, Price: 48.60; Name: arsanit fasada EPS70 038, Price: 52.50; Name: arsanit fasada 032, Price: 57.30; Name: arsanit EPS60, Price: 50.70; Name: arsanit EPS80, Price: 55.80; Name: arsanit EPS120, Price: 72.00; Name: arsanit EPS200, Price: 76.20; Name: arsanit wodoodporny EPS100, Price: 72.90; Name: arsanit wodoodporny EPS120, Price: 81.60; Name: grastyr fasada 045, Price: 42.00; Name: grastyr fasada 042, Price: 42.90; Name: grastyr fasada 040, Price: 46.20; Name: grastyr fasada EPS70 040, Price: 50.40; Name: grastyr fasada 032, Price: 57.30; Name: grastyr EPS80, Price: 56.10; Name: grastyr EPS100 038, Price: 60.00; Name: grastyr EPS200, Price: 91.50; Name: grastyr wodoodporny EPS100, Price: 66.30; Name: inthermo fasada 045, Price: 42.30; Name: inthermo fasada 042, Price: 43.50; Name: inthermo fasada 040, Price: 46.50; Name: inthermo fasada EPS70 040, Price: 50.40; Name: inthermo fasada EPS70 038, Price: 52.80; Name: inthermo fasada 032, Price: 57.00; Name: inthermo EPS80, Price: 55.80; Name: inthermo EPS100 038, Price: 59.70; Name: inthermo EPS200, Price: 93.00; Name: inthermo wodoodporny EPS100, Price: 69.00; Name: krasbud fasada 045, Price: 42.30; Name: krasbud fasada 042, Price: 42.90; Name: krasbud fasada 040, Price: 46.50; Name: krasbud fasada EPS70 040, Price: 50.40; Name: krasbud fasada EPS70 038, Price: 52.50; Name: krasbud EPS60, Price: 50.70; Name: krasbud EPS70, Price: 52.50; Name: krasbud EPS80, Price: 56.10; Name: krasbud EPS100 038, Price: 60.00; Name: krasbud EPS200, Price: 90.30; Name: krasbud wodoodporny EPS100, Price: 72.90; Name: neotherm fasada 045, Price: 38.70; Name: neotherm fasada 042, Price: 41.70; Name: neotherm fasada 040, Price: 45.00; Name: neotherm fasada EPS70 040, Price: 48.00; Name: neotherm fasada EPS70 038, Price: 49.50; Name: neotherm fasada 032, Price: 49.80; Name: neotherm EPS60, Price: 46.50; Name: neotherm EPS70, Price: 49.50; Name: neotherm EPS80, Price: 52.50; Name: neotherm EPS100 038, Price: 54.00; Name: neotherm EPS150, Price: 85.50; Name: neotherm EPS200, Price: 91.50; Name: neotherm wodoodporny EPS100, Price: 64.50; Name: neotherm wodoodporny EPS200, Price: 99.00; Name: sonarol fasada 044, Price: 36.00; Name: sonarol fasada 042, Price: 42.00; Name: sonarol fasada 040, Price: 48.00; Name: sonarol fasada EPS70 040, Price: 54.00; Name: sonarol fasada EPS70 038, Price: 57.00; Name: sonarol fasada 031, Price: 69.00; Name: sonarol EPS60, Price: 52.20; Name: sonarol EPS60 031, Price: 73.50; Name: sonarol EPS80, Price: 64.20; Name: sonarol EPS100 038, Price: 72.30; Name: sonarol EPS200, Price: 108.00; Name: sonarol wodoodporny EPS100, Price: 91.20; Name: sonarol wodoodporny EPS150, Price: 102.60; Name: styrhop fasada 044, Price: 42.00; Name: styrhop fasada 040, Price: 45.90; Name: styrhop EPS60, Price: 49.80; Name: styrhop EPS80, Price: 55.50; Name: styrhop EPS100 038, Price: 59.40; Name: styrhop wodoodporny EPS120, Price: 96.00; Name: styrmann fasada 045, Price: 42.30; Name: styrmann fasada EPS70 040, Price: 58.80; Name: styrmann fasada EPS70 038, Price: 61.50; Name: styrmann EPS80, Price: 66.00; Name: styrmann EPS100 038, Price: 73.20; Name: styrmann EPS200, Price: 108.00; Name: styrmann wodoodporny EPS100, Price: 87.30; Name: styropak fasada EPS70 038, Price: 59.40; Name: styropak fasada 032, Price: 63.90; Name: styropak EPS60, Price: 57.30; Name: styropak EPS80, Price: 63.60; Name: styropak EPS80 031, Price: 70.20; Name: styropak EPS100 038, Price: 63.60; Name: styropak EPS150, Price: 87.00; Name: styropak EPS200, Price: 99.90; Name: styropak wodoodporny EPS100, Price: 84.00; Name: tyron fasada 042, Price: 43.80; Name: tyron fasada 040, Price: 47.40; Name: tyron fasada 032, Price: 58.20; Name: tyron EPS60, Price: 51.30; Name: tyron EPS70, Price: 53.70; Name: tyron EPS80, Price: 57.60; Name: tyron EPS100 038, Price: 60.90; Name: tyron EPS120, Price: 132.00; Name: tyron EPS150, Price: 77.70; Name: tyron EPS200, Price: 92.10; Name: tyron wodoodporny EPS100, Price: 70.50; Name: tyron wodoodporny EPS120, Price: 141.00; Name: styropianex fasada 045, Price: 35.70; Name: styropianex fasada 042, Price: 41.70; Name: styropianex fasada 040, Price: 47.70; Name: styropianex fasada EPS70 038, Price: 59.10; Name: styropianex fasada 032, Price: 63.00; Name: styropianex EPS80, Price: 65.70; Name: styropianex EPS100 038, Price: 72.00; Name: styropianex EPS200, Price: 107.70; Name: styropianex wodoodporny EPS100, Price: 93.00; Name: NTB fasada 042, Price: 42.00; Name: NTB fasada 040, Price: 48.00; Name: NTB fasada 038, Price: 54.00; Name: NTB fasada EPS70 040, Price: 54.00; Name: NTB fasada 032, Price: 63.00; Name: NTB EPS60, Price: 52.50; Name: NTB EPS100 038, Price: 61.50; Name: NTB EPS200, Price: 104.70; Name: NTB wodoodporny EPS100, Price: 101.70; Name: NTB wodoodporny EPS150, Price: 116.70; Name: eurotermika fasada 045, Price: 42.60; Name: eurotermika fasada 042, Price: 43.20; Name: eurotermika fasada 040, Price: 45.90; Name: eurotermika EPS60, Price: 50.10; Name: eurotermika EPS80, Price: 55.80; Name: eurotermika EPS100 038, Price: 60.00; Name: eurotermika EPS150, Price: 75.90; Name: eurotermika wodoodporny EPS150, Price: 83.40; Name: eurostyr fasada 044, Price: 45.60; Name: eurostyr fasada 042, Price: 47.40; Name: eurostyr fasada 040, Price: 54.30; Name: eurostyr fasada EPS70 038, Price: 57.60; Name: eurostyr EPS60, Price: 57.90; Name: eurostyr wodoodporny EPS100, Price: 80.40; Name: genderka fasada 033, Price: 55.50; Name: genderka fasada 031, Price: 61.50; Name: styropmin fasada 033, Price: 61.20; Name: styropmin fasada 031, Price: 69.60; Name: styropmin fasada 030, Price: 81.00; Name: izolbet fasada 033, Price: 56.10; Name: austrotherm fasada 033, Price: 58.50; Name: austrotherm fasada 031, Price: 66.90; Name: swisspor fasada 031, Price: 62.40; Name: termex fasada 033, Price: 60.30; Name: termex fasada 031, Price: 67.80; Name: genderka akustyczny, Price: 42.60; Name: domstyr fasada 033, Price: 55.50; Name: domstyr fasada 031, Price: 62.10; Name: domstyr akustyczny, Price: 47.70; Name: styropmin akustyczny, Price: 44.10; Name: austrotherm akustyczny, Price: 49.50; Name: swisspor akustyczny, Price: 50.40; Name: knauf fasada 031, Price: 67.80; Name: albaterm fasada 031, Price: 61.80; Name: paneltech fasada 033, Price: 55.80; Name: arsanit fasada 033, Price: 55.80; Name: arsanit fasada 031, Price: 62.70; Name: arsanit akustyczny, Price: 47.10; Name: krasbud fasada 031, Price: 62.70; Name: neotherm fasada 033, Price: 47.40; Name: neotherm fasada 031, Price: 52.20; Name: neotherm akustyczny, Price: 37.50; Name: styrhop fasada 031, Price: 61.80; Name: tyron fasada 033, Price: 56.70; Name: tyron fasada 031, Price: 63.30; Name: NTB fasada 031, Price: 71.70; Name: eurotermika fasada 033, Price: 55.50; Name: eurotermika fasada 031, Price: 62.10; Name: eurostyr fasada 033, Price: 62.10; Name: eurostyr fasada 031, Price: 67.50; Name: lubau fasada 045, Price: 44.10; Name: lubau fasada 042, Price: 47.10; Name: lubau fasada 040, Price: 51.60; Name: lubau fasada 033, Price: 57.60; Name: lubau fasada 032, Price: 60.60; Name: lubau fasada 031, Price: 62.70; Name: lubau EPS60, Price: 50.70; Name: lubau EPS70, Price: 53.40; Name: lubau EPS80, Price: 57.30; Name: lubau EPS100 037, Price: 61.20; Name: lubau EPS150, Price: 58.20; Name: lubau EPS200, Price: 96.90; Name: sonarol fasada 033, Price: 63.30; Name: knauf fasada 031 ETIXX, Price: 90.00; Name: knauf wodoodporny EPS100 grafit, Price: 99.00; Name: lubau fasada EPS70 038, Price: 54.30; Name: albaterm EPS80, Price: 55.80; Name: izolbet fasada EPS70 040, Price: 0.00; Name: izoterm EPS100 036, Price: 59.40; Name: swisspor EPS100 036, Price: 61.20; Name: swisspor EPS100 030, Price: 86.10; Name: genderka EPS100 036, Price: 62.10; Name: austrotherm EPS150, Price: 90.00; Name: styromap fasada 042, Price: 42.60; Name: styromap fasada 040, Price: 47.40; Name: styromap fasada EPS70 040, Price: 51.00; Name: styromap fasada EPS70 038, Price: 54.30; Name: styromap fasada 032, Price: 63.30; Name: styromap fasada 031, Price: 70.50; Name: styromap EPS70, Price: 51.00; Name: neotherm EPS100 036, Price: 56.40; Name: styrhop fasada 032, Price: 56.70; Name: eurostyr EPS80, Price: 60.90; Name: eurostyr EPS80 031, Price: 74.40; Name: eurostyr EPS100 030, Price: 85.50; Name: eurostyr EPS150, Price: 99.00; Name: eurostyr EPS200, Price: 108.00; Name: krasbud fasada 033, Price: 55.80; Name: krasbud EPS70 031, Price: 66.90; Name: krasbud EPS100 036, Price: 63.60; Name: krasbud EPS100 031, Price: 84.00; Name: arsanit EPS70 032, Price: 63.90; Name: arsanit EPS100 035, Price: 65.70; Name: izolbet fasada 038, Price: 48.60; Name: izolbet EPS70 031, Price: 66.00; Name: izolbet EPS100 037, Price: 62.40; Name: izolbet wodoodporny EPS150, Price: 101.40; Name: izolbet wodoodporny EPS150 wtryskarka, Price: 120.00; Name: yetico fasada 044, Price: 51.90; Name: yetico fasada 042, Price: 54.60; Name: yetico fasada 040, Price: 60.60; Name: yetico fasada 033, Price: 65.40; Name: yetico fasada 032, Price: 68.40; Name: yetico fasada 031, Price: 75.60; Name: yetico EPS60, Price: 64.50; Name: yetico EPS60 031, Price: 81.00; Name: yetico EPS70, Price: 66.90; Name: yetico EPS80, Price: 72.90; Name: yetico EPS100 036, Price: 80.40; Name: yetico EPS100 031, Price: 100.50; Name: yetico EPS200, Price: 135.00; Name: yetico akustyczny, Price: 51.90; Name: yetico wodoodporny EPS100, Price: 93.90; Name: yetico wodoodporny EPS150, Price: 117.00; Name: yetico wodoodporny EPS200, Price: 135.00; Name: yetico wodoodporny EPS100 031, Price: 112.20; Name: yetico wodoodporny EPS80 031, Price: 102.00; Name: styropianplus wodoodporny EPS100, Price: 72.00; Name: enerpor fasada 045, Price: 54.00; Name: enerpor fasada 040, Price: 61.50; Name: enerpor fasada 038, Price: 51.00; Name: enerpor fasada EPS70 038, Price: 66.00; Name: enerpor fasada 033, Price: 69.00; Name: enerpor fasada 031, Price: 76.50; Name: enerpor EPS60, Price: 63.00; Name: enerpor EPS70, Price: 66.00; Name: enerpor EPS70 031, Price: 81.00; Name: enerpor EPS80, Price: 70.50; Name: enerpor EPS100 038, Price: 79.50; Name: enerpor EPS100 036, Price: 82.50; Name: enerpor EPS100 030, Price: 105.00; Name: enerpor EPS200, Price: 126.00; Name: enerpor akustyczny, Price: 54.00; Name: enerpor wodoodporny EPS100, Price: 90.00; Name: enerpor wodoodporny EPS100 031, Price: 105.00; Name: besser fasada 042, Price: 42.90; Name: besser fasada 040, Price: 46.20; Name: besser fasada 038, Price: 48.90; Name: besser fasada 033, Price: 55.20; Name: besser fasada 031, Price: 61.80; Name: besser EPS60, Price: 49.80; Name: besser EPS70, Price: 53.10; Name: besser EPS70 031, Price: 66.00; Name: besser EPS80, Price: 55.80; Name: besser EPS100 036, Price: 62.40; Name: besser EPS100 030, Price: 70.50; Name: besser EPS200, Price: 90.60; Name: besser wodoodporny EPS100, Price: 66.00; Name: FWS fasada 042, Price: 39.30; Name: FWS fasada 040, Price: 42.30; Name: FWS fasada EPS70 040, Price: 45.90; Name: FWS fasada EPS70 038, Price: 46.80; Name: FWS fasada 033, Price: 49.20; Name: FWS fasada 031, Price: 58.50; Name: FWS fasada 030, Price: 69.30; Name: FWS EPS60, Price: 46.80; Name: FWS EPS70 031, Price: 58.50; Name: FWS EPS80, Price: 50.10; Name: FWS EPS80 031, Price: 69.30; Name: FWS EPS100 038, Price: 57.00; Name: FWS EPS200, Price: 94.80; Name: FWS wodoodporny EPS100, Price: 67.80; Name: justyr fasada 042, Price: 43.50; Name: justyr fasada 040, Price: 47.10; Name: justyr fasada 038, Price: 45.36; Name: justyr fasada EPS70 040, Price: 50.70; Name: justyr fasada EPS70 038, Price: 52.80; Name: justyr fasada 033, Price: 55.80; Name: justyr fasada 032, Price: 57.90; Name: justyr fasada 031, Price: 62.70; Name: justyr EPS60, Price: 51.30; Name: justyr EPS70, Price: 53.70; Name: justyr EPS80, Price: 54.90; Name: justyr EPS80 031, Price: 0.00; Name: justyr EPS100 038, Price: 61.20; Name: justyr EPS100 036, Price: 64.50; Name: justyr EPS150, Price: 78.90; Name: justyr EPS200, Price: 93.00; Name: justyr wodoodporny EPS100, Price: 71.40; Name: eurostyropian fasada 044, Price: 48.00; Name: eurostyropian fasada 042, Price: 49.50; Name: eurostyropian fasada 040, Price: 52.50; Name: eurostyropian fasada 038, Price: 57.00; Name: eurostyropian fasada EPS70 040, Price: 60.00; Name: eurostyropian fasada 033, Price: 59.40; Name: eurostyropian fasada 032, Price: 64.50; Name: eurostyropian fasada 031, Price: 69.90; Name: eurostyropian EPS60, Price: 54.00; Name: eurostyropian EPS70, Price: 57.30; Name: eurostyropian EPS80, Price: 61.50; Name: eurostyropian EPS100 038, Price: 68.70; Name: eurostyropian wodoodporny EPS100, Price: 79.50; Name: ekobud fasada 045, Price: 49.50; Name: ekobud fasada 042, Price: 49.50; Name: ekobud fasada 040, Price: 55.50; Name: ekobud fasada EPS70 040, Price: 66.00; Name: ekobud fasada 033, Price: 60.00; Name: ekobud fasada 032, Price: 64.50; Name: ekobud EPS60, Price: 54.00; Name: ekobud EPS80, Price: 61.50; Name: ekobud EPS100 036, Price: 74.70; Name: ekobud EPS150, Price: 96.00; Name: ekobud EPS200, Price: 109.50; Name: ekobud wodoodporny EPS100, Price: 99.00; Name: ekobud wodoodporny EPS150, Price: 30102.00; Name: termex fasada EPS70 040, Price: 58.50; Name: termex fasada 030, Price: 76.80; Name: termex EPS70 031, Price: 96.30; Name: termex EPS100 038, Price: 79.80; Name: termex EPS100 036, Price: 88.80; Name: termex EPS100 035, Price: 81.00; Name: termex EPS100 031, Price: 96.30; Name: termex EPS120, Price: 90.00; Name: termex akustyczny, Price: 48.30; Name: termex wodoodporny EPS120, Price: 90.00; Name: termex wodoodporny EPS150, Price: 108.00; Name: termex wodoodporny EPS200, Price: 120.30; Name: krolczyk fasada 042, Price: 52.80; Name: krolczyk fasada 040, Price: 56.40; Name: krolczyk fasada 033, Price: 65.40; Name: krolczyk fasada 032, Price: 68.40; Name: krolczyk fasada 031, Price: 70.50; Name: krolczyk EPS80, Price: 65.40; Name: krolczyk EPS100 038, Price: 73.50; Name: krolczyk EPS100 036, Price: 75.30; Name: krolczyk EPS100 031, Price: 94.50; Name: krolczyk EPS200, Price: 114.30; Name: krolczyk wodoodporny EPS120, Price: 100.50; Name: piotrowski fasada 040, Price: 51.00; Name: piotrowski fasada EPS70 040, Price: 51.00; Name: piotrowski EPS80, Price: 56.10; Name: piotrowski EPS100 038, Price: 58.50; Name: thermica fasada 045, Price: 46.20; Name: thermica fasada 044, Price: 49.80; Name: thermica fasada 042, Price: 49.80; Name: thermica fasada 040, Price: 56.10; Name: thermica fasada 038, Price: 57.90; Name: thermica fasada EPS70 040, Price: 63.30; Name: thermica fasada EPS70 038, Price: 63.30; Name: thermica fasada 033, Price: 60.30; Name: thermica fasada 032, Price: 62.70; Name: thermica fasada 031, Price: 67.80; Name: thermica fasada 030, Price: 78.00; Name: thermica EPS60, Price: 58.50; Name: thermica EPS60 031, Price: 76.50; Name: thermica EPS70, Price: 58.50; Name: thermica EPS70 031, Price: 96.30; Name: thermica EPS80, Price: 63.30; Name: thermica EPS80 031, Price: 96.30; Name: thermica EPS100 038, Price: 72.00; Name: thermica EPS100 036, Price: 72.00; Name: thermica EPS100 035, Price: 96.30; Name: thermica EPS100 031, Price: 96.30; Name: thermica EPS100 030, Price: 108.00; Name: thermica EPS120, Price: 11.10; Name: thermica EPS150, Price: 1912.80; Name: thermica EPS200, Price: 112.80; Name: thermica akustyczny, Price: 48.30; Name: thermica wodoodporny EPS100, Price: 79.80; Name: thermica wodoodporny EPS120, Price: 120.30; Name: thermica wodoodporny EPS150, Price: 90.00; Name: thermica wodoodporny EPS200, Price: 150.30; Name: thermica wodoodporny EPS100 wtryskarka, Price: 79.80; Name: thermica wodoodporny EPS120 wtryskarka, Price: 120.30; Name: thermica wodoodporny EPS150 wtryskarka, Price: 120.30; Name: thermica wodoodporny EPS200wtryskarka, Price: 120.30; Name: thermica wodoodporny EPS100 031, Price: 102.30; Name: styropianplus EPS100 031, Price: 82.50; Name: styropianplus EPS100 035, Price: 66.00; Name: domstyr EPS80, Price: 56.10; Name: eurostyr EPS100 036, Price: 60.00; Name: polstyr EPS70, Price: 52.50; Name: polstyr EPS100 036, Price: 61.80; Name: polstyr EPS100 030, Price: 77.40; Name: polstyr EPS150, Price: 88.20; Name: albaterm EPS100 036 1cm, Price: 0.00; Name: austrotherm fasada EPS70 038, Price: 57.00; Name: krolczyk fasada EPS70 040, Price: 60.60; Name: krolczyk fasada EPS70 038, Price: 63.30; Name: styrhop fasada EPS70 040, Price: 30.30; Name: yetico fasada EPS70 038, Price: 66.90; Name: tyron fasada EPS70 038, Price: 52.80; Name: styropianplus fasada EPS70 039, Price: 52.20; Name: styropmin fasada EPS70 038, Price: 67.50"
          albaterm - dobry jakościowo styropian w dobrej cenie
  arsanit - standardowy styropian w przeciętnej cenie
  austrotherm - bardzo dobry styropian w wysokiej cenie
  besser - bardzo słaby styropian w niskie cenie
  domstyr- standardowy styropian w przeciętnej cenie
  ekobud-zakrzewo- według własnego uznania
  enerpor - słaby styropian przeważnie w niskie cenie
  eurostyr- standardowy styropian przeważnie w dobrej cenie
  eurostyropian- standardowy styropian przeważnie w przeciętnej cenie
  eurotermika - standardowy styropian przeważnie w dobrej cenie
  FWS -  umiarkowana jakość bardzo dobra cena
  genderka - standardowy styropian przeważnie w słabej cenie
  grastyr - standardowy styropian przeważnie w przeciętnej cenie
  inthermo - bardzo słaby styropian w niskie cenie
  izoline - dobry jakościowo styropian w przeciętnej cenie
  izolbet - standardowy styropian w zawyżonej cenie
  izoterm  -  umiarkowana jakość bardzo dobra cena
  justyr - dobry jakościowo styropian w dobrej cenie
  knauf - dobry styropian w bardzo zawyżonej cenie zdecydowanie lepiej kupić styropmin lub austrotherm
  krasbud - standardowy styropian przeważnie w przeciętnej cenie
  krolczyk- według własnego uznania
  neotherm- standardowy styropian przeważnie w przeciętnej cenie
  NTB- standardowy styropian przeważnie w przeciętnej cenie
  paneltech - standardowy styropian przeważnie w dobrej cenie
  piotrowski -  umiarkowana jakość bardzo dobra cena
  polstyr - standardowy styropian przeważnie w słabej cenie
  sonarol - standardowy styropian przeważnie w dobrej cenie
  styrhop -  umiarkowana jakość bardzo dobra cena
  styrmann - standardowy styropian przeważnie w przeciętnej cenie
  styropak - standardowy styropian przeważnie w przeciętnej cenie
  styropoz - standardowy styropian przeważnie w dobrej cenie
  styromap - standardowy styropian przeważnie w dobrej cenie
  styropianex - standardowy styropian przeważnie w dobrej cenie
  styropian plus - dobry jakościowo styropian w dobrej cenie
  styropmin - bardzo dobry styropian w wysokiej cenie
  swisspor - dobry jakościowo styropian przeważnie w przeciętnej cenie
  termex - standardowy styropian przeważnie w dobrej cenie
  termoorganika - dobry jakościowo w mocno zawyżonej cenie zdecydowanie lepiej wziąść styropmin austrotherm lub swisspor
  thermica - standardowy styropian przeważnie w przeciętnej cenie
  yetico - standardowy styropian przeważnie w przeciętnej cenie
  tyron - standardowy styropian przeważnie w przeciętnej cenie

        This are styrofoams witch we have in offer. You are part of my laravel program witch sugest customer wtch styrofoam to buy For example: Response: { "message": "Dzień sobry, znalazłem takie produkty na podłogę dla ciebie!", "products": [{name: "name",descripion:"descriptipn"}] }"
        You have to provide response only in json and in this format otherwise you will break system! Do not add any letters then json do not add also marking that this is json

        iAlways provide all of nescesary product witch you think wll be ok make sure that whole name wich you provide is full and mach one of proivided to you
        DO NOT USE PRODUCTS WITCH ARE NOT IN PASTED TO YOU
        I HAVE PASTED YOU ALSO PRICES OF ALL PRODUCTS SO SUGEST BY IT
        DO NOT HALUCINATE

        na elewacje nie polecaj eps 70 chyba że użytkownik potrzevuje tego parametru


        styropian z serii eps 100 to styropian posadzkowy
        styropian 031 to grafit
        fasada to styropian elewacyjny

        <example>
            userInput: Szukam styropianu grafitowego na elewacje
            { "message": "Dzień sobry, znalazłem takie produkty grafitowe elewacje dla ciebie!","products": [{name: "styropianplus fasada 031",descripion:""}, {name: "neotherm fasada 031",descripion:""}, {name: "izoterm fasada 031",descripion:""}, {name: "swisspor fasada 031",descripion:""}] }"
        </example>

        <example>
            userInput: Szukam najtańszego styropianu na elewacje
            { "message": "Dzień sobry, znalazłem takie produkty elewacje dla ciebie!","products": [{name: "postyr fasada 045",descripion:""}, {name: "arsanit fasada 045",descripion:""}, {name: "izoterm fasada 045",descripion:""}, {name: "genderka fasada 045",descripion:""}] }"
        </example>

        <example>
            userInput: Szukam styropianu na posadzke
            { "message": "Dzień sobry, znalazłem takie produkty na posadzke dla ciebie!","products": [{name: "izoterm eps100 038",descripion:""}, {name: "izoterm eps100 036",descripion:""}, {name: "neotherm eps100 036", description: ""}, {name: "syropianplus eps100 036",descripion:""}] }"
        </example>

                <example>
            userInput: Szukam styropianu grafitowego na elewacje
            { "message": "Dzień sobry, znalazłem takie produkty grafitowe elewacje dla ciebie!","products": [{name: "styropianplus fasada 031",descripion:""}, {name: "neotherm fasada 031",descripion:""}, {name: "izoterm fasada 031",descripion:""}, {name: "swisspor fasada 031",descripion:""}] }"
        </example>



        Polecaj najbardziej neotherma i izoterma i justyra

        więc jeśli użytkownik szuka najtańszego styropianu na elewacje poleć mu  fasada 038 zamiast eps 70 083
',
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

    $response = json_decode(json_decode($response)->content[0]->text);

    foreach ($response->products as &$product) {
        $p = \App\Entities\Product::where('name', $product->name)->first();
        if ($p) {
            $product->name = $p;

            $product->name = $product->name->stock->product()
                ->select('product_prices.*', 'product_packings.*', 'products.*')
                ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->with('media')
                ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
                ->orderBy('priority')
                ->orderBy('name')
                ->first();

            $product->name->load('stock');
            $product->name->load('opinions');
            $product->name->load('price');

            $product->name->similarProducts = $product->name->category->products()->whereHas('children')->with('price')->get();

            $product->name->meanOpinion = $product->name->opinions->avg('rating') ?? 0;
        }
    }

    return $response;
});

Route::post('auctions/save', function (Request $request) {
    $products = [];
    foreach ($request->auctionData as $product) {
        $productR = \App\Entities\Product::where('name', 'like', '%' . $product['styrofoamType'] . '%')->where('name', 'like', '%' . $product['thickness'] . '%')->whereDoesntHave('children')->first();

        if ($productR) {
            $productR = $productR->toArray();
        }

        $productR['amount'] = $product['quantity'];
        $products[] = $productR;
    }


    $customer = Customer::where('login', $request->userInfo['email'])->first();

    if (!$customer) {
        $customer = Customer::create([
            'login' => $request->userInfo['email'],
            'status' => 'ACTIVE',
            'password' => Hash::make($request->userInfo['phone']),
        ]);
    }

    DB::beginTransaction();

    $orderBuilder = (new OrderBuilder())
        ->setPackageGenerator(new BackPackPackageDivider())
        ->setPriceCalculator(new OrderPriceCalculator())
        ->setProductService(app(ProductService::class));

    $orderBuilder
        ->setTotalTransportSumCalculator(new TransportSumCalculator())
        ->setUserSelector(new GetCustomerForNewOrder());

    $builderData = $orderBuilder->newStore(['phone' => $request->userInfo['phone']], $customer);
    $order = Order::find($builderData['id']);
    $orderBuilder->assignItemsToOrder($order, $products);
    $orderBuilder->updateOrderAddress($order, [], 'DELIVERY_ADDRESS', $request->userInfo['phone'], 'order', $request->userInfo['email'],);

    DB::commit();

    $order = Order::find($builderData['id']);
    $order->firm_source_id = FirmSource::byFirmAndSource(config('orders.firm_id'), 2)->value('id');
    $order->packages_values = json_encode($data['packages']  ?? null);
    $order->save();


    $orderAddresses = $order->addresses()->get();

    if (empty($data['cart_token'])) {
        foreach ($orderAddresses as $orderAddress) {
            OrderAddressesService::updateOrderAddressFromCustomer($orderAddress, $customer);
            $orderAddress->postal_code = $request->userInfo['zipCode'];
            $orderAddress->save();
        }
    }

    $order->update(['status_id' => 3]);

    dispatch_now(new OrderStatusChangedNotificationJob($order->id));

    $order->orderOffer()->firstOrNew([
        'order_id' => $order->id,
        'message' => Status::find(18)->message,
    ]);

    Mailer::create()
        ->to($customer->login)
        ->send(new NewStyroOfferMade(
            $order,
        ));

    if ($order->created_at->format('Y-m-d H:i:s') === $order->updated_at->format('Y-m-d H:i:s')) {
        dispatch(new DispatchLabelEventByNameJob($order, "new-order-created"));
    }

    $order->chat->chatUsers->first()->update(['customer_id' => $customer->id]);

    $order->additional_service_cost = 50;
    $order->customer_name = $request->userInfo['email'];
    $order->save();

    $delay = now()->addHours(2);

    dispatch(new ReferFriendNotificationJob($order))->delay($delay);

    $arr = [];
    AddLabelService::addLabels($order, [271], $arr, []);

    $auction = app(ChatAuctionsService::class)->createAuction(
        CreateChatAuctionDTO::fromRequest($order->chat, [
            'end_of_auction' => now()->addDays(3)->toString(),
            'price' => 50,
            'quality' => 50,
            'notes' => '',
        ])
    );

    Mailer::create()
        ->to($order->customer->login)
        ->send(new AuctionCreationConfirmation(
            $auction
        ));

    app(ChatAuctionsService::class)->confirmAuction($auction);

    return response()->json($builderData + [
        'newAccount' => $customer->created_at->format('Y-m-d H:i:s') === $customer->updated_at->format('Y-m-d H:i:s'),
        'access_token' => $customer->createToken('Api code')->accessToken,
        'expires_in' => CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR * CarbonInterface::SECONDS_PER_MINUTE
    ]);
});

