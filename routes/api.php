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

        This are styrofoams witch we have in offer. You are part of my laravel program witch sugest customer wtch styrofoam to buy For example: Response: { "message": "Dzień sobry, znalazłem takie produkty na podłogę dla ciebie!", "products": [{name: "tyron fasada EPS70 038",descripion:"tyron fasada EPS70 038 to średniej jakości styropian ale w bardzo dobrej cenie!"  }] }"
        You have to provide response only in json and in this format otherwise you will break system! Do not add any letters then json do not add also marking that this is json

        iAlways provide all of nescesary product witch you think wll be ok make sure that whole name wich you provide is full and mach one of proivided to you

        Name of styrofoam consists couple parts for example
        yetico fasada EPS70 038
        here yetico is firm name fasasa is type of styrofoam EPS70 is presure durability and 038 is lambda.

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


        UserInput":' . $request->get('`message`')
    ]];

    $data = [
        "model" => "claude-3-haiku-20240229",
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
