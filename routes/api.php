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
        styropianplus fasada 044 styropianplus fasada 042 styropianplus fasada 040 styropianplus fasada 033 styropianplus fasada 032 styropianplus fasada 031 styropianplus akustyczny styrmann fasada 042 styrmann fasada 040 styrmann fasada 033 styrmann fasada 032 styrmann fasada 031 styropmin fasada 042 styropmin fasada 040 styropak fasada 045 styropak fasada 042 styropak fasada 040 styropak fasada 033 styropak fasada 031 styropak akustyczny izoterm fasada 045 izoterm fasada 042 izoterm fasada 040 izoterm fasada 032 polstyr fasada 045 polstyr fasada 044 polstyr fasada 042 polstyr fasada 040 polstyr fasada 038 polstyr fasada 033 polstyr fasada 032 polstyr fasada 031 polstyr akustyczny swisspor fasada 045 swisspor fasada 042 swisspor fasada 040 termex fasada 042 termex fasada 038 knauf fasada 042 knauf fasada 040 termex fasada 045 termex fasada 040 swisspor fasada 032 termex fasada 032 knauf fasada 032 izoterm fasada EPS70 040 izoterm fasada EPS70 038 izoterm EPS80 izoterm EPS100 038 izoterm EPS200 polstyr fasada EPS70 040 polstyr fasada EPS70 038 polstyr EPS60 polstyr EPS80 polstyr EPS100 038 polstyr EPS200 polstyr wodoodporny EPS120 genderka fasada 045 genderka fasada 042 genderka fasada 040 genderka fasada 038 genderka fasada EPS70 038 genderka fasada 032 genderka EPS80 genderka EPS80 031 genderka EPS100 031 genderka EPS150 genderka EPS200 genderka wodoodporny EPS100 genderka wodoodporny EPS200 domstyr fasada 044 domstyr fasada 042 domstyr fasada 040 domstyr fasada 038 domstyr fasada EPS70 040 domstyr fasada 032 domstyr EPS60 domstyr EPS100 038 domstyr EPS100 031 domstyr EPS150 domstyr EPS200 domstyr wodoodporny EPS100 domstyr wodoodporny EPS200 domstyr wodoodporny EPS100 wtryskarka domstyr wodoodporny EPS200wtryskarka styropmin EPS60 styropmin EPS70 styropmin EPS80 styropmin EPS80 031 styropmin EPS100 038 styropmin EPS100 031 styropmin EPS150 styropmin EPS200 styropmin wodoodporny EPS100 styropmin wodoodporny EPS100 wtryskarka izolbet fasada 044 izolbet fasada 042 izolbet fasada 040 izolbet fasada 032 izolbet EPS60 izolbet EPS80 izolbet EPS150 izolbet EPS200 izolbet wodoodporny EPS100 izolbet wodoodporny EPS100 wtryskarka austrotherm fasada 042 austrotherm fasada 040 austrotherm EPS70 austrotherm EPS80 austrotherm EPS80 031 austrotherm EPS100 038 austrotherm wodoodporny EPS120 swisspor fasada EPS70 038 swisspor EPS60 swisspor EPS60 031 swisspor EPS80 swisspor EPS80 031 swisspor EPS150 swisspor EPS200 swisspor wodoodporny EPS100 termex fasada EPS70 038 termex EPS60 termex EPS60 031 termex EPS70 termex EPS80 termex EPS80 031 termex EPS100 030 termex EPS150 termex EPS200 termex wodoodporny EPS100 knauf fasada EPS70 038 knauf EPS70 knauf EPS80 knauf EPS80 031 knauf EPS100 038 knauf EPS200 knauf wodoodporny EPS100 styropianplus EPS60 styropianplus EPS60 031 styropianplus EPS70 styropianplus EPS80 styropianplus EPS80 031 styropianplus EPS100 038 styropianplus EPS150 styropianplus EPS200 styropianplus wodoodporny EPS150 styropianplus wodoodporny EPS150 wtryskarka albaterm fasada 044 albaterm fasada 042 albaterm fasada 040 albaterm fasada EPS70 040 albaterm fasada 032 albaterm EPS60 albaterm EPS100 038 albaterm EPS200 albaterm wodoodporny EPS100 paneltech fasada 045 paneltech fasada 042 paneltech fasada 040 paneltech fasada 038 paneltech fasada 032 paneltech EPS60 paneltech EPS80 paneltech EPS80 031 paneltech EPS100 038 paneltech EPS120 paneltech EPS150 paneltech EPS200 paneltech wodoodporny EPS120 paneltech wodoodporny EPS150 paneltech wodoodporny EPS200 arsanit fasada 045 arsanit fasada 042 arsanit fasada 040 arsanit fasada 038 arsanit fasada EPS70 038 arsanit fasada 032 arsanit EPS60 arsanit EPS80 arsanit EPS120 arsanit EPS200 arsanit wodoodporny EPS100 arsanit wodoodporny EPS120 grastyr fasada 045 grastyr fasada 042 grastyr fasada 040 grastyr fasada EPS70 040 grastyr fasada 032 grastyr EPS80 grastyr EPS100 038 grastyr EPS200 grastyr wodoodporny EPS100 inthermo fasada 045 inthermo fasada 042 inthermo fasada 040 inthermo fasada EPS70 040 inthermo fasada EPS70 038 inthermo fasada 032 inthermo EPS80 inthermo EPS100 038 inthermo EPS200 inthermo wodoodporny EPS100 krasbud fasada 045 krasbud fasada 042 krasbud fasada 040 krasbud fasada EPS70 040 krasbud fasada EPS70 038 krasbud EPS60 krasbud EPS70 krasbud EPS80 krasbud EPS100 038 krasbud EPS200 krasbud wodoodporny EPS100 neotherm fasada 045 neotherm fasada 042 neotherm fasada 040 neotherm fasada EPS70 040 neotherm fasada EPS70 038 neotherm EPS60 neotherm EPS70 neotherm EPS80 neotherm EPS100 038 neotherm EPS150 neotherm EPS200 neotherm wodoodporny EPS100 neotherm wodoodporny EPS200 sonarol fasada 044 sonarol fasada 042 sonarol fasada 040 sonarol fasada EPS70 040 sonarol fasada EPS70 038 sonarol fasada 031 sonarol EPS60 sonarol EPS60 031 sonarol EPS80 sonarol EPS100 038 sonarol EPS200 sonarol wodoodporny EPS100 sonarol wodoodporny EPS150 styrhop fasada 044 styrhop fasada 040 styrhop EPS60 styrhop EPS80 styrhop EPS100 038 styrhop wodoodporny EPS120 styrmann fasada 045 styrmann fasada EPS70 040 styrmann fasada EPS70 038 styrmann EPS80 styrmann EPS100 038 styrmann EPS200 styrmann wodoodporny EPS100 styropak fasada EPS70 038 styropak fasada 032 styropak EPS60 styropak EPS80 styropak EPS80 031 styropak EPS100 038 styropak EPS150 styropak EPS200 styropak wodoodporny EPS100 tyron fasada 042 tyron fasada 040 tyron fasada 032 tyron EPS60 tyron EPS70 tyron EPS80 tyron EPS100 038 tyron EPS120 tyron EPS150 tyron EPS200 tyron wodoodporny EPS100 tyron wodoodporny EPS120 styropianex fasada 045 styropianex fasada 042 styropianex fasada 040 styropianex fasada EPS70 038 styropianex fasada 032 styropianex EPS80 styropianex EPS100 038 styropianex EPS200 styropianex wodoodporny EPS100 NTB fasada 042 NTB fasada 040 NTB fasada 038 NTB fasada EPS70 040 NTB fasada 032 NTB EPS60 NTB EPS100 038 NTB EPS200 NTB wodoodporny EPS100 NTB wodoodporny EPS150 eurotermika fasada 045 eurotermika fasada 042 eurotermika fasada 040 eurotermika EPS60 eurotermika EPS80 eurotermika EPS100 038 eurotermika EPS150 eurotermika wodoodporny EPS150 eurostyr fasada 044 eurostyr fasada 042 eurostyr fasada 040 eurostyr fasada EPS70 038 eurostyr EPS60 eurostyr wodoodporny EPS100 genderka fasada 033 genderka fasada 031 styropmin fasada 033 styropmin fasada 031 styropmin fasada 030 izolbet fasada 033 austrotherm fasada 033 austrotherm fasada 031 swisspor fasada 031 termex fasada 033 termex fasada 031 genderka akustyczny domstyr fasada 033 domstyr fasada 031 domstyr akustyczny styropmin akustyczny austrotherm akustyczny swisspor akustyczny knauf fasada 031 albaterm fasada 031 paneltech fasada 033 arsanit fasada 033 arsanit fasada 031 arsanit akustyczny krasbud fasada 031 neotherm fasada 033 neotherm fasada 031 neotherm akustyczny styrhop fasada 031 tyron fasada 033 tyron fasada 031 NTB fasada 031 eurotermika fasada 033 eurotermika fasada 031 eurostyr fasada 033 eurostyr fasada 031 lubau fasada 045 lubau fasada 042 lubau fasada 040 lubau fasada 033 lubau fasada 032 lubau fasada 031 lubau EPS60 lubau EPS70 lubau EPS80 lubau EPS100 037 lubau EPS150 lubau EPS200 sonarol fasada 033 knauf fasada 031 ETIXX knauf wodoodporny EPS100 grafit lubau fasada EPS70 038 albaterm EPS80 izolbet fasada EPS70 040 izoterm EPS100 036 swisspor EPS100 036 swisspor EPS100 030 genderka EPS100 036 austrotherm EPS150 styromap fasada 042 styromap fasada 040 styromap fasada EPS70 040 styromap fasada EPS70 038 styromap fasada 032 styromap fasada 031 styromap EPS70 neotherm EPS100 036 styrhop fasada 032 eurostyr EPS80 eurostyr EPS80 031 eurostyr EPS100 030 eurostyr EPS150 eurostyr EPS200 krasbud fasada 033 krasbud EPS70 031 krasbud EPS100 036 krasbud EPS100 031 arsanit EPS70 032 arsanit EPS100 035 izolbet fasada 038 izolbet EPS70 031 izolbet EPS100 037 izolbet wodoodporny EPS150 izolbet wodoodporny EPS150 wtryskarka yetico fasada 044 yetico fasada 042 yetico fasada 040 yetico fasada 033 yetico fasada 032 yetico fasada 031 yetico EPS60 yetico EPS60 031 yetico EPS70 yetico EPS80 yetico EPS100 036 yetico EPS100 031 yetico EPS200 yetico akustyczny yetico wodoodporny EPS100 yetico wodoodporny EPS150 yetico wodoodporny EPS200 yetico wodoodporny EPS100 031 yetico wodoodporny EPS80 031 styropianplus wodoodporny EPS100 enerpor fasada 045 enerpor fasada 040 enerpor fasada 038 enerpor fasada EPS70 038 enerpor fasada 033 enerpor fasada 031 enerpor EPS60 enerpor EPS70 enerpor EPS70 031 enerpor EPS80 enerpor EPS100 038 enerpor EPS100 036 enerpor EPS100 030 enerpor EPS200 enerpor akustyczny enerpor wodoodporny EPS100 enerpor wodoodporny EPS100 031 besser fasada 042 besser fasada 040 besser fasada 038 besser fasada 033 besser fasada 031 besser EPS60 besser EPS70 besser EPS70 031 besser EPS80 besser EPS100 036 besser EPS100 030 besser EPS200 besser wodoodporny EPS100 FWS fasada 042 FWS fasada 040 FWS fasada EPS70 040 FWS fasada EPS70 038 FWS fasada 033 FWS fasada 031 FWS fasada 030 FWS EPS60 FWS EPS70 031 FWS EPS80 FWS EPS80 031 FWS EPS100 038 FWS EPS200 FWS wodoodporny EPS100 justyr fasada 042 justyr fasada 040 justyr fasada 038 justyr fasada EPS70 040 justyr fasada EPS70 038 justyr fasada 033 justyr fasada 032 justyr fasada 031 justyr EPS60 justyr EPS70 justyr EPS80 justyr EPS80 031 justyr EPS100 038 justyr EPS100 036 justyr EPS150 justyr EPS200 justyr wodoodporny EPS100 eurostyropian fasada 044 eurostyropian fasada 042 eurostyropian fasada 040 eurostyropian fasada 038 eurostyropian fasada EPS70 040 eurostyropian fasada 033 eurostyropian fasada 032 eurostyropian fasada 031 eurostyropian EPS60 eurostyropian EPS70 eurostyropian EPS80 eurostyropian EPS100 038 eurostyropian wodoodporny EPS100 ekobud fasada 045 ekobud fasada 042 ekobud fasada 040 ekobud fasada EPS70 040 ekobud fasada 033 ekobud fasada 032 ekobud EPS60 ekobud EPS80 ekobud EPS100 036 ekobud EPS150 ekobud EPS200 ekobud wodoodporny EPS100 ekobud wodoodporny EPS150 termex fasada EPS70 040 termex fasada 030 termex EPS70 031 termex EPS100 038 termex EPS100 036 termex EPS100 035 termex EPS100 031 termex EPS120 termex akustyczny termex wodoodporny EPS120 termex wodoodporny EPS150 termex wodoodporny EPS200 krolczyk fasada 042 krolczyk fasada 040 krolczyk fasada 033 krolczyk fasada 032 krolczyk fasada 031 krolczyk EPS80 krolczyk EPS100 038 krolczyk EPS100 036 krolczyk EPS100 031 krolczyk EPS200 krolczyk wodoodporny EPS120 piotrowski fasada 040 piotrowski fasada EPS70 040 piotrowski EPS80 piotrowski EPS100 038 thermica fasada 045 thermica fasada 044 thermica fasada 042 thermica fasada 040 thermica fasada 038 thermica fasada EPS70 040 thermica fasada EPS70 038 thermica fasada 033 thermica fasada 032 thermica fasada 031 thermica fasada 030 thermica EPS60 thermica EPS60 031 thermica EPS70 thermica EPS70 031 thermica EPS80 thermica EPS80 031 thermica EPS100 038 thermica EPS100 036 thermica EPS100 035 thermica EPS100 031 thermica EPS100 030 thermica EPS120 thermica EPS150 thermica EPS200 thermica akustyczny thermica wodoodporny EPS100 thermica wodoodporny EPS120 thermica wodoodporny EPS150 thermica wodoodporny EPS200 thermica wodoodporny EPS100 wtryskarka thermica wodoodporny EPS120 wtryskarka thermica wodoodporny EPS150 wtryskarka thermica wodoodporny EPS200wtryskarka thermica wodoodporny EPS100 031 styropianplus EPS100 031 styropianplus EPS100 035 domstyr EPS80 eurostyr EPS100 036 polstyr EPS70 polstyr EPS100 036 polstyr EPS100 030 polstyr EPS150 albaterm EPS100 036 1cm austrotherm fasada EPS70 038 krolczyk fasada EPS70 040 krolczyk fasada EPS70 038 styrhop fasada EPS70 040 yetico fasada EPS70 038 tyron fasada EPS70 038 styropianplus fasada EPS70 039 styropmin fasada EPS70 038

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

        This are styrofoams witch we have in offer. You are part of my laravel program witch sugest customer wtch styrofoam to buy For example userinut: "Szukam taniego styropianu podłogowego" Response: { "message": "Dzień sobry, znalazłem takie produkty na podłogę dla ciebie!", "products": [{name: "tyron fasada EPS70 038",descripion:"tyron fasada EPS70 038 to średniej jakości styropian ale w bardzo dobrej cenie!"  }] }
        You have to provide response only in json and in this format otherwise you will break system! Do not add any letters then json do not add also marking that this is json

        Always provide all of nescesary product witch you think will be ok make sure that whole name wich you provide is full and mach one of proivided to you

        Name of styrofoam consists couple parts for example
        yetico fasada EPS70 038
        here yetico is firm name fasasa is type of styrofoam EPS70 is presure durability and 038 is lambda.


        UserInput:' . $request->get('`message`')
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

    $response = json_decode(json_decode($response)->content[0]->text);
    foreach ($response->products as &$product) {
        $product->name = $product->stock->product()
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

    return $response;
});
