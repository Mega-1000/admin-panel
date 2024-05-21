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
