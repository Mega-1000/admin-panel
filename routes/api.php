<?php

use App\Entities\Category;
use App\Entities\ShippingPayInReport;
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
use Illuminate\Http\Request;
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

Route::post('styro-help', function (Request $request) {
    $apiUrl = "https://api.anthropic.com/v1/messages";
    $apiKey = "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";
    $anthropicVersion = "2023-06-01";


    $prompt = [[
        "role" => "user",
        "content" => '
Name: styropianplus fasada 044, Price: 42.90; Name: styropianplus fasada 042, Price: 45.60; Name: styropianplus fasada 040, Price: 48.30; Name: styropianplus fasada 033, Price: 53.40; Name: styropianplus fasada 032, Price: 58.50; Name: styropianplus fasada 031, Price: 61.50; Name: styropianplus akustyczny, Price: 44.40; Name: styrmann fasada 042, Price: 48.00; Name: styrmann fasada 040, Price: 54.00; Name: styrmann fasada 033, Price: 66.00; Name: styrmann fasada 032, Price: 71.10; Name: styrmann fasada 031, Price: 75.00; Name: styropmin fasada 042, Price: 50.40; Name: styropmin fasada 040, Price: 53.40; Name: styropak fasada 045, Price: 34.50; Name: styropak fasada 042, Price: 40.80; Name: styropak fasada 040, Price: 46.80; Name: styropak fasada 033, Price: 59.70; Name: styropak fasada 031, Price: 66.60; Name: styropak akustyczny, Price: 46.50; Name: izoterm fasada 045, Price: 42.00; Name: izoterm fasada 042, Price: 42.60; Name: izoterm fasada 040, Price: 45.60; Name: izoterm fasada 032, Price: 54.30; Name: polstyr fasada 045, Price: 41.10; Name: polstyr fasada 044, Price: 42.30; Name: polstyr fasada 042, Price: 43.80; Name: polstyr fasada 040, Price: 48.00; Name: polstyr fasada 038, Price: 51.60; Name: polstyr fasada 033, Price: 51.90; Name: polstyr fasada 032, Price: 52.20; Name: polstyr fasada 031, Price: 58.50; Name: polstyr akustyczny, Price: 42.00; Name: swisspor fasada 045, Price: 44.10; Name: swisspor fasada 042, Price: 47.10; Name: swisspor fasada 040, Price: 50.10; Name: termex fasada 042, Price: 49.80; Name: termex fasada 038, Price: 57.90; Name: knauf fasada 042, Price: 48.90; Name: knauf fasada 040, Price: 51.90; Name: termex fasada 045, Price: 46.20; Name: termex fasada 040, Price: 56.10; Name: swisspor fasada 032, Price: 51.60; Name: termex fasada 032, Price: 62.70; Name: knauf fasada 032, Price: 62.70; Name: izoterm fasada EPS70 040, Price: 48.60; Name: izoterm fasada EPS70 038, Price: 50.10; Name: izoterm EPS80, Price: 53.10; Name: izoterm EPS100 038, Price: 56.70; Name: izoterm EPS200, Price: 85.50; Name: polstyr fasada EPS70 040, Price: 51.30; Name: polstyr fasada EPS70 038, Price: 51.60; Name: polstyr EPS60, Price: 51.90; Name: polstyr EPS80, Price: 54.90; Name: polstyr EPS100 038, Price: 61.20; Name: polstyr EPS200, Price: 90.60; Name: polstyr wodoodporny EPS120, Price: 70.50; Name: genderka fasada 045, Price: 42.00; Name: genderka fasada 042, Price: 45.60; Name: genderka fasada 040, Price: 49.50; Name: genderka fasada 038, Price: 51.00; Name: genderka fasada EPS70 038, Price: 54.60; Name: genderka fasada 032, Price: 57.90; Name: genderka EPS80, Price: 54.60; Name: genderka EPS80 031, Price: 72.30; Name: genderka EPS100 031, Price: 84.90; Name: genderka EPS150, Price: 87.00; Name: genderka EPS200, Price: 96.00; Name: genderka wodoodporny EPS100, Price: 72.90; Name: genderka wodoodporny EPS200, Price: 78.00; Name: domstyr fasada 044, Price: 42.60; Name: domstyr fasada 042, Price: 43.50; Name: domstyr fasada 040, Price: 46.80; Name: domstyr fasada 038, Price: 48.90; Name: domstyr fasada EPS70 040, Price: 50.70; Name: domstyr fasada 032, Price: 57.00; Name: domstyr EPS60, Price: 50.40; Name: domstyr EPS100 038, Price: 60.00; Name: domstyr EPS100 031, Price: 84.00; Name: domstyr EPS150, Price: 75.90; Name: domstyr EPS200, Price: 90.90; Name: domstyr wodoodporny EPS100, Price: 69.30; Name: domstyr wodoodporny EPS200, Price: 0.00; Name: domstyr wodoodporny EPS100 wtryskarka, Price: 72.30; Name: domstyr wodoodporny EPS200wtryskarka, Price: 0.00; Name: styropmin EPS60, Price: 58.80; Name: styropmin EPS70, Price: 60.30; Name: styropmin EPS80, Price: 62.40; Name: styropmin EPS80 031, Price: 80.10; Name: styropmin EPS100 038, Price: 67.20; Name: styropmin EPS100 031, Price: 91.50; Name: styropmin EPS150, Price: 100.80; Name: styropmin EPS200, Price: 113.10; Name: styropmin wodoodporny EPS100, Price: 88.80; Name: styropmin wodoodporny EPS100 wtryskarka, Price: 123.30; Name: izolbet fasada 044, Price: 42.90; Name: izolbet fasada 042, Price: 43.50; Name: izolbet fasada 040, Price: 47.10; Name: izolbet fasada 032, Price: 58.20; Name: izolbet EPS60, Price: 51.30; Name: izolbet EPS80, Price: 57.00; Name: izolbet EPS150, Price: 77.40; Name: izolbet EPS200, Price: 93.00; Name: izolbet wodoodporny EPS100, Price: 70.50; Name: izolbet wodoodporny EPS100 wtryskarka, Price: 90.00; Name: austrotherm fasada 042, Price: 47.40; Name: austrotherm fasada 040, Price: 51.60; Name: austrotherm EPS70, Price: 57.60; Name: austrotherm EPS80, Price: 60.00; Name: austrotherm EPS80 031, Price: 300.00; Name: austrotherm EPS100 038, Price: 66.00; Name: austrotherm wodoodporny EPS120, Price: 75.00; Name: swisspor fasada EPS70 038, Price: 50.40; Name: swisspor EPS60, Price: 51.90; Name: swisspor EPS60 031, Price: 66.30; Name: swisspor EPS80, Price: 55.50; Name: swisspor EPS80 031, Price: 76.50; Name: swisspor EPS150, Price: 94.50; Name: swisspor EPS200, Price: 109.50; Name: swisspor wodoodporny EPS100, Price: 97.50; Name: termex fasada EPS70 038, Price: 58.50; Name: termex EPS60, Price: 58.50; Name: termex EPS60 031, Price: 96.30; Name: termex EPS70, Price: 58.50; Name: termex EPS80, Price: 63.30; Name: termex EPS80 031, Price: 81.00; Name: termex EPS100 030, Price: 105.00; Name: termex EPS150, Price: 109.80; Name: termex EPS200, Price: 112.80; Name: termex wodoodporny EPS100, Price: 79.80; Name: knauf fasada EPS70 038, Price: 57.90; Name: knauf EPS70, Price: 60.00; Name: knauf EPS80, Price: 62.70; Name: knauf EPS80 031, Price: 81.90; Name: knauf EPS100 038, Price: 65.40; Name: knauf EPS200, Price: 96.00; Name: knauf wodoodporny EPS100, Price: 81.00; Name: styropianplus EPS60, Price: 49.20; Name: styropianplus EPS60 031, Price: 70.50; Name: styropianplus EPS70, Price: 53.10; Name: styropianplus EPS80, Price: 54.90; Name: styropianplus EPS80 031, Price: 68.40; Name: styropianplus EPS100 038, Price: 61.20; Name: styropianplus EPS150, Price: 87.00; Name: styropianplus EPS200, Price: 100.50; Name: styropianplus wodoodporny EPS150, Price: 89.70; Name: styropianplus wodoodporny EPS150 wtryskarka, Price: 96.00; Name: albaterm fasada 044, Price: 42.60; Name: albaterm fasada 042, Price: 43.20; Name: albaterm fasada 040, Price: 46.50; Name: albaterm fasada EPS70 040, Price: 50.10; Name: albaterm fasada 032, Price: 55.20; Name: albaterm EPS60, Price: 50.40; Name: albaterm EPS100 038, Price: 60.60; Name: albaterm EPS200, Price: 91.50; Name: albaterm wodoodporny EPS100, Price: 72.00; Name: paneltech fasada 045, Price: 42.00; Name: paneltech fasada 042, Price: 44.10; Name: paneltech fasada 040, Price: 47.10; Name: paneltech fasada 038, Price: 50.10; Name: paneltech fasada 032, Price: 57.90; Name: paneltech EPS60, Price: 51.60; Name: paneltech EPS80, Price: 56.70; Name: paneltech EPS80 031, Price: 65.40; Name: paneltech EPS100 038, Price: 60.90; Name: paneltech EPS120, Price: 72.00; Name: paneltech EPS150, Price: 78.00; Name: paneltech EPS200, Price: 91.50; Name: paneltech wodoodporny EPS120, Price: 78.00; Name: paneltech wodoodporny EPS150, Price: 87.00; Name: paneltech wodoodporny EPS200, Price: 97.50; Name: arsanit fasada 045, Price: 42.00; Name: arsanit fasada 042, Price: 43.80; Name: arsanit fasada 040, Price: 46.80; Name: arsanit fasada 038, Price: 48.60; Name: arsanit fasada EPS70 038, Price: 52.50; Name: arsanit fasada 032, Price: 57.30; Name: arsanit EPS60, Price: 50.70; Name: arsanit EPS80, Price: 55.80; Name: arsanit EPS120, Price: 72.00; Name: arsanit EPS200, Price: 76.20; Name: arsanit wodoodporny EPS100, Price: 72.90; Name: arsanit wodoodporny EPS120, Price: 81.60; Name: grastyr fasada 045, Price: 42.00; Name: grastyr fasada 042, Price: 42.90; Name: grastyr fasada 040, Price: 46.20; Name: grastyr fasada EPS70 040, Price: 50.40; Name: grastyr fasada 032, Price: 57.30; Name: grastyr EPS80, Price: 56.10; Name: grastyr EPS100 038, Price: 60.00; Name: grastyr EPS200, Price: 91.50; Name: grastyr wodoodporny EPS100, Price: 66.30; Name: inthermo fasada 045, Price: 42.30; Name: inthermo fasada 042, Price: 43.50; Name: inthermo fasada 040, Price: 46.50; Name: inthermo fasada EPS70 040, Price: 50.40; Name: inthermo fasada EPS70 038, Price: 52.80; Name: inthermo fasada 032, Price: 57.00; Name: inthermo EPS80, Price: 55.80; Name: inthermo EPS100 038, Price: 59.70; Name: inthermo EPS200, Price: 93.00; Name: inthermo wodoodporny EPS100, Price: 69.00; Name: krasbud fasada 045, Price: 42.30; Name: krasbud fasada 042, Price: 42.90; Name: krasbud fasada 040, Price: 46.50; Name: krasbud fasada EPS70 040, Price: 50.40; Name: krasbud fasada EPS70 038, Price: 52.50; Name: krasbud EPS60, Price: 50.70; Name: krasbud EPS70, Price: 52.50; Name: krasbud EPS80, Price: 56.10; Name: krasbud EPS100 038, Price: 60.00; Name: krasbud EPS200, Price: 90.30; Name: krasbud wodoodporny EPS100, Price: 72.90; Name: neotherm fasada 045, Price: 38.70; Name: neotherm fasada 042, Price: 41.70; Name: neotherm fasada 040, Price: 45.00; Name: neotherm fasada EPS70 040, Price: 48.00; Name: neotherm fasada EPS70 038, Price: 49.50; Name: neotherm fasada 032, Price: 49.80; Name: neotherm EPS60, Price: 46.50; Name: neotherm EPS70, Price: 49.50; Name: neotherm EPS80, Price: 52.50; Name: neotherm EPS100 038, Price: 54.00; Name: neotherm EPS150, Price: 85.50; Name: neotherm EPS200, Price: 91.50; Name: neotherm wodoodporny EPS100, Price: 64.50; Name: neotherm wodoodporny EPS200, Price: 99.00; Name: sonarol fasada 044, Price: 36.00; Name: sonarol fasada 042, Price: 42.00; Name: sonarol fasada 040, Price: 48.00; Name: sonarol fasada EPS70 040, Price: 54.00; Name: sonarol fasada EPS70 038, Price: 57.00; Name: sonarol fasada 031, Price: 69.00; Name: sonarol EPS60, Price: 52.20; Name: sonarol EPS60 031, Price: 73.50; Name: sonarol EPS80, Price: 64.20; Name: sonarol EPS100 038, Price: 72.30; Name: sonarol EPS200, Price: 108.00; Name: sonarol wodoodporny EPS100, Price: 91.20; Name: sonarol wodoodporny EPS150, Price: 102.60; Name: styrhop fasada 044, Price: 42.00; Name: styrhop fasada 040, Price: 45.90; Name: styrhop EPS60, Price: 49.80; Name: styrhop EPS80, Price: 55.50; Name: styrhop EPS100 038, Price: 59.40; Name: styrhop wodoodporny EPS120, Price: 96.00; Name: styrmann fasada 045, Price: 42.30; Name: styrmann fasada EPS70 040, Price: 58.80; Name: styrmann fasada EPS70 038, Price: 61.50; Name: styrmann EPS80, Price: 66.00; Name: styrmann EPS100 038, Price: 73.20; Name: styrmann EPS200, Price: 108.00; Name: styrmann wodoodporny EPS100, Price: 87.30; Name: styropak fasada EPS70 038, Price: 59.40; Name: styropak fasada 032, Price: 63.90; Name: styropak EPS60, Price: 57.30; Name: styropak EPS80, Price: 63.60; Name: styropak EPS80 031, Price: 70.20; Name: styropak EPS100 038, Price: 63.60; Name: styropak EPS150, Price: 87.00; Name: styropak EPS200, Price: 99.90; Name: styropak wodoodporny EPS100, Price: 84.00; Name: tyron fasada 042, Price: 43.80; Name: tyron fasada 040, Price: 47.40; Name: tyron fasada 032, Price: 58.20; Name: tyron EPS60, Price: 51.30; Name: tyron EPS70, Price: 53.70; Name: tyron EPS80, Price: 57.60; Name: tyron EPS100 038, Price: 60.90; Name: tyron EPS120, Price: 132.00; Name: tyron EPS150, Price: 77.70; Name: tyron EPS200, Price: 92.10; Name: tyron wodoodporny EPS100, Price: 70.50; Name: tyron wodoodporny EPS120, Price: 141.00; Name: styropianex fasada 045, Price: 35.70; Name: styropianex fasada 042, Price: 41.70; Name: styropianex fasada 040, Price: 47.70; Name: styropianex fasada EPS70 038, Price: 59.10; Name: styropianex fasada 032, Price: 63.00; Name: styropianex EPS80, Price: 65.70; Name: styropianex EPS100 038, Price: 72.00; Name: styropianex EPS200, Price: 107.70; Name: styropianex wodoodporny EPS100, Price: 93.00; Name: NTB fasada 042, Price: 42.00; Name: NTB fasada 040, Price: 48.00; Name: NTB fasada 038, Price: 54.00; Name: NTB fasada EPS70 040, Price: 54.00; Name: NTB fasada 032, Price: 63.00; Name: NTB EPS60, Price: 52.50; Name: NTB EPS100 038, Price: 61.50; Name: NTB EPS200, Price: 104.70; Name: NTB wodoodporny EPS100, Price: 101.70; Name: NTB wodoodporny EPS150, Price: 116.70; Name: eurotermika fasada 045, Price: 42.60; Name: eurotermika fasada 042, Price: 43.20; Name: eurotermika fasada 040, Price: 45.90; Name: eurotermika EPS60, Price: 50.10; Name: eurotermika EPS80, Price: 55.80; Name: eurotermika EPS100 038, Price: 60.00; Name: eurotermika EPS150, Price: 75.90; Name: eurotermika wodoodporny EPS150, Price: 83.40; Name: eurostyr fasada 044, Price: 45.60; Name: eurostyr fasada 042, Price: 47.40; Name: eurostyr fasada 040, Price: 54.30; Name: eurostyr fasada EPS70 038, Price: 57.60; Name: eurostyr EPS60, Price: 57.90; Name: eurostyr wodoodporny EPS100, Price: 80.40; Name: genderka fasada 033, Price: 55.50; Name: genderka fasada 031, Price: 61.50; Name: styropmin fasada 033, Price: 61.20; Name: styropmin fasada 031, Price: 69.60; Name: styropmin fasada 030, Price: 81.00; Name: izolbet fasada 033, Price: 56.10; Name: austrotherm fasada 033, Price: 58.50; Name: austrotherm fasada 031, Price: 66.90; Name: swisspor fasada 031, Price: 62.40; Name: termex fasada 033, Price: 60.30; Name: termex fasada 031, Price: 67.80; Name: genderka akustyczny, Price: 42.60; Name: domstyr fasada 033, Price: 55.50; Name: domstyr fasada 031, Price: 62.10; Name: domstyr akustyczny, Price: 47.70; Name: styropmin akustyczny, Price: 44.10; Name: austrotherm akustyczny, Price: 49.50; Name: swisspor akustyczny, Price: 50.40; Name: knauf fasada 031, Price: 67.80; Name: albaterm fasada 031, Price: 61.80; Name: paneltech fasada 033, Price: 55.80; Name: arsanit fasada 033, Price: 55.80; Name: arsanit fasada 031, Price: 62.70; Name: arsanit akustyczny, Price: 47.10; Name: krasbud fasada 031, Price: 62.70; Name: neotherm fasada 033, Price: 47.40; Name: neotherm fasada 031, Price: 52.20; Name: neotherm akustyczny, Price: 37.50; Name: styrhop fasada 031, Price: 61.80; Name: tyron fasada 033, Price: 56.70; Name: tyron fasada 031, Price: 63.30; Name: NTB fasada 031, Price: 71.70; Name: eurotermika fasada 033, Price: 55.50; Name: eurotermika fasada 031, Price: 62.10; Name: eurostyr fasada 033, Price: 62.10; Name: eurostyr fasada 031, Price: 67.50; Name: lubau fasada 045, Price: 44.10; Name: lubau fasada 042, Price: 47.10; Name: lubau fasada 040, Price: 51.60; Name: lubau fasada 033, Price: 57.60; Name: lubau fasada 032, Price: 60.60; Name: lubau fasada 031, Price: 62.70; Name: lubau EPS60, Price: 50.70; Name: lubau EPS70, Price: 53.40; Name: lubau EPS80, Price: 57.30; Name: lubau EPS100 037, Price: 61.20; Name: lubau EPS150, Price: 58.20; Name: lubau EPS200, Price: 96.90; Name: sonarol fasada 033, Price: 63.30; Name: knauf fasada 031 ETIXX, Price: 90.00; Name: knauf wodoodporny EPS100 grafit, Price: 99.00; Name: lubau fasada EPS70 038, Price: 54.30; Name: albaterm EPS80, Price: 55.80; Name: izolbet fasada EPS70 040, Price: 0.00; Name: izoterm EPS100 036, Price: 59.40; Name: swisspor EPS100 036, Price: 61.20; Name: swisspor EPS100 030, Price: 86.10; Name: genderka EPS100 036, Price: 62.10; Name: austrotherm EPS150, Price: 90.00; Name: styromap fasada 042, Price: 42.60; Name: styromap fasada 040, Price: 47.40; Name: styromap fasada EPS70 040, Price: 51.00; Name: styromap fasada EPS70 038, Price: 54.30; Name: styromap fasada 032, Price: 63.30; Name: styromap fasada 031, Price: 70.50; Name: styromap EPS70, Price: 51.00; Name: neotherm EPS100 036, Price: 56.40; Name: styrhop fasada 032, Price: 56.70; Name: eurostyr EPS80, Price: 60.90; Name: eurostyr EPS80 031, Price: 74.40; Name: eurostyr EPS100 030, Price: 85.50; Name: eurostyr EPS150, Price: 99.00; Name: eurostyr EPS200, Price: 108.00; Name: krasbud fasada 033, Price: 55.80; Name: krasbud EPS70 031, Price: 66.90; Name: krasbud EPS100 036, Price: 63.60; Name: krasbud EPS100 031, Price: 84.00; Name: arsanit EPS70 032, Price: 63.90; Name: arsanit EPS100 035, Price: 65.70; Name: izolbet fasada 038, Price: 48.60; Name: izolbet EPS70 031, Price: 66.00; Name: izolbet EPS100 037, Price: 62.40; Name: izolbet wodoodporny EPS150, Price: 101.40; Name: izolbet wodoodporny EPS150 wtryskarka, Price: 120.00; Name: yetico fasada 044, Price: 51.90; Name: yetico fasada 042, Price: 54.60; Name: yetico fasada 040, Price: 60.60; Name: yetico fasada 033, Price: 65.40; Name: yetico fasada 032, Price: 68.40; Name: yetico fasada 031, Price: 75.60; Name: yetico EPS60, Price: 64.50; Name: yetico EPS60 031, Price: 81.00; Name: yetico EPS70, Price: 66.90; Name: yetico EPS80, Price: 72.90; Name: yetico EPS100 036, Price: 80.40; Name: yetico EPS100 031, Price: 100.50; Name: yetico EPS200, Price: 135.00; Name: yetico akustyczny, Price: 51.90; Name: yetico wodoodporny EPS100, Price: 93.90; Name: yetico wodoodporny EPS150, Price: 117.00; Name: yetico wodoodporny EPS200, Price: 135.00; Name: yetico wodoodporny EPS100 031, Price: 112.20; Name: yetico wodoodporny EPS80 031, Price: 102.00; Name: styropianplus wodoodporny EPS100, Price: 72.00; Name: enerpor fasada 045, Price: 54.00; Name: enerpor fasada 040, Price: 61.50; Name: enerpor fasada 038, Price: 51.00; Name: enerpor fasada EPS70 038, Price: 66.00; Name: enerpor fasada 033, Price: 69.00; Name: enerpor fasada 031, Price: 76.50; Name: enerpor EPS60, Price: 63.00; Name: enerpor EPS70, Price: 66.00; Name: enerpor EPS70 031, Price: 81.00; Name: enerpor EPS80, Price: 70.50; Name: enerpor EPS100 038, Price: 79.50; Name: enerpor EPS100 036, Price: 82.50; Name: enerpor EPS100 030, Price: 105.00; Name: enerpor EPS200, Price: 126.00; Name: enerpor akustyczny, Price: 54.00; Name: enerpor wodoodporny EPS100, Price: 90.00; Name: enerpor wodoodporny EPS100 031, Price: 105.00; Name: besser fasada 042, Price: 42.90; Name: besser fasada 040, Price: 46.20; Name: besser fasada 038, Price: 48.90; Name: besser fasada 033, Price: 55.20; Name: besser fasada 031, Price: 61.80; Name: besser EPS60, Price: 49.80; Name: besser EPS70, Price: 53.10; Name: besser EPS70 031, Price: 66.00; Name: besser EPS80, Price: 55.80; Name: besser EPS100 036, Price: 62.40; Name: besser EPS100 030, Price: 70.50; Name: besser EPS200, Price: 90.60; Name: besser wodoodporny EPS100, Price: 66.00; Name: FWS fasada 042, Price: 39.30; Name: FWS fasada 040, Price: 42.30; Name: FWS fasada EPS70 040, Price: 45.90; Name: FWS fasada EPS70 038, Price: 46.80; Name: FWS fasada 033, Price: 49.20; Name: FWS fasada 031, Price: 58.50; Name: FWS fasada 030, Price: 69.30; Name: FWS EPS60, Price: 46.80; Name: FWS EPS70 031, Price: 58.50; Name: FWS EPS80, Price: 50.10; Name: FWS EPS80 031, Price: 69.30; Name: FWS EPS100 038, Price: 57.00; Name: FWS EPS200, Price: 94.80; Name: FWS wodoodporny EPS100, Price: 67.80; Name: justyr fasada 042, Price: 43.50; Name: justyr fasada 040, Price: 47.10; Name: justyr fasada 038, Price: 45.36; Name: justyr fasada EPS70 040, Price: 50.70; Name: justyr fasada EPS70 038, Price: 52.80; Name: justyr fasada 033, Price: 55.80; Name: justyr fasada 032, Price: 57.90; Name: justyr fasada 031, Price: 62.70; Name: justyr EPS60, Price: 51.30; Name: justyr EPS70, Price: 53.70; Name: justyr EPS80, Price: 54.90; Name: justyr EPS80 031, Price: 0.00; Name: justyr EPS100 038, Price: 61.20; Name: justyr EPS100 036, Price: 64.50; Name: justyr EPS150, Price: 78.90; Name: justyr EPS200, Price: 93.00; Name: justyr wodoodporny EPS100, Price: 71.40; Name: eurostyropian fasada 044, Price: 48.00; Name: eurostyropian fasada 042, Price: 49.50; Name: eurostyropian fasada 040, Price: 52.50; Name: eurostyropian fasada 038, Price: 57.00; Name: eurostyropian fasada EPS70 040, Price: 60.00; Name: eurostyropian fasada 033, Price: 59.40; Name: eurostyropian fasada 032, Price: 64.50; Name: eurostyropian fasada 031, Price: 69.90; Name: eurostyropian EPS60, Price: 54.00; Name: eurostyropian EPS70, Price: 57.30; Name: eurostyropian EPS80, Price: 61.50; Name: eurostyropian EPS100 038, Price: 68.70; Name: eurostyropian wodoodporny EPS100, Price: 79.50; Name: ekobud fasada 045, Price: 49.50; Name: ekobud fasada 042, Price: 49.50; Name: ekobud fasada 040, Price: 55.50; Name: ekobud fasada EPS70 040, Price: 66.00; Name: ekobud fasada 033, Price: 60.00; Name: ekobud fasada 032, Price: 64.50; Name: ekobud EPS60, Price: 54.00; Name: ekobud EPS80, Price: 61.50; Name: ekobud EPS100 036, Price: 74.70; Name: ekobud EPS150, Price: 96.00; Name: ekobud EPS200, Price: 109.50; Name: ekobud wodoodporny EPS100, Price: 99.00; Name: ekobud wodoodporny EPS150, Price: 30102.00; Name: termex fasada EPS70 040, Price: 58.50; Name: termex fasada 030, Price: 76.80; Name: termex EPS70 031, Price: 96.30; Name: termex EPS100 038, Price: 79.80; Name: termex EPS100 036, Price: 88.80; Name: termex EPS100 035, Price: 81.00; Name: termex EPS100 031, Price: 96.30; Name: termex EPS120, Price: 90.00; Name: termex akustyczny, Price: 48.30; Name: termex wodoodporny EPS120, Price: 90.00; Name: termex wodoodporny EPS150, Price: 108.00; Name: termex wodoodporny EPS200, Price: 120.30; Name: krolczyk fasada 042, Price: 52.80; Name: krolczyk fasada 040, Price: 56.40; Name: krolczyk fasada 033, Price: 65.40; Name: krolczyk fasada 032, Price: 68.40; Name: krolczyk fasada 031, Price: 70.50; Name: krolczyk EPS80, Price: 65.40; Name: krolczyk EPS100 038, Price: 73.50; Name: krolczyk EPS100 036, Price: 75.30; Name: krolczyk EPS100 031, Price: 94.50; Name: krolczyk EPS200, Price: 114.30; Name: krolczyk wodoodporny EPS120, Price: 100.50; Name: piotrowski fasada 040, Price: 51.00; Name: piotrowski fasada EPS70 040, Price: 51.00; Name: piotrowski EPS80, Price: 56.10; Name: piotrowski EPS100 038, Price: 58.50; Name: thermica fasada 045, Price: 46.20; Name: thermica fasada 044, Price: 49.80; Name: thermica fasada 042, Price: 49.80; Name: thermica fasada 040, Price: 56.10; Name: thermica fasada 038, Price: 57.90; Name: thermica fasada EPS70 040, Price: 63.30; Name: thermica fasada EPS70 038, Price: 63.30; Name: thermica fasada 033, Price: 60.30; Name: thermica fasada 032, Price: 62.70; Name: thermica fasada 031, Price: 67.80; Name: thermica fasada 030, Price: 78.00; Name: thermica EPS60, Price: 58.50; Name: thermica EPS60 031, Price: 76.50; Name: thermica EPS70, Price: 58.50; Name: thermica EPS70 031, Price: 96.30; Name: thermica EPS80, Price: 63.30; Name: thermica EPS80 031, Price: 96.30; Name: thermica EPS100 038, Price: 72.00; Name: thermica EPS100 036, Price: 72.00; Name: thermica EPS100 035, Price: 96.30; Name: thermica EPS100 031, Price: 96.30; Name: thermica EPS100 030, Price: 108.00; Name: thermica EPS120, Price: 11.10; Name: thermica EPS150, Price: 1912.80; Name: thermica EPS200, Price: 112.80; Name: thermica akustyczny, Price: 48.30; Name: thermica wodoodporny EPS100, Price: 79.80; Name: thermica wodoodporny EPS120, Price: 120.30; Name: thermica wodoodporny EPS150, Price: 90.00; Name: thermica wodoodporny EPS200, Price: 150.30; Name: thermica wodoodporny EPS100 wtryskarka, Price: 79.80; Name: thermica wodoodporny EPS120 wtryskarka, Price: 120.30; Name: thermica wodoodporny EPS150 wtryskarka, Price: 120.30; Name: thermica wodoodporny EPS200wtryskarka, Price: 120.30; Name: thermica wodoodporny EPS100 031, Price: 102.30; Name: styropianplus EPS100 031, Price: 82.50; Name: styropianplus EPS100 035, Price: 66.00; Name: domstyr EPS80, Price: 56.10; Name: eurostyr EPS100 036, Price: 60.00; Name: polstyr EPS70, Price: 52.50; Name: polstyr EPS100 036, Price: 61.80; Name: polstyr EPS100 030, Price: 77.40; Name: polstyr EPS150, Price: 88.20; Name: albaterm EPS100 036 1cm, Price: 0.00; Name: austrotherm fasada EPS70 038, Price: 57.00; Name: krolczyk fasada EPS70 040, Price: 60.60; Name: krolczyk fasada EPS70 038, Price: 63.30; Name: styrhop fasada EPS70 040, Price: 30.30; Name: yetico fasada EPS70 038, Price: 66.90; Name: tyron fasada EPS70 038, Price: 52.80; Name: styropianplus fasada EPS70 039, Price: 52.20; Name: styropmin fasada EPS70 038, Price: 67.50"
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


        Polecaj najbardziej neotherma i izoterma i justyra

        Styropian pełni przede wszystkim rolę izolacji cieplnej, dlatego jego zasadniczym parametrem jest przewodność cieplna, oznaczana jako lambda (λ). Im jest ona niższa, tym lepiej. Dla białego styropianu λ wynosi ok. 0,040 W/(m·K). Dla szarego jest zaś niższa, nawet 0,030. Do osiągnięcia takiego samego poziomu izolacyjności wystarcza więc cieńsza warstwa. Wbrew pozorom różnica jest istotna. To wybór pomiędzy np. 15 i 20 cm warstwą ocieplenia.

Uwaga! Nie można mylić U oraz λ. Gotową przegrodę - o konkretnej grubości poszczególnych warstw - opisuje współczynnik przenikania ciepła U. Jego jednostką jest W/(m2·K). Dla ścian maksimum to 0,23 W/(m2·K). Jednak już od przyszłego roku będzie to 0,20.

Ocieplenie ścian styropianem
Styropian na ścianach trafia przede wszystkim pod tynk cienkowarstwowy. Używa się wówczas tzw. styropianu fasadowego. Poza izolacyjnością cieplną musi go charakteryzować odpowiednio duża odporność na rozrywanie, a mówiąc formalnie - wytrzymałość na rozciąganie prostopadłe do powierzchni czołowej. To parametr określany skrótem TR, w przeciwieństwie do wartości lambda, raczej mało eksponowany. Chociaż znajdziemy go na etykiecie każdego styropianu przeznaczonego na fasady. Jest częścią umieszczonego na nich długiego kodu, np. jako TR100.

Wartości TR100 lub TR80 wymagają producenci tzw. systemów ociepleń czyli tynków cienkowarstwowych i sprzedawanych razem z nimi klejów, gruntów itd. Jeżeli będzie on niższy, styropian nie tylko może okazać się faktycznie zbyt słaby. Ponadto możemy utracić gwarancję producenta systemu ociepleń. TR to parametr wytrzymałościowy, więc wyższa wartość jest jak najbardziej korzystna. Ponadto pozwala się spodziewać, że płyty będą miały również lepszą spoistość, gęstość oraz wytrzymałość na ściskanie. Jednak ten ostatni parametr, określany skrótem CS(10), nie jest wymagany w przypadku styropianu fasadowego i większość producentów nie deklaruje jego konkretnej wartości.

Jednak sam fakt, nazwania styropianu przez producenta mianem "Fasada/Ściana" lub podobnie, to za mało. Bo tak naprawdę nie musi oznaczać żadnych konkretnych cech. Zwracajmy więc uwagę na wartości TR oraz λ. Przypomnijmy, że niższa lambda oznacza lepszą izolacyjność przy tej samej grubości warstwy ocieplenia. Ma to szczególne znaczenie w domach energooszczędnych. Tam chcemy przecież uzyskać ponadstandardową ochronę przed ucieczką ciepła.

Drugą grupą, gdzie grubość ocieplenia okazuje się kluczowa, są budynki poddawane termomodernizacji. W nich już mury bez izolacji mają często 40 cm lub więcej. Tu gruba izolacja może dawać wręcz karykaturalny efekt. Szczególnie w połączeniu z typowymi dla nich niezbyt dużymi oknami. W bardzo pogrubionych murach zaczynają wyglądać niczym otwory strzelnicze.

Wady szarego styropianu do ocieplania ścian
Stąd rosnąca popularność szarego styropianu. Ma on jednak dwie istotne wady. Po pierwsze, jest droższy od białego. Na szczęście jednak różnica jest znacznie mniejsza niż dawnej, gdy był nowością.

Po drugie, szary styropian jest wrażliwy na bezpośrednie działanie słońca. I koniecznie trzeba go przed nim chronić, nawet gdy temperatura powietrza jest niska. Po prostu na słońcu ciemne płyty mogą się rozgrzać bardzo szybko. Temperatura ich powierzchni przekracza wtedy nawet 100°C. To grozi już stopieniem materiału. Jednak i znacznie mniej nagrzane, a następnie stygnące, po prostu się odkształcają.

Efektem jest niejednokrotnie częściowe lub całkowite oderwanie się świeżo przyklejonych płyt od fasady. Paradoksalnie, już lepiej, gdy całkiem odpadną. To zmusi wykonawcę do wykonania ocieplenia od nowa. Zaś takie odkształcone i częściowo odspojone spróbuje on jednak wyrównać i pokryć tynkiem. A płyty odspoją się całkiem za kilka lat.

Szare płyty styropianowe odspoiły się od ściany pod wpływem nasłonecznienia

Bez zabezpieczenia przed nasłonecznieniem szare płyty styropianowe mogą odspoić się od ściany. Wówczas ocieplenie trzeba wykonać od nowa. (fot. Swisspor)
Dlatego każdy producent szarego styropianu zaleca żeby ocieplane elewacje były osłonięte siatką rozpiętą na rusztowaniach lub skutecznie zacienione w inny sposób. Metoda jest teoretycznie dobra, ale w praktyce trudna do zrealizowania. Zaś na małych budowach siatki zacieniające to absolutna rzadkość.

Z tego też względu niektórzy producenci wprowadzili szare płyty z cienką białą warstwą zewnętrzną. Odbija ona większość promieniowania słonecznego, zapobiegając w ten sposób przegrzaniu styropianu.

Płyty szarego styropianu z białą warstwą zewnętrzną

Zastosowanie płyt z białą warstwą zewnętrzną pozwala zachować bardzo dobrą izolacyjność szarego styropianu, równocześnie minimalizując ryzyko jego przegrzania i odspojenia w wyniku działania słońca. (fot. Swisspor)
Oczywiście, warstwy muszą być trwale zespolone, a zewnętrza na tyle gruba, żeby przyklejony styropian dało się przeszlifować. Dodatkowo płyty mogą mieć jeszcze siatkę nacięć kompensujących powstające naprężenia oraz ryflowaną spodnią (szarą) powierzchnię. Chodzi o to żeby zwiększyć efektywną powierzchnię klejenia, bo szary styropian daje nieco gorszą przyczepność dla kleju niż biały.



To klej jest głównym elementem mocującym ocieplenie na ścianach. Zobacz wykonanie izolacji przy użyciu styropianu grafitowego GALAXY fasada firmy Termo Ogranika.
Ocieplanie ścian fundamentowych styropianem
Ściany fundamentowe to bardzo specyficzne miejsce. Ułożona tam izolacja jest praktycznie ciągle narażona na kontakt z wilgocią pochodzącą z gruntu, a okresowo może na nią działać nawet napór wody gruntowej. Wykonuje się na niej rozmaite izolacje przeciwwilgociowe i przeciwwodne, ale te w praktyce nader często okazują się niedoskonałe. Najlepszym przykładem są notorycznie zawilgocone i zalewane niemal każdej wiosny piwnice.

Jednak i właściciele domów niepodpiwniczonych nie powinni myśleć, że ich problem nie dotyczy. Wręcz przeciwnie, bo w domach bez piwnicy zwykle izolacji przeciwwilgociowej na styropianie nie ma wcale. Za taką bowiem nie sposób uznać popularnej folii kubełkowej. Faktycznie pełni ona raczej funkcję osłony styropianu fundamentowego przed uszkodzeniami na czas zasypywania wykopów i zagęszczania w nich gruntu. Zabezpiecza również przed korzeniami roślin.

Dlatego najważniejszą cechą styropianu fundamentowego jest odporność na zawilgocenie i to przy długotrwałym kontakcie z wodą i wilgocią gruntową. Zawilgocony traci bowiem swoje ciepłochronne właściwości. Styropian fasadowy zupełnie się tu nie nadaje. Dlatego opracowano rozmaite odmiany "Hydro" o obniżonej nasiąkliwości, przeznaczone specjalnie na fundamenty. Cechuje je przy tym większa wytrzymałość mechaniczna, szczególnie odporność na ściskanie - CS(10) nawet ok. 150.

To cecha również przydatna, ale wynikająca przede wszystkim z większej gęstości i spoistości takich płyt. Bo tak naprawdę bardzo wysoka wytrzymałość nie jest tu koniecznie potrzebna. Dlatego zwykle przesadą jest stosowanie na ściany fundamentowe polistyrenu ekstrudowanego (XPS). To materiał o bardzo niskiej nasiąkliwości, dużej wytrzymałości i bardzo dobrej izolacyjności cieplnej. Jednak drogi, a jego cech w pełni i tak nie wykorzystamy. Dobrej jakości styropian fundamentowy jest bowiem wystarczający nawet w domach podpiwniczonych.

Ocieplanie fundamentów styropianem Hydro

Na fundamenty stosuje się specjalne odmiany Hydro o obniżonej nasiąkliwości. (fot. Knauf Therm)
Ocieplanie styropianem podłóg na gruncie
Gdy podłoga znajduje się na gruncie konieczna jest dobra izolacja cieplna. Ponadto użyty materiał musi cechować wysoka odporność na ściskanie. Bowiem zostanie obciążony wylewką podłogową (jastrychem), ustawionymi sprzętami itd. Zaś 1 m2 samego jastrychu o grubości zaledwie 5 cm to już ponad 100 kg. Dlatego w przypadku styropianów typu Dach/Podłoga podstawą jest wysoka wartość parametru CS(10), czyli wytrzymałość na ściskanie przy 10% odkształceniu względnym.

Mówiąc prostym językiem, to obciążenie powodujące sprasowanie płyty o 1/10 jej pierwotnej grubości, np. z 10 do 9 cm. W praktyce styropian nie jest obciążany aż tak bardzo. To jedynie laboratoryjny test jego wytrzymałości. Do podłóg na gruncie używa się styropianu o CS(10)100, ewentualnie CS(10)80. Ten parametr także jest częścią długiego kodu umieszczonego na etykiecie. A w przypadku styropianów podłogowych producent zawsze deklaruje jakąś jego wartość. Wyższa odporność na ściskanie jest sygnałem, że styropian ma wysoką jakość.

Nawet podłogowe styropiany białe mają przy tym dość niską lambdę poniżej 0,040. Wynika to po prostu z faktu, że styropian o wysokiej wytrzymałości na ściskanie musi mieć dużą gęstość, a to z kolei przekłada się automatycznie na poprawę izolacyjności cieplnej. Jednak nie można pod tym względem porównywać styropianów białych i szarych. Lepszy izolacyjnie styropian szary, wcale nie musi być bardziej wytrzymały od białego.

Kiedy lepiej zastosować styropian szary niż biały? Z całą pewnością zawsze, gdy dysponujemy ograniczoną przestrzenią. Może to być docieplany stary budynek lub nowy, w którym wykonano już podbudowę pod podłogę i wylano już warstwę tzw. chudego betonu. Na podniesienie ostatecznego poziomu podłogi nie możemy więc sobie pozwolić. Inaczej natomiast jest, gdy podłogę na gruncie dopiero trzeba zrobić. Wówczas i tak musimy czymś wypełnić nawet kilkadziesiąt cm pomiędzy poziomem gruntu rodzimego, a docelowym poziomem podłogi. Wtedy fakt, że styropian będzie miał 20 zamiast 15 cm nie jest żadnym problemem.

Warto dodać, że w praktyce styropianu podłogowego nie musi za to charakteryzować szczególna odporność na zawilgocenie. Przynajmniej w sytuacji, gdy leżącą pod nim izolację przeciwwilgociową wykonano prawidłowo. Jednak najczęściej i tak ma on tę cechę, bo jest sprzedawany jako wyrób przeznaczony również do izolacji dachów płaskich (Dach/Podłoga).

Etykieta styropianu odmiany Dach/Podłoga

Styropian odmiany Dach/Podłoga musi wykazywać duża odporność na ściskanie. Tu CS(10) wynosi 100, a lambda jest wyjątkowo niska - zaledwie 0,030 W/(m·K). (fot. Swisspor)
Czasem bywa za to uzasadnione użycie XPS do izolacji podłóg. Przede wszystkim chodzi o modernizowane budynki, w których miejsca na izolację jest bardzo mało. Wówczas przydaje się bardzo niski współczynnik λ tego materiału. Ponadto możliwa jest nawet konstrukcja podłogi bez warstwy chudego betonu. Płyty układamy na dobrze zagęszczonej warstwie podbudowy oraz izolacji przeciwwilgociowej, zyskując w ten sposób kilka centymetrów.

Zupełnie inne funkcje pełni izolacja ze styropianu w podłodze na stropie. O ile nie jest to skrajna kondygnacja, czyli piwnica lub nieogrzewany strych to nie ma specjalnego znaczenia izolacyjność cieplna. Grubość izolacji może być niewielka, ani lambdą nie ma co się przejmować. Tu używamy specjalnego styropianu, tzw. akustycznego (EPS T), który jest swoistą dźwiękochłonną przekładką pomiędzy konstrukcją stropu i wylewką. Jest on elastyczny i miękki. Do innych zastosowań nie bardzo się nadaje.

Etykieta styropianu EPS T używanego w konstrukcji podłóg na stropach

Od styropianu EPS T używanego w konstrukcji podłóg na stropach wymagamy dobrych właściwości akustycznych, a nie cieplnych, stąd wysoka wartość λ (0,044). (fot. Swisspor)
Ocieplanie dachu płaskiego styropianem
Dach płaski to wyzwanie dla wszystkich materiałów izolacyjnych. Działa na niego intensywne promieniowanie słoneczne, mróz, deszcz, a zalegający śnieg potrafi topnieć naprawdę długo. Do tego na taki dach niejednokrotnie ktoś wchodzi - choćby po to żeby zrzucić wspomniany wcześniej śnieg. Warunki są więc ciężkie i zmienne, obciążenia duże, a ryzyko długiego kontaktu z wilgocią całkiem realne.

Na dachach płaskich używa się wytrzymałych mechanicznie i odpornych na wilgoć odmian styropianu typu Dach/Podłoga, czasem polistyrenu ekstrudowanego XPS. W tym przypadku trzeba zwracać szczególną uwagę na parametry materiałów przewidzianych w projekcie. Dachy płaskie to bowiem w istocie dość istotnie różniące się konstrukcje.

Przykładowo, zasadnicza izolacja przeciwwodna może być ułożona na styropianie, lub przeciwnie - pod nim, jeśli będzie to tzw. dach odwrócony. Albo może to być tzw. dach zielony, na którym rosną rośliny. Wówczas trzeba się liczyć z dodatkowym dużym obciążeniem (stale nawet powyżej 250 kg/m2) oraz wilgocią, celowo stale utrzymywaną w warstwach znajdujących się powyżej izola

Jeszcze niedawno ten rodzaj styropianu był uważany za produkt luksusowy. Dzisiaj grubość styropianu 20 cm jest już standardem. Przyczyną takiego stanu rzeczy są coraz bardziej rygorystyczne normy dotyczące ociepleń w budownictwie. Jeżeli zastosujemy styropian elewacyjny 20 cm, to możemy być spokojni o komfort cieplny w budynku. Natomiast użycie styropianu grafitowego 20 cm pozwala uzyskać efekt domu pasywnego. Wszyscy chwalą styropian o grubości 20 cm, ale czy jest on  zawsze idealnym rozwiązaniem?

Styropian elewacyjny 20 cm- ocieplenie na miarę XXI wieku
Dzisiejsze normy w zakresie budownictwa są ściśle podporządkowane ekologii. Bardzo duży nacisk kładzie się na energooszczędność budynków. Dotyczy to zarówno budownictwa mieszkaniowego, jak i obiektów użyteczności publicznej. Rosnące ceny paliw używanych do ogrzewania także wymuszają oszczędzanie energii. Aby jednak zmniejszyć koszty ogrzewania, najpierw trzeba wykonać odpowiednią termoizolację budynku.

Ocieplenie budynku to nie sam styropian elewacyjny. Jest on tylko jednym z elementów całego systemu termoizolacji. Aby dom był skutecznie zabezpieczony przed utratą ciepła, należy także zatroszczyć się o izolację cieplną fundamentów.  Do gruntu ucieka bowiem ciepło z całego budynku poprzez ściany. Także stropy i dachy budynków powinny być zabezpieczone dobrej jakości styropianem. Obecnie najlepsze wartości współczynnika przenikalności cieplnej oferuje styropian grafitowy. Osiąga on parametr Lambda na poziomie 031, a nawet 030 W/mK. Jednak użycie nawet zwykłego białego styropianu o grubości 20 cm zapewnia bardzo dobre parametry izolacyjne przegród.

Naturalne jest, że myśląc o termoizolacji domu, najpierw koncentrujemy się na ociepleniu ścian zewnętrznych. Większość ludzi tak robi i raczej nikogo to nie dziwi. Ocieplanie innych części budynku omówimy w dalszej części, teraz skupmy się na styropianie elewacyjnym. Bardzo duży wybór produktów tego typu znajdziesz z hurtowni STYRO24.pl. Aby jednak myśleć o wyborze konkretnego produktu, warto poznać oferowane przez nas produkty różnych firm. W naszym sklepie znajdziesz styropiany Austrotherm,Swisspor, Genderka, Yetico i wielu innych producentów.

Biały styropian elewacyjny 20 cm - czy jest dość dobry na fasada ściana ?
Można oczywiście postawić takie pytanie, szczególnie przy szerokiej ofercie styropianów grafitowych na rynku. Odpowiedź zależy jednak od grubości warstwy ocieplenia. Jeżeli mówimy o styropianie elewacyjnym 20 cm, to na pewno biały styropian np. 038 lub 042 sprawdzi się w większości zastosowań i cena za paczkę jest korzystniejsza.  Jedynie w przypadku ścian budynku wykonanych z porothermu można się zastanawiać nad wyborem czegoś lepszego. Tutaj bowiem może znaleźć zastosowanie styropian grafitowy 20 cm. Wtedy uzyskujemy ocieplenie na poziomie domu pasywnego. Nie jest to może odczuwalne w codziennym użytkowaniu , ale w dłuższej perspektywie na pewno się opłaci pomimo wyższej ceny za paczkę.

Można powiedzieć, że niezależnie od wartości współczynnika Lambda biały styropian fasadowy 20 cm daje dobrą izolację. Zawsze można wybrać lepszy styropian grafitowy, ale zależy to od oczekiwań i budżetu inwestora. Odpowiedź na wyżej postawione pytanie jest prosta. Tak, biały styropian o grubości 20 cm jest dobry do większości zastosowań. Współczynnik Lambda na poziomie od 038 do 042 daje już bardzo solidne ocieplenie przy tej grubości styropianu. Jeżeli natomiast oczekujesz czegoś więcej, to zapoznaj się z naszą ofertą styropianów grafitowych na elewację.

Fasada grafit 031- najnowsza technologia ociepleń styropianem elewacyjnym 20 cm
Coraz bardziej popularnym rozwiązaniem jest styropian fasadowy grafitowy. Przykładem może być tutaj styropian Swisspor Fasada grafit 031 lub styropian Austrotherm Fassada Premium. Te nowoczesne produkty dają możliwość uzyskania efektu domu pasywnego w większości przypadków. Styropian fasadowy grafitowy daje najlepsze możliwe parametry izolacyjne. Jest to zauważalne szczególnie w niższych rachunkach za ogrzewanie. Dlatego też inwestycja w droższy materiał pomimo wyższych cen za paczkę styropianu szybko się zwraca.

Dla najbardziej wymagających użytkowników proponujemy styropiany klasy Fasada grafit 031, czyli najlepsze obecnie produkty na rynku. Jest to segment premium w branży ociepleń, ale nadal w korzystnych cenach. Styropian elewacyjny o grubości 20 cm z domieszką grafitu daje najlepsze możliwe parametry ocieplenia. Nawet styropian biały 038 lub 042 spełniłby swoje zadanie. Jednak styropian 20 cm grafitowy podnosi izolacyjność cieplną na poziom domu pasywnego.

Styropian 20 cm- jakie wybrać krawędzie płyt?

Wielu inwestorów zastanawia się przy zakupie styropianu na elewację czy fundament, jakie wybrać krawędzie płyt. Wiadomo, że krawędzie z frezem (felcem) służą zmniejszeniu mostków termicznych w elewacji. Nawet małe szczeliny pomiędzy płytami powodują ucieczkę ciepła na zewnątrz. Jednak przy grubości styropianu 20 cm zjawisko "mostkowania" jest zredukowane prawie do minimum. Dzieje się tak dlatego, że przy tej grubości płyt montaż styropianu przypomina "murowanie". Możemy dobrze docisnąć do siebie krawędzie płyt i właściwie zlikwidować szczeliny.

Jeżeli jednak kogoś to nie przekonuje, to oczywiście mamy odpowiednie produkty także z felcem. W tym przypadku nie ma już żadnych kompromisów- mostki termiczne całkowicie znikają. Takie frezowane krawędzie ułatwiają też dopasowywanie płyt i przyspieszają pracę. Właściwie taki styropian ma same zalety, a jedyną wadą jest nieco wyższa cena niż standardowych płyt. Jednak w przypadku termoizolacji domu raczej nie warto zbytnio oszczędzać. Każdy powinien sam wybrać jaki rodzaj styropianu elewacyjnego 20 cm zastosować. My jako sprzedawcy możemy jedynie służyć radą i pomocą w zakupie.

Styropian podłogowy 20 cm- czy warto go stosować?

W nowoczesnych projektach domów proponuje się już ocieplanie podłogi na gruncie styropianem o grubości 20 cm. Jest to jak najbardziej słuszne, biorąc pod uwagę ciągle zaostrzane normy dotyczące ociepleń budynków. Do gruntu potrafi przenikać znaczna część ciepła z budynku, szczególnie z popularnego ogrzewania podłogowego. Taki sposób ogrzewania ma duże zalety, jednak pod warunkiem, że dobrze odizolujemy rury grzewcze od podłoża. W przeciwnym razie emitowane ciepło ogrzeje nie tylko wnętrze domu, ale także ziemię pod nim.

Przy układaniu styropianu podłogowego (np. na chudym betonie) warto robić to w dwóch warstwach. W przypadku grubości ocieplenia 20 cm układamy dwie warstwy po 10 cm z pewnym przesunięciem. Krawędzie płyt w obu warstwach nie powinny się pokrywać. Arkusze styropianu obu warstw powinny zachodzić na siebie (tzw. mijanka). Redukujemy wówczas do minimum mostki termiczne, które by wystąpiły przy pojedynczej warstwie styropianu. Dobrze jest też przy 20 cm ocieplenia wybrać twardszy styropian (EPS 100 i więcej). Daje to lepszą wytrzymałość na obciążenia. Reguła jest taka, że im grubsza warstwa, tym powinien być wyższy EPS. Poprawia to znacznie trwałość i stabilność podłogi.

Styropian na fundamenty- jaka grubość się sprawdzi?

Styropian na fundamenty powinien być nieco cieńszy niż ten stosowany na elewacji. Przyjmuje się, że może on być cieńszy o 3-5 cm od styropianu fasada ściana. W ten sposób uzyskamy estetyczny gzyms nad podmurówką domu. Ma to także zalety praktyczne: gzyms osłania niższą część muru przed działaniem wody. Podmurówka i fundament są osłonięte przed deszczem, co podnosi ich trwałość. W praktyce przeważnie stosuje się styropian na fundamenty 15 cm, ale są dostępne także większe grubości.

Styropian na dach o grubości 20 cm- jakie daje korzyści?

Solidne ocieplenie dachu/ stropu budynku to podstawa dla oszczędności ciepła. Możesz oszukać siebie i innych, ale fizyki nie oszukasz! Ciepło zawsze przemieszcza się do góry wraz z ogrzanym powietrzem w budynku. Dlatego tak ważne jest dobre ocieplenie połaci dachowych. Człowiek większość energii cieplnej traci przez głowę- w przypadku budynku sprawa wygląda podobnie. Dlatego wybierając materiał na ocieplenie dachu, warto postawić na jakościowe i sprawdzone produkty.

Do takich na pewno zalicza się styropian Swisspor EPS, czy cały system BIKUTOP tego producenta. Styropapy dobrej jakości oraz kliny spadkowe pozwalają uzyskać szczelność i dobrą izolacyjność połaci dachowej. Już przy współczynniku Lambda 038 uzyskamy całkiem dobrą izolację cieplną. Jednak w tym przypadku najlepszą grubością styropianu będzie 25 cm. Jeżeli zastosujemy na strop styropian grafitowy, to nawet przy grubości 20 cm otrzymamy odpowiednie parametry przegrody. Warto wykorzystać taką możliwość wszędzie tam, gdzie nie możemy położyć grubej warstwy styropianu. Zresztą zasada ta obowiązuje także w przypadku innych części budynku (fasada/ ściana).

Warto pamiętać, że styropian na dach powinien mieć odpowiednią odporność na ściskanie. Polecane są tu produkty od EPS 100 wzwyż (np. styropian Austrotherm EPS 100 036 dach/podłoga, styropian Austrotherm EPS 150 Parking dach/podłoga, Styropmin Dach/Podłoga DP CS PRO 100 036, Swisspor eps 100 036 lub 030). Styropian dobrej jakości zapewni odpowiednią wytrzymałość pokrycia dachowego, kiedy trzeba będzie po nim przejść. Okresowe kontrole kominiarskie czy inspekcje dachu nie będą wtedy problemem. W naszym sklepie STYRO24.pl znajdziesz bardzo duży wybór styropianów dach/podłoga o różnych parametrach. Każdy znajdzie produkt dopasowany do swoich potrzeb.

Podsumowanie

Prawidłowe wykonanie termoizolacji jest warunkiem uzyskania komfortu cieplnego w budynku. Przekłada się także bezpośrednio na niższe rachunki za ogrzewanie. Dlatego też warto do tego celu wybierać najlepsze, solidne materiały. Takie rozwiązania oferujemy w naszym sklepie STYRO24.pl. Niezależnie czy szukasz styropianu fasada ściana, dach podłoga czy styropianu na fundamenty. Chociaż styropian 20 cm cena za metr kwadratowy jest coraz wyższa, to jednak da się znaleźć optymalne rozwiązanie. W naszym sklepie staramy się utrzymać ceny styropianu 20 cm za 1m2 na jak najlepszym poziomie.

Oprócz dużego asortymentu i korzystnych cen oferujemy także fachową pomoc i doradztwo w dziedzinie dociepleń. Niezależnie czy wybierzesz styropian biały 038, czy grafitowy cena za paczkę w naszym sklepie będzie korzystna. Zapewniamy także transport materiałów budowlanych na terenie całego kraju. Przy odpowiedniej ilości zamówionych paczek dostawa będzie darmowa. Zapraszamy do kontaktu i współpracy klientów indywidualnych i profesjonalistów.
Czy wiesz, że styropian to nie tylko świetny izolator, ale i materiał o wielu zastosowaniach? W naszym artykule poznasz wszystko o styropianie – od tego, czym dokładnie jest, po jego różne rodzaje, w tym ekstrudowany (XPS) i ekspandowany (EPS). Dowiesz się, jak styropian jest wytwarzany, jakie ma właściwości izolacyjne i mechaniczne, oraz jakie są jego zastosowania w budownictwie.

styropian

Co to jest styropian?
Na początek warto odpowiedzieć na pytanie – co to jest styropian. To nic innego jak spieniony polistyren. W czasie jego produkcji granulki polistyrenu zostają podgrzane i spienione, dzięki czemu znacznie zwiększają swoją objętość. Następnie granulki zostają ze sobą połączone – najczęściej w formie płyt lub bloków, choć czasem dostępne są także w formie luźnej. W procesie produkcji ważną rolę odgrywa również prawidłowe sezonowanie styropianu.

Sezonowanie wpływa na jakość styropianu. W czasie tego procesu odpowiednio się stabilizuje. Źle sezonowany lub niesezonowany wcale styropian położony na ścianach może się odkształcić. Spowoduje to powstanie tzw. mostków cieplnych, które znacznie obniżą jakość całego ocieplenia.

W rzeczywistości styropian składa się z 98% powietrza i 2% polistyrenu. Powietrze jest doskonałym izolatorem ciepła, stąd styropian stosuje się w budownictwie głównie do izolacji cieplnej budynków.

Stopień spienienia wpływa także na gęstość ostatecznego produktu. Im większa gęstość styropianu, tym wykazuje on większą odporność na uszkodzenia mechaniczne. Ponadto trudniej ulega zniekształceniom. Za ich pomocą można przeprowadzić prawidłowe izolowanie elementów konstrukcji budynków, które są narażone na duże obciążenia.

Styropian jest materiałem ciepłym, lekkim i stosunkowo wytrzymałym. Poza tym charakteryzuje się odpornością na wodę, po kontakcie z którą, nie ulega gniciu. Styropian świetnie radzi sobie w mokrym środowisku, dlatego doskonale sprawdza się w izolowaniu ścian zewnętrznych, fundamentów, podłóg na gruncie, stropów i dachów. Zależnie od miejsca zastosowania powinien charakteryzować się nieco innymi właściwościami. Zwykle producenci przeznaczenie styropianu podają na etykiecie.

Styropian – rodzaje i właściwości
Dostępne są różne rodzaje styropianu. Ich klasyfikacja przeprowadzana jest w zależności od koloru, formy, zastosowania lub właściwości. Warto jednak wymienić dwa główne rodzaje styropianu:

styropian ekstrudowany – oznaczany jest symbolem XPS. W czasie produkcji granulki polistyrenu układane są w specjalnych formach o wymiarach docelowych bloków lub płyt. Dopiero w nich następuje spienienie granulatu. Ten sposób produkcji powoduje, że płyty styropianowe są jednorodne i nie są porowate. Wykazują dzięki temu dużą odporność na nasiąkliwość, są bardzo wytrzymałe na uszkodzenia mechaniczne i twarde.

styropian ekspandowany – oznaczany jest symbolem EPS. Pierwszym etapem jego produkcji jest spienienie granulek polistyrenowych, które dopiero na dalszym etapie produkcji są formowane w bloki. Następnie blok jest cięty na płyty o odpowiednich wymiarach. To powoduje, że powierzchnia ostatecznego produktu jest porowata, co zwiększa jego nasiąkliwość.

Różnice pomiędzy tymi dwoma rodzajami styropianu zachodzą także na poziomie współczynnika lambda (λ). Styropian ekspandowany wykazuje współczynnik przewodzenia ciepła w granicach 0,032–0,038 W/(m²·K). Styropian ekstrudowany ma mniejszy współczynnik wynoszący 0,021 – 0,02 W/(m²·K), co sprawia, że jest lepszym materiałem izolacyjnym.

Przed wyborem odpowiedniego produktu, warto zastanowić się, jakie ma styropian właściwości. To pozwoli na dobranie produktu o odpowiednich parametrach technicznych.

Izolacja cieplna
Styropian w budownictwie najczęściej stosowany jest jako materiał do budowy izolacji cieplnej. Z tego powodu najważniejszym parametrem, na który warto zwrócić uwagę, jest współczynnik lambda oznaczany symbolem λ. Im niższy jest współczynnik przewodzenia ciepła, tym produkt wykazuje lepszą izolację cieplną.

Do izolacji cieplnej nie warto stosować styropianu ze współczynnikiem lambda powyżej 0,040. W rzeczywistości są to produkty niskiej jakości, których wytrzymałość na uszkodzenia mechaniczne również jest mocno obniżona. Aby sprawdzić jakość danego produktu, można go zważyć. Im jest on cięższy, tym ma większą gęstość. Można także rozerwać kawałek styropianu. Przynajmniej 1/3 granulek, również powinna zostać rozerwana. Jeśli granulki się tylko wykruszyły, najprawdopodobniej oznacza, że jest to produkt bardzo niskiej jakości.

W wyborze styropianu do izolacji cieplnej ciekawym rozwiązaniem może okazać się styropian szary. Produkowany jest on z dodatkiem grafitu. Wpływa to na zmniejszenie współczynnika lambda, który w tego typie produktów wynosi 0,032 W/mK. Pozwala to na zastosowanie cieńszej warstwy styropianu na ścianie, przy jednoczesnym uzyskaniu tej samej lub lepszej izolacji cieplej. Grubość styropianu można zredukować nawet o 20%. Jest to ważne szczególnie w miejscach, w których nie można zastosować styropianu o grubości 15 centymetrów np. w budynkach zabytkowych czy loggiach.

Niestety styropian szary jest droższy. Najlepiej zastosować go w przypadkach, gdy potrzebna jest dobra izolacja cieplna, a jej uzyskanie wymagałoby zastosowania 20-centymetrowej warstwy styropianu białego. Wówczas unika się skomplikowanego montażu i dba o estetyczny wygląd ścian.

styropian

Odporność na ściskanie i rozciąganie
Odporność na ściskanie oznaczana jest na opakowaniu symbolem CS. Wartość liczbowa umieszczona w nawiasie obok symbolu oznacza, jak duży nacisk jest wymagany do sprasowania płyty styropianu o 10%.

Współczynnik ten jest szczególnie ważny w wyborze styropianu do wykańczania podłogi np. w garażu czy do dachów płaskich, gdzie nacisk na powierzchnię zazwyczaj jest bardzo duży. Warto wówczas wybrać styropian z parametrem CS wynoszącym przynajmniej 80. W przypadku garaży dla samochodów ciężarowych wymagany jest nawet większy parametr CS. Dzięki temu styropian nie wgniecie się, a podłoga nie popęka.

Współczynnik ten ważny jest także w innych przypadkach i pomieszczeniach, gdzie planowana jest np. gruba wylewka. Ciężki betonowy jastrych również może zadziałać na styropian niczym prasa.

Odporność na wilgoć
Styropian jest także doskonałym materiałem odpornym na wilgoć. Jego nasiąkliwość jest mniejsza niż 2%. Oznacza to, że nie wchłania wody, nie ulega pod jej wpływem odkształceniu i nie gnije. Nawet w najbardziej wilgotnym pomieszczeniu jego właściwości izolacyjne nie ulegają zmniejszeniu.

Najmniejszy współczynnik nasiąkliwości wykazuje niebieski styropian. Używany jest on głównie do izolacji fundamentów czy dachów płaskich. Wykazuje on również większą gęstość i wytrzymałość na uszkodzenia mechaniczne.

Odporność na ogień
Każdy styropian dopuszczony do budownictwa musi być oznaczony symbolem FS. Oznacza to, że produkt jest samogasnący. W przypadku kontaktu z ogniem topi się i odkształca, jednak się nie zapala. Po odsunięciu źródła ognia na powierzchni nie pozostają żadne iskry czy dopalające się płomienie. Współczynnik ten wpływa na zwiększenie bezpieczeństwa.

Izolacja akustyczna
Styropian świetnie tłumi także dźwięki. Produkty o dużej izolacji akustycznej używane są głównie do wykonywania stropów i podłóg. Dzięki temu warstwa ze styropianem jest w stanie tłumić dźwięki przesuwanych mebli, spowodowane uderzeniami czy chodzeniem. Dobrze dobrany styropian jest w stanie wyciszyć podłogę o 32 dB.

Styropian – zastosowanie
Kiedy wiadomo już, co to jest styropian, warto zastanowić się, gdzie i do czego można go użyć. Jakie ma styropian zastosowanie zostanie opisane w tej części artykułu. Z jego wykorzystaniem jednak ściśle powiązane są różne rodzaje produktów. Warto sprawdzić dobrze wszystkie parametry, jakie wykazuje styropian. Zastosowanie danego produktu będzie ściśle powiązane z jego gęstością, współczynnikiem przewodzenia ciepła czy wytrzymałością mechaniczną.

Na opakowaniach płyt styropianowych oprócz wymienionych wyżej współczynników można znaleźć także jego zastosowanie. Jest to oznaczenie, które pomoże w pierwszym odrzuceniu niektórych modeli, jednak nie powinno być ono decydujące. W zależności od miejsca użycia płyt styropianowych powinny one mieć inne parametry techniczne. Poniżej znajdują się najczęstsze pola eksploatacji styropianu oparte na oznaczeniach rodzajów styropianów.

Fundament
W ten sposób oznacza się styropian polecany do izolacji fundamentów, piwnic i podłóg na gruncie. Ten rodzaj styropianu ma lepsze niż pozostałe odmiany właściwości izolacyjne i większą odporność na działanie wody. Może więc mieć bezpośredni kontakt z gruntem, bez stosowanie dodatkowych zabezpieczeń. Dlatego często stosuje się go również w dachach płaskich zielonych lub wykończonych żwirem.

Fasada
Tak opisany styropian stosowany jest do izolacji ścian zewnętrznych metodą BSO (zwaną dziś ETICS, a dawniej lekką mokrą). Ważny jest tu współczynnik przewodzenia ciepła. Im będzie on niższy, tym styropian lepiej będzie chronił przed ucieczką ciepła.

Dach
Tak oznacza się odmianę styropianu do izolacji dachów skośnych. Jest on oferowany w płytach specjalnie nacinanych, aby można je było zmieścić między krokwiami.

Oznaczony tym napisem rodzaj styropianu stosowany jest do ocieplania dachów płaskich (stropodachów). Tu ważny jest nie tylko współczynnik izolacyjności termicznej (im jest niższa, tym lepiej), ale również wytrzymałość na ściskanie (im większa, tym lepiej). Oprócz zwykłego styropianu na dachy płaskie poleca się również styropian pokryty fabrycznie papą, folią aluminiową lub powłoką bitumiczną.

Podłoga
Styropian z takim napisem stosuje się do izolacji stropów i podłóg pływających. Dlatego istotna jest jego wytrzymałość na ściskanie. Ten rodzaj styropianu może być wykończony płytami wiórowymi. Po ułożeniu tworzy gotowe podłoże na przykład pod parkiet lub płytki ceramiczne.

Na podłogi pływające przeznaczona jest specjalna odmiana styropianu. Ma ona wysoką elastyczność, dzięki której dobrze tłumi dźwięki uderzeniowe, na przykład wynikłe ze stawiania kroków.

Ocieplanie domu: jaki styropian wybrać na elewacje? Parametry przy wyborze styropianu
Kategorie: Wykończenie zewnętrzne elewacje
Ściany zewnętrzne mogą generować nawet 20% strat cieplnych w budynku. Wraz z uciekającą energią jesteśmy narażeni na dodatkowe opłaty za ogrzewanie. Najlepszym rozwiązaniem jest termoizolacja ścian, która zapewni odpowiedni komfort cieplny. W tym celu doskonale sprawdzi się styropian. Jakie parametry powinien mieć?

Wybór styropianu - budowlaniec na rusztowaniu
Jakie elementy domu można ocieplić styropianem?
Styropian jest materiałem uniwersalnym i można go stosować do izolacji większości miejsc w budynku. Najlepiej sprawdza się zastosowany jako ocieplenie:

ścian zewnętrznych – najczęściej stosowana jest tzw. metoda lekka mokra (ETICS), polegająca na położeniu na ścianach płyn styropianowych i pokryciu ich tynkiem akrylowym lub silikonowym;
fundamentów – styropian zastosowany w tym miejscu narażony jest na działania wody, wilgoci oraz uszkodzeń mechanicznych, dlatego zaleca się produkty o zwiększonej odporności i mające zwiększone parametry izolacyjne;
stropodachów – skuteczną termoizolację dachu płaskiego zagwarantuje styropian o jak najniższym współczynniku przewodzenia ciepła i zwiększonej wytrzymałości na ściskanie;
podłóg – ważniejsza od grubości styropianu w tym przypadku jest przede wszystkim odporność na duży nacisk.
Rodzaje styropianu: jaki styropian na elewację 2019?
Wyróżniamy dwa rodzaje styropianu: wersję standardową EPS i tzw. styrodur, czyli XPS. W procesie produkcji na powierzchni styropianu EPS można zauważyć charakterystyczne pory, które są naturalnym izolatorem. Porowatość sprawia jednocześnie, że ten wariant nie sprawdzi się w miejscach narażonych na działanie warunków atmosferycznych czy innych czynników zewnętrznych, np. na fundamentach. Najlepiej zaizoluje ściany, podłogi na gruncie czy stropodachy. Co istotne, styropian ma ograniczoną paroprzepuszczalność, a to pozwala stosować go pod każdego rodzaju tynkiem.

Docinanie styropianu na elewacji budynku
Unowocześnioną wersją EPS jest styropian XPS, czyli ekstrudowany. Odmienny proces produkcyjny sprawia, że ta wersja jest mniej porowata, a bardziej gęsta i jednolita. W efekcie styrodur ma lepsze parametry izolacyjne, jest odporniejszy na duży nacisk, niską temperaturę oraz wilgotność, co pozwala stosować go nie tylko na ścianach czy dachach, ale także na fundamentach, w garażach, piwnicach i na parkingach. Dlatego najbardziej trafną odpowiedzią na pytanie: co wybrać – styropian czy styrodur, będzie wcześniejsze określenie miejsca zastosowania styropianu.

Jakie parametry są najważniejsze przy wyborze styropianu?
Najistotniejszą kwestią przy wyborze styropianu jest jego współczynnik przewodzenia ciepła λ, od którego zależy grubość całej izolacji cieplneji bilans energetyczny budynku. To, na co warto zwrócić dodatkowo uwagę, to także twardość i nasiąkliwość materiału. Prawidłowo dobrany styropian dopasowany jest do rodzaju ścian oraz ich wykończenia. Dlatego, zamiast zastanawiać się, jakiej firmy styropian na ocieplenie domu wybrać, w pierwszej kolejności przeanalizujmy współczynnik λ poszczególnych produktów.

Poniżej prezentujemy zaś przykładowe średnie ceny za styropian biały:

Izolbet	Knauf	Termo Organika	Austrotherm
10 cm	54 zł/3 m²	23 zł/m²	157 zł/paczka	20 zł/m²
15 cm	50 zł/2 m²	35 zł/m²	167 zł/paczka	30 zł/m²
20 cm	150 zł/1 m²	57 zł/m²	180 zł/paczka	40 zł/m²
Czy grubość styropianu ma znaczenie? Jak gruby styropian na ocieplenie domu wybrać?
 Zgodnie z aktualnymi wytycznymi współczynnik przenikania ciepła U ścian zewnętrznych nie powinien być większy niż 0,23, a od 2021 r. – większy niż 0,20 W/(m²·K). Aby osiągnąć takie wartości, niezbędne będzie dopasowanie odpowiedniego materiału izolacyjnego.

Jak gruby styropian na ocieplenie domu będzie zatem optymalny? Przyjmuje się, że dla domów energooszczędnych i pasywnych powinien mieć ok. 20-25 cm. Jednak kwestia grubości styropianu nie ma tak dużego znaczenia jak wartość lambda. W zależności od rodzaju materiału cieńszy wariant może mieć lepszą izolacyjność niż grubszy.

Grubość styropianu: jaką wybrać do ocieplenia elewacji?
Wartość lambda dla styropianu na elewację domu
W ofertach producentów dostępne są różne wersje styropianu, których współczynnik przewodzenia ciepła lambda (λ) mieści się w granicach 0,031-0,045 W/(m.K). Im mniejsza wartość współczynnika, tym lepsze właściwości izolacyjne ma styropian. Najpopularniejszym produktem jest styropian biały, którego λ = 0,038 W/(m.K). Możemy spotkać także styropian szary (inaczej grafitowy), który jest lepszym izolatorem, ponieważ jego współczynnik przewodzenia ciepła to jedyne 0,031 W/(m.K).

Jaki styropian na elewację wybrać: biały czy grafitowy?
Kiedy stoimy przed dylematem zakupu konkretnych materiałów, musimy odpowiedzieć sobie nie tylko na pytanie, jak gruby styropian na ocieplenie domu wybrać, ale także, na jaki kolor się zdecydować.

Klejenie grafitowego styropianu na elewacji domu
Co do grubości styropianu – wiemy już, że nie zawsze idzie ona w parze z izolacyjnością cieplną. Styropian EPS dzielimy dodatkowo kolorystycznie. Wersja biała to opcja podstawowa, a szara (inaczej grafitowa, z uwagi na dodatek grafitu w procesie produkcyjnym) wyróżnia się lepszymi parametrami zarówno cieplnymi, jak i użytkowymi. Oprócz właściwości obydwa materiały różni koszt. Przykładowo cena styropianu 15 cm w kolorze szarym może być nawet kilkanaście złotych wyższa od białego. Siłą rzeczy automatycznie w tym przypadku wzrastają koszty ocieplenia domu.

Przeczytaj więcej: Czy grafit stanie się najpopularniejszym izolatorem termicznym?

Ocieplenie domu styropianem – co warto wiedzieć przed rozpoczęciem prac?
Zanim rozpoczną się prace budowlane, czeka nas nie tylko wybór materiałów, ale także dodatkowych akcesoriów i ekipy remontowej, która zajmie się ocieplaniem budynku. Warto pamiętać, że termoizolację wykonuje się bardzo rzadko, dlatego powinna być położona wyjątkowo starannie. Przed rozpoczęciem prac zdecydujmy, jaka metoda montażu zostanie użyta, jaka zaprawa zostanie zastosowana i czy materiał będzie dodatkowo mocowany na kołki do styropianu.

Grafitowy styropian na elewacji budynku
Styropian to wciąż jeden z najlepszych izolatorów termicznych. Jego właściwości oraz cena sprawiają, że znajduje on zastosowanie zarówno w budownictwie jednorodzinnym, jak i wielkogabarytowym. Aby spełniał swoje zadanie, przede wszystkim musi być dostosowany do rodzaju ściany oraz oczekiwanego współczynnika przenikania ciepła.



        UserInput":' . $request->get('`message`')
    ]];

    $data = [
        "model" => "claude-3-sonnet-20240307",
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
