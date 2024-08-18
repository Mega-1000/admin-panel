<?php

use App\Entities\ChatAuctionOffer;
use App\Entities\ContactApproach;
use App\Entities\Customer;
use App\Entities\FirmSource;
use App\Entities\Product;
use App\Entities\Status;
use App\Facades\Mailer;
use App\Factory\OrderBuilderFactory;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\GetCustomerForAdminEdit;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\LocationHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPackagesCalculator;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\RecalculateBuyingLabels;
use App\Helpers\TransportSumCalculator;
use App\Http\Controllers\Api\OrderWarehouseNotificationController;
use App\Http\Controllers\AuctionsController;
use App\Http\Controllers\ContactApproachController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\FirmPanelActionsController;
use App\Http\Controllers\FirmRepresentController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OrderDatatableColumnsManagementController;
use App\Entities\OrderPackage;
use App\Http\Controllers\AddLabelsCSVController;
use App\Http\Controllers\AllegroBillingController;
use App\Http\Controllers\AllegroReturnPaymentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ConfirmProductStockOrderController;
use App\Http\Controllers\ControllSubjectInvoiceController;
use App\Http\Controllers\DeleteOrderInvoiceValueController;
use App\Http\Controllers\DifferenceInShipmentCostCookiesController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\FastResponseController;
use App\Http\Controllers\FilePermissionsController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormCreatorController;
use App\Http\Controllers\GenerateRealCostsForCompanyReportController;
use App\Http\Controllers\ImportAllegroBillingController;
use App\Http\Controllers\LowOrderQuantityAlertController;
use App\Http\Controllers\MailReportController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NewsletterMessageController;
use App\Http\Controllers\NewsletterPacketController;
use App\Http\Controllers\OrderDatatableController;
use App\Http\Controllers\OrderInvoiceDocumentsController;
use App\Http\Controllers\OrderPaymentConfirmationController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\OrdersMessagesController;
use App\Http\Controllers\OrdersPackagesController;
use App\Http\Controllers\OrderWithDeclaredPaymentsListingController;
use App\Http\Controllers\PackageProductOrderController;
use App\Http\Controllers\ProductPacketController;
use App\Http\Controllers\ProductStockLogsController;
use App\Http\Controllers\ProductStocksController;
use App\Http\Controllers\RecalculateLabelsInOrdersBasedOnPeriod;
use App\Http\Controllers\ShipmentCostFilterCookieController;
use App\Http\Controllers\ShippingPayInReportController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TableOfShipmentPaymentsErrorsController;
use App\Http\Middleware\FilterOrderInvoiceValue;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\OrderStatusChangedNotificationJob;
use App\Jobs\ReferFriendNotificationJob;
use App\Mail\NewStyroOfferMade;
use App\Services\MessageService;
use App\Services\OrderAddressesService;
use App\Services\ProductService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

use Illuminate\Http\Request;
use App\Entities\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use function App\Jobs\array_flatten;


Debugbar::disable();
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::redirect('/', '/admin');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::prefix('jobs')->group(function () {
        Route::queueMonitor();
    });
    Route::post('orderPackages/duplicate/{packageId}', 'OrdersPackagesController@duplicate')->name('order_packages.duplicate');

    Route::group(['middleware' => 'admin'], function () {
        Route::post('shipmentCostFilterCookie', ShipmentCostFilterCookieController::class)->name('shipmentCostFilterCookie');
        Route::get('/allegro-api/auth/device/{code?}', 'AllegroApiController@auth_device');
        Route::get('/allegro-api/auth/oauth2', 'AllegroApiController@auth_oauth2');

        Route::get('test1', function () {
            define('CLIENT_ID', '972fc1b48a054003a6c14575d73e2d8b'); // wprowadź Client_ID aplikacji
            define('CLIENT_SECRET', 'cArroPf68VMQquvQk3x5z1SbGWaeZOxE7vB6FBb2HIM6JXZPoEvVYI4J66tCxRCO'); // wprowadź Client_Secret aplikacji

            function getCurl($headers, $url, $content = null)
            {
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_RETURNTRANSFER => true
                ));
                if ($content !== null) {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
                }
                return $ch;
            }

            function getAccessToken()
            {
                $authorization = base64_encode(CLIENT_ID . ':' . CLIENT_SECRET);
                $headers = array("Authorization: Basic {$authorization}", "Content-Type: application/x-www-form-urlencoded");
                $content = "grant_type=client_credentials";
                $url = "https://allegro.pl/auth/oauth/token";
                $ch = getCurl($headers, $url, $content);
                $tokenResult = curl_exec($ch);
                $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                echo $tokenResult . 'okej';

                if ($tokenResult === false || $resultCode !== 200) {
                    exit ("Something went wrong");
                }
                return json_decode($tokenResult)->access_token;
            }

            function main()
            {
                echo "access_token = ", getAccessToken();
            }

            main();
        });

        Route::group(['prefix' => 'products'], function () {
            Route::group(['prefix' => 'sets', 'as' => 'sets.'], __DIR__ . '/web/ProductsSetsRoutes.php');
        });

        Route::post('/checkChatsNeedIntervention', 'OrdersController@checkChatsNeedIntervention')->name('checkChatsNeedIntervention');
        Route::post('/getChatDisputes', 'OrdersController@getChatDisputes')->name('getChatDisputes');
        Route::post('/resolveOrderDispute/{order}', 'OrdersController@resolveOrderDispute')->name('resolveOrderDispute');
        Route::post('/resolveChatIntervention/{chatId}', 'OrdersController@resolveChatIntervention')->name('resolveChatIntervention');

        Route::get('/disputes', 'AllegroDisputeController@list');
        Route::get('/disputes/view/{id}', 'AllegroDisputeController@view');
        Route::post('/disputes/send/{id}', 'AllegroDisputeController@sendMessage');
        Route::get('/disputes/attachment/{url}', 'AllegroDisputeController@getAttachment');

        Route::get('/bonus', 'BonusController@index')->name('bonus.index');
        Route::post('/bonus', 'BonusController@create')->name('bonus.create');
        Route::post('/bonus/delete', 'BonusController@destroy')->name('bonus.destroy');
        Route::get('/bonus/users/{id?}', 'BonusController@getResponsibleUsers')->name('bonus.users');
        Route::get('/bonus/chat/{id}', 'BonusController@getChat')->name('bonus.chat');
        Route::get('/bonus/close/{id}', 'BonusController@resolve')->name('bonus.close');
        Route::get('/bonus/order-chat/{id}', 'BonusController@firstOrderChat')->name('bonus.order-chat');
        Route::post('/bonus/send/{id}', 'BonusController@sendMessage')->name('bonus.send_message');

        Route::get('/newsletter-messages/create', [NewsletterMessageController::class, 'create'])->name('newsletter_messages.create');
        Route::get('/order-with-declared-payments-listing', OrderWithDeclaredPaymentsListingController::class)->name('order-with-declared-payments-listing');

        Route::get('prices/allegro-prices/{id}', 'ProductPricesController@getAllegroPrices')->name('prices.allegroPrices');
        Route::get('orders/{id}/get-basket', 'OrdersController@goToBasket')->name('orders.goToBasket');
        Route::get('/pages/content/delete', 'PagesGeneratorController@deleteContent')->name('pages.deleteContent');
        Route::get('/pages/{id}/content/edit', 'PagesGeneratorController@editContent')->name('pages.editContent');
        Route::get('/pages/{id}/content/new', 'PagesGeneratorController@newContent')->name('pages.newContent');
        Route::post('/pages/{id}/content/store', 'PagesGeneratorController@storeContent')->name('pages.saveContent');
        Route::get('/pages/{id}/content', 'PagesGeneratorController@contentList')->name('pages.list');

        Route::get('/pages/new', 'PagesGeneratorController@createPage')->name('pages.create');
        Route::post('/pages/store', 'PagesGeneratorController@store')->name('pages.store');
        Route::get('/pages/{id}/delete', 'PagesGeneratorController@delete')->name('pages.delete');
        Route::get('/pages/{id}', 'PagesGeneratorController@edit')->name('pages.edit');
        Route::get('/pages', 'PagesGeneratorController@getPages')->name('pages.index');
        Route::get('/getAllChats/{currentPage?}', 'AllegroChatController@getAllChats')->name('pages.getAllChats');

        Route::get('/getDelivererImportLog/{id}', 'DelivererController@getDelivererImportLog')->name('deliverer.getImportLog');

        Route::get('/warehouse/{warehouse}', 'OrdersController@getCalendar')->name('orders.get-calendar');

        Route::get('/get/user/{id}',
            'OrdersController@getUserInfo');

        Route::get('/order/task/create/',
            'TasksController@createTask');

        Route::get('/quick-order', 'OrdersController@createQuickOrder');
        Route::get('/store-quick-order', 'OrdersController@storeQuickOrder');

        Route::get('users', 'UserController@index')->name('users.index');
        Route::get('users/datatable/all', 'UserController@datatable')->name('users.datatable');
        Route::get('users/create', 'UserController@create')->name('users.create');
        Route::post('users/store', 'UserController@store')->name('users.store');
        Route::get('users/{id}/editItem', 'UserController@edit')->name('users.edit');
        Route::put('users/{id}/update', ['uses' => 'UserController@update',])->name('users.update');
        Route::delete('users-destroy/{id}/', [
            'uses' => 'UserController@destroy',
        ])->name('users.destroy');
        Route::put('users/{id}/change-status', [
            'uses' => 'UserController@changeStatus',
        ])->name('users.change.status');

        Route::get('calc_real_cost_for_company_sum', function () {
            $orderPackage = OrderPackage::all();

            foreach ($orderPackage as $package) {
                $package->update([
                    'real_cost_for_company_sum' => $package->realCostsForCompany->sum('cost')
                ]);

                echo $package;
            }
        });

        Route::get('firms', 'FirmsController@index')->name('firms.index');
        Route::get('firms/datatable', 'FirmsController@datatable')->name('firms.datatable');
        Route::get('firms/create', 'FirmsController@create')->name('firms.create');
        Route::post('firms/store', 'FirmsController@store')->name('firms.store');
        Route::get('firms/{firm}/edit', 'FirmsController@edit')->name('firms.edit');
        Route::put('firms/{id}/update', [
            'uses' => 'FirmsController@update',
        ])->name('firms.update');
        Route::delete('firms/{id}/', [
            'uses' => 'FirmsController@destroy',
        ])->name('firms.destroy');
        Route::put('firms/{id}/change-status', [
            'uses' => 'FirmsController@changeStatus',
        ])->name('firms.change.status');
        Route::get('firms/{id}/sendRequestToUpdateFirmData',
            'FirmsController@sendRequestToUpdateFirmData')->name('firms.sendRequestToUpdateFirmData');

        Route::get('warehouses/datatable/{id}', 'WarehousesController@datatable')->name('warehouses.datatable');
        Route::get('warehouses/create/{firm_id}', 'WarehousesController@create')->name('warehouses.create');
        Route::post('warehouses/store/{firm_id}', 'WarehousesController@store')->name('warehouses.store');
        Route::get('warehouses/{id}/edit', 'WarehousesController@edit')->name('warehouses.edit');
        Route::put('warehouses/{id}/update', [
            'uses' => 'WarehousesController@update',
        ])->name('warehouses.update');
        Route::delete('warehouses/{id}/', [
            'uses' => 'WarehousesController@destroy',
        ])->name('warehouses.destroy');
        Route::put('warehouses/{id}/change-status', [
            'uses' => 'WarehousesController@changeStatus',
        ])->name('warehouses.change.status');
        Route::get('warehouses/search/autocomplete', 'WarehousesController@autocomplete');
        Route::get('warehouses/{symbol}/editBySymbol', 'WarehousesController@editBySymbol');

        Route::get('warehouse/orders/new', 'WarehouseOrdersController@index')->name('warehouse.orders.index');
        Route::post('warehouse/orders/datatable',
            'WarehouseOrdersController@datatable')->name('warehouse.orders.datatable');
        Route::post('warehouse/orders/datatable/all',
            'WarehouseOrdersController@datatableAll')->name('warehouse.orders.datatable.all');
        Route::post('warehouse/orders/makeOrder',
            'WarehouseOrdersController@makeOrder')->name('warehouse.orders.makeOrder');
        Route::get('warehouse/orders/{id}/edit', 'WarehouseOrdersController@edit')->name('warehouse.orders.edit');
        Route::put('warehouse/orders/{id}/update', 'WarehouseOrdersController@update')->name('warehouse.orders.update');
        Route::get('warehouse/orders', 'WarehouseOrdersController@all')->name('warehouse.orders.all');
        Route::post('warehouse/orders/sendEmail',
            'WarehouseOrdersController@sendEmail')->name('warehouse.orders.sendEmail');

        Route::get('employees/datatable/{id}', 'EmployeesController@datatable')->name('employees.datatable');
        Route::get('employees/create/{firm_id}', 'EmployeesController@create')->name('employees.create');
        Route::post('employees/store/{firm_id}', 'EmployeesController@store')->name('employees.store');
        Route::get('employees/{id}/edit', 'EmployeesController@edit')->name('employees.edit');
        Route::post('send-email-about-required-prices-update/{id}', [EmployeesController::class, 'requestNewPrices'])->name('employees.request-new-prices');
        Route::put('employees/{id}/update', [
            'uses' => 'EmployeesController@update',
        ])->name('employees.update');
        Route::delete('employees/{id}/', [
            'uses' => 'EmployeesController@destroy',
        ])->name('employees.destroy');
        Route::put('employees/{id}/change-status', [
            'uses' => 'EmployeesController@changeStatus',
        ])->name('employees.change.status');

        Route::get('statuses', 'StatusesController@index')->name('statuses.index');
        Route::get('statuses/datatable/', 'StatusesController@datatable')->name('statuses.datatable');
        Route::get('statuses/create/', 'StatusesController@create')->name('statuses.create');
        Route::post('statuses/store/', 'StatusesController@store')->name('statuses.store');
        Route::get('statuses/{id}/edit', 'StatusesController@edit')->name('statuses.edit');
        Route::put('statuses/{id}/update', [
            'uses' => 'StatusesController@update',
        ])->name('statuses.update');
        Route::delete('statuses/{id}/', [
            'uses' => 'StatusesController@destroy',
        ])->name('statuses.destroy');
        Route::put('statuses/{id}/change-status', [
            'uses' => 'StatusesController@changeStatus',
        ])->name('statuses.change.status');

        Route::get('labels', 'LabelsController@index')->name('labels.index');
        Route::get('labels/datatable/', 'LabelsController@datatable')->name('labels.datatable');
        Route::get('labels/create/', 'LabelsController@create')->name('labels.create');
        Route::post('labels/store/', 'LabelsController@store')->name('labels.store');
        Route::get('labels/{id}/edit', 'LabelsController@edit')->name('labels.edit');
        Route::put('labels/{id}/update', [
            'uses' => 'LabelsController@update',
        ])->name('labels.update');
        Route::delete('labels/{id}/', [
            'uses' => 'LabelsController@destroy',
        ])->name('labels.destroy');
        Route::put('labels/{id}/change-status', [
            'uses' => 'LabelsController@changeStatus',
        ])->name('labels.change.status');
        Route::get('labels/{id}/associated-labels-to-add-after-removal',
            'LabelsController@associatedLabelsToAddAfterRemoval')->name('labels.associatedLabelsToAddAfterRemoval');

        Route::get('label-groups', 'LabelGroupsController@index')->name('label_groups.index');
        Route::get('label-groups/datatable/', 'LabelGroupsController@datatable')->name('label_groups.datatable');
        Route::get('label-groups/create/', 'LabelGroupsController@create')->name('label_groups.create');
        Route::post('label-groups/store/', 'LabelGroupsController@store')->name('label_groups.store');
        Route::get('label-groups/{id}/edit', 'LabelGroupsController@edit')->name('label_groups.edit');
        Route::put('label-groups/{id}/update', [
            'uses' => 'LabelGroupsController@update',
        ])->name('label_groups.update');
        Route::delete('label-groups/{id}/', [
            'uses' => 'LabelGroupsController@destroy',
        ])->name('label_groups.destroy');

        Route::get('customers', 'CustomersController@index')->name('customers.index');
        Route::get('customers/datatable', 'CustomersController@datatable')->name('customers.datatable');
        Route::get('customers/create', 'CustomersController@create')->name('customers.create');
        Route::post('customers/store', 'CustomersController@store')->name('customers.store');
        Route::get('customers/{id}/edit', 'CustomersController@edit')->name('customers.edit');
        Route::put('customers/{id}/update', 'CustomersController@update')->name('customers.update');
        Route::post('customers/{id}/override-customer-data', 'CustomersController@changeLoginOrPassword')->name('customers.change.login-or-password');
        Route::delete('customers/{id}/', 'CustomersController@destroy')->name('customers.destroy');
        Route::put('customers/{id}/change-status', 'CustomersController@changeStatus')->name('customers.change.status');

        Route::get('packageTemplates', 'PackageTemplatesController@index')->name('package_templates.index');
        Route::get('packageTemplates/datatable', 'PackageTemplatesController@datatable')->name('package_templates.datatable');
        Route::get('packageTemplates/create', 'PackageTemplatesController@create')->name('package_templates.create');
        Route::post('packageTemplates/store', 'PackageTemplatesController@store')->name('package_templates.store');
        Route::get('packageTemplates/{id}/edit', 'PackageTemplatesController@edit')->name('package_templates.edit');
        Route::put('packageTemplates/{id}/update', 'PackageTemplatesController@update')->name('package_templates.update');
        Route::delete('packageTemplates/{id}/delete', 'PackageTemplatesController@destroy')->name('package_templates.destroy');
        Route::get('packageTemplate/{id}/data', 'PackageTemplatesController@getPackageTemplate')->name('package_templates.getPackageTemplate');

        Route::get('employeeRoles', 'EmployeeRoleController@index')->name('employee_role.index');
        Route::get('employeeRoles/datatable', 'EmployeeRoleController@datatable')->name('employee_role.datatable');
        Route::get('employeeRoles/create', 'EmployeeRoleController@create')->name('employee_role.create');
        Route::post('employeeRoles/store', 'EmployeeRoleController@store')->name('employee_role.store');
        Route::get('employeeRoles/{id}/edit', 'EmployeeRoleController@edit')->name('employee_role.edit');
        Route::put('employeeRoles/{id}/update', 'EmployeeRoleController@update')->name('employee_role.update');
        Route::delete('employeeRoles/{id}/delete', 'EmployeeRoleController@destroy')->name('employee_role.destroy');

        Route::get('contentTypes', 'ContentTypesController@index')->name('content_type.index');
        Route::get('contentTypes/create', 'ContentTypesController@create')->name('content_type.create');
        Route::post('contentTypes/store', 'ContentTypesController@store')->name('content_type.store');
        Route::get('contentTypes/{id}/edit', 'ContentTypesController@edit')->name('content_type.edit');
        Route::put('contentTypes/{id}/update', 'ContentTypesController@update')->name('content_type.update');
        Route::delete('contentTypes/{id}/delete', 'ContentTypesController@destroy')->name('content_type.destroy');

        Route::get('containerTypes', 'ContainerTypesController@index')->name('container_type.index');
        Route::get('containerTypes/create', 'ContainerTypesController@create')->name('container_type.create');
        Route::post('containerTypes/store', 'ContainerTypesController@store')->name('container_type.store');
        Route::get('containerTypes/{id}/edit', 'ContainerTypesController@edit')->name('container_type.edit');
        Route::put('containerTypes/{id}/update', 'ContainerTypesController@update')->name('container_type.update');
        Route::delete('containerTypes/{id}/delete', 'ContainerTypesController@destroy')->name('container_type.destroy');

        Route::get('packingTypes', 'PackingTypesController@index')->name('packing_type.index');
        Route::get('packingTypes/create', 'PackingTypesController@create')->name('packing_type.create');
        Route::post('packingTypes/store', 'PackingTypesController@store')->name('packing_type.store');
        Route::get('packingTypes/{id}/edit', 'PackingTypesController@edit')->name('packing_type.edit');
        Route::put('packingTypes/{id}/update', 'PackingTypesController@update')->name('packing_type.update');
        Route::delete('packingTypes/{id}/delete', 'PackingTypesController@destroy')->name('packing_type.destroy');

        Route::get('generate-fs', 'OrdersController@generateFS')->name('orders.fs');
        Route::get('generate-advanced-invoices', 'OrdersController@generateAdvanced');

        Route::post('/add-additional-info/{id}', 'OrdersController@addAdditionalInfo')->name('orders.addAdditionalInfo');
        Route::get('sello-import', 'OrdersController@selloImport')->name('orders.sello_import');
        Route::get('send_tracking_numbers', 'OrdersController@sendTrackingNumbers')->name('orders.send_tracking_numbers');

        Route::get('do-action', function () {
            $order = Order::whereHas('labels', function ($q) {
                $q->where('label_id', 265);
            })->orWhereHas('labels', function ($q) {
                $q->where('label_id', 55);
            })
                ->orWhereHas('labels', function ($q) {
                    $q->where('label_id', 95);
                })
                ->where(function ($query) {
                    $query->where('not_able_to_handle_users', 'not like', '%' . auth()->user()->id . '%')
                        ->orWhereNull('not_able_to_handle_users');
                })
                ->first();

            if (!$order) {
                return view('aproaches-index', [
                    'items' => ContactApproach::where(function($query) {
                        $query->where('from_date', '>', now())
                            ->orWhereNull('from_date');
                    })->where('done', false)->get()
                    ]
                );
            }

            return view('do_action', compact('order'));
        })->name('orders.do_action');

        Route::group(['as' => ''], __DIR__ . '/web/ProductStocksRoutes.php');

        Route::post('differenceInShipmentCostCookies', DifferenceInShipmentCostCookiesController::class)->name('differenceInShipmentCostCookies');
        //addUsersFromCompanyToChat
        Route::post('addUsersFromCompanyToChat/{chat}', [MessagesController::class, 'addUsersFromCompanyToChat'])->name('addUsersFromCompanyToChat');
        Route::post('addUsersFromCompanyToAuction/{chat}', [MessagesController::class, 'addUsersFromCompanyToAuction'])->name('addUsersFromCompanyToAuction');

        Route::get('shipping-payin-report', ShippingPayinReportController::class)->name('shipping-payin-report');

        Route::post('positions/{from}/{to}/quantity/move',
            'ProductStockPositionsController@quantityMove')->name('product_stocks.quantity_move');
        Route::get('products/analyzer', 'ProductAnalyzerController@index')->name('product_analyzer.index');
        Route::post('products/analyzer/datatable', 'ProductAnalyzerController@datatable')->name('product_analyzer.datatable');

        Route::get('orders', 'OrdersController@index');
        Route::post('orders/update-notices', 'OrdersController@updateNotices')->name('orders.updateNotice');
        Route::post('orders/returnItemsFromStock',
            'OrdersController@returnItemsFromStock')->name('orders.returnItemsFromStock');
        Route::post('orders/acceptItemsToStock',
            'OrdersController@acceptItemsToStock')->name('orders.acceptItemsToStock');
        Route::post('orders/sendSelfOrderToWarehouse/{id}',
            'OrdersController@sendSelfOrderToWarehouse')->name('orders.sendSelfOrderToWarehouse');
        Route::post('orders/findPackage', 'OrdersController@findPackage')->name('orders.findPackage');
        Route::post('orders/findPackageAuto', 'OrdersController@findPackageAuto')->name('orders.findPackageAuto');
        Route::post('orders/accept-deny', 'OrdersController@acceptDeny')->name('accept-deny');
        Route::post('orders/change-limits', 'OrdersController@changeOrderLimits')->name('change.order.limit');
        Route::post('orders/datatable', 'OrdersController@datatable')->name('orders.datatable');
        Route::post('orders/printAll', 'OrdersController@printAll')->name('orders.printAll');
        Route::post('orders/sendVisibleCouriers', 'OrdersController@sendVisibleCouriers')->name('orders.sendVisibleCouriers');
        Route::get('orders/create', 'OrdersController@create')->name('orders.create');
        Route::get('orders/{order_id}/edit', 'OrdersController@edit')->name('orders.edit');
        Route::get('orders/{id}/edit/packages', 'OrdersController@editPackages')->name('orders.editPackages');
        Route::post('orders/{id}/files/add', 'OrdersController@addFile')->name('orders.fileAdd');
        Route::get('orders/{id}/files/{file_id}', 'OrdersController@getFile')->name('orders.getFile');
        Route::get('orders/files/delete/{file_id}', 'OrdersController@deleteFile')->name('orders.fileDelete');
        Route::post('orders/find-page/{id}', 'OrdersController@findPage')->name('orders.findPage');
        Route::post('orders/find-by-dates', 'OrdersController@findByDates')->name('orders.findByDates');
        Route::delete('orders/{id}/', 'OrdersController@destroy')->name('orders.destroy');
        Route::put('orders/{id}/update', [
            'uses' => 'OrdersController@update',
        ])->name('orders.update');
        Route::delete('orders/{id}/update', [
            'uses' => 'OrdersController@update',
        ]);
        Route::put('orders/{id}/updateSelf', [
            'uses' => 'OrdersController@updateSelf',
        ])->name('orders.updateSelf');
        Route::get('orders/{token}/print', 'OrdersController@print')->name('orders.print');
        Route::get('orders/invoice-value-delete/{id}', DeleteOrderInvoiceValueController::class)->name('orders.invoice-value');


        Route::get('orderReturn/{order_id}', 'OrderReturnController@index')->name('order_return.index');
        Route::put('orderReturn/{id}/store', ['uses' => 'OrderReturnController@store'])->name('order_return.store');
        Route::get('orderReturn/{id}/print', 'OrderReturnController@print')->name('order_return.print');
        Route::get('orderReturn/{id}/image', 'OrderReturnController@getImgFullScreen')->name('order_return.image');

        Route::post('orders/splitOrders', 'OrdersController@splitOrders');
        Route::get('orders/{orderIdToGet}/data/{orderIdToSend}/move',
            'OrdersController@moveData')->name('orders.moveData');
        Route::post('orders/{orderIdToGet}/data/{orderIdToSend}/payment/move',
            'OrdersController@movePaymentData')->name('orders.movePaymentData');
        Route::post('orders/{orderId}/surplus/move',
            'OrdersController@moveSurplus')->name('orders.moveSurplus');
        Route::get('orders/{id}/getDataFromLastOrder',
            'OrdersController@getDataFromLastOrder')->name('orders.getDataFromLastOrder');
        Route::get('orders/{id}/getDataFromCustomer',
            'OrdersController@getDataFromCustomer')->name('orders.getDataFromCustomer');
        Route::get('orders/{id}/getDataFromFirm/{firm_symbol}',
            'OrdersController@getDataFromFirm')->name('orders.getDataFromFirm');
        Route::get('orders/{id}/sendOfferToCustomer',
            'OrdersController@sendOfferToCustomer')->name('orders.sendOfferToCustomer');
        Route::get('orders/getCosts', 'OrdersController@getCosts')->name('orders.getCosts');
        Route::post('orders/invoice/request', 'OrdersController@invoiceRequest')->name('orders.invoiceRequest');
        Route::get('orders/{id}/invoices', 'OrdersController@getInvoices')->name('orders.getInvoices');
        Route::patch('orders/invoice/{id}/visibility', 'Api\InvoicesController@changeInvoiceVisibility')->name('orders.changeInvoiceVisibility');
        Route::get('orders/{id}/files', 'OrdersController@getFiles')->name('orders.getFiles');
        Route::post('orders/allegro-payment', 'OrdersPaymentsController@payAllegro')->name('orders.allegroPayments');
        Route::post('orders/allegro-commission', 'AllegroController@setCommission')->name('orders.allegroCommission');
        Route::post('orders/allegro-new-letter', 'AllegroController@createNewLetter')->name('orders.newLettersFromAllegro');
        Route::post('orders/allegro-new-order', 'AllegroController@createNewOrder')->name('orders.newOrdersFromAllegroComissions');
        Route::post('orders/create-payments', 'OrdersController@createPayments')->name('orders.create-payments');
        Route::post('orders/generate-allegro-payments', 'OrdersController@downloadAllegroPaymentsExcel')->name('orders.generate-allegro-orders');
        Route::post('orders/surplus/return', 'OrdersPaymentsController@returnSurplusPayment')->name('orders.returnSurplus');

        Route::get('orderPayments/datatable/{id}',
            'OrdersPaymentsController@datatable')->name('order_payments.datatable');
        Route::get('orderPayments/create/{id}', 'OrdersPaymentsController@create')->name('order_payments.create');
        //order_payments.create_return
        Route::get('orderPaymenyts/create-return/{order}', 'OrdersPaymentsController@createReturn')->name('order_payments.create_return');
        Route::post('orderPaymenyts/create-return/{order}', 'OrdersPaymentsController@storeReturn')->name('order_payments.create_return_post');
        Route::get('orderPayments/create/{id}/master',
            'OrdersPaymentsController@createMaster')->name('order_payments.createMaster');
        Route::get('orderPayments/create/{id}/master/without',
            'OrdersPaymentsController@createMasterWithoutOrder')->name('order_payments.createMasterWithoutOrder');
        Route::get('payments', 'OrdersPaymentsController@payments')->name('payments.index');
        Route::get('payments/clean', 'OrdersPaymentsController@cleanTable')->name('payments.clean');
        Route::get('payments/{id}/list', 'OrdersPaymentsController@paymentsEdit')->name('payment.index');
        Route::get('payments/{id}/delete', 'OrdersPaymentsController@paymentsDestroy')->name('payments.destroy');
        Route::get('payments/{id}/edit', 'OrdersPaymentsController@paymentsEdit')->name('payments.edit');
        Route::put('payments/{id}/update', 'OrdersPaymentsController@paymentUpdate')->name('payments.update');
        Route::post('payments/book', 'OrdersPaymentsController@bookPayment')->name('payments.book');
        Route::post('orderPayments/store', 'OrdersPaymentsController@store')->name('order_payments.store');
        Route::post('orderPayments/store/master',
            'OrdersPaymentsController@storeMaster')->name('order_payments.storeMaster');
        Route::get('orderPayments/{id}/edit', 'OrdersPaymentsController@edit')->name('order_payments.edit');
        Route::put('orderPayments/{id}/update', [
            'uses' => 'OrdersPaymentsController@update',
        ])->name('order_payments.update');
        Route::delete('orderPayments/{id}/', [
            'uses' => 'OrdersPaymentsController@destroy',
        ])->name('order_payments.destroy');

        Route::get('orderTasks/datatable/{id}', 'OrdersTasksController@datatable')->name('order_tasks.datatable');
        Route::get('orderTasks/create/{id}', 'OrdersTasksController@create')->name('order_tasks.create');
        Route::post('orderTasks/store', 'OrdersTasksController@store')->name('order_tasks.store');
        Route::get('orderTasks/{id}/edit', 'OrdersTasksController@edit')->name('order_tasks.edit');
        Route::put('orderTasks/{id}/update', [
            'uses' => 'OrdersTasksController@update',
        ])->name('order_tasks.update');
        Route::delete('orderTasks/{id}/', [
            'uses' => 'OrdersTasksController@destroy',
        ])->name('order_tasks.destroy');

        Route::get('orderPackages/datatable/{id}',
            'OrdersPackagesController@datatable')->name('order_packages.datatable');
        Route::get('orderPackages/create/{id}/{multi?}', 'OrdersPackagesController@create')->name('order_packages.create');
        Route::post('orderPackages/store', 'OrdersPackagesController@store')->name('order_packages.store');
        Route::get('orderPackages/{id}/edit', 'OrdersPackagesController@edit')->name('order_packages.edit');
        Route::put('orderPackages/{id}/update', [
            'uses' => 'OrdersPackagesController@update',
        ])->name('order_packages.update');
        Route::delete('orderPackages/{id}/', [
            'uses' => 'OrdersPackagesController@destroy',
        ])->name('order_packages.destroy');
        Route::get('orderPackages/{orderPackage}/sendRequestForCancelled',
            'OrdersPackagesController@sendRequestForCancelled')->name('order_packages.sendRequestForCancelled');
        Route::post('orderPackages/protocols',
            'OrdersPackagesController@getProtocols')->name('order_packages.getProtocols');
        Route::post('orderPackages/closeDay',
            'OrdersPackagesController@closeDay')->name('order_packages.closeDay');
        Route::post('orderPackages/closeGroup',
            'OrdersPackagesController@closeGroup')->name('order_packages.closeGroup');
        Route::get('orderPackages/{courier_name}/letters',
            'OrdersPackagesController@letters')->name('order_packages.letters');
        Route::get('orderPackages/{package_id}/send',
            'OrdersPackagesController@prepareGroupPackageToSend')->name('package.prepareToSend');
        Route::post('orderPackages/changeValue', 'OrdersPackagesController@changeValue')->name('order_packages.changeValue');
        Route::put('orderPackages/changePackageCosts', 'OrdersPackagesController@changePackageCost')->name('order_packages.changePackageCost');

        Route::get('orderMessages/datatable/{id}',
            'OrdersMessagesController@datatable')->name('order_messages.datatable');
        Route::get('orderMessages/create/{id}', 'OrdersMessagesController@create')->name('order_messages.create');
        Route::post('orderMessages/store', 'OrdersMessagesController@store')->name('order_messages.store');
        Route::get('orderMessages/{id}/edit', 'OrdersMessagesController@edit')->name('order_messages.edit');
        Route::put('orderMessages/{id}/update', [
            'uses' => 'OrdersMessagesController@update',
        ])->name('order_messages.update');
        Route::delete('orderMessages/{id}/', [
            'uses' => 'OrdersMessagesController@destroy',
        ])->name('order_messages.destroy');
        Route::get('products/getPrice', 'Api\ProductsController@getCurrentPrices')->name('products.currentPrices');


        Route::get('orders/status/{id}/message', 'OrdersController@getStatusMessage')->name('order.status.message');

        Route::get('invoice/{id}/delete', 'OrdersController@deleteInvoice')->name('order.deleteInvoice');

        Route::post('orders/detach-label',
            'LabelsController@detachLabelFromOrder')->name('orders.detachLabel');

        Route::post('orders/label-removal/{orderId}/{labelId}',
            'OrdersController@swapLabelsAfterLabelRemoval')->name('orders.label-removal');
        Route::post('orders/payment-deadline', 'OrdersController@setPaymentDeadline')->name('orders.payment-deadline');

        Route::get('get-available-warehouses-string', [OrdersController::class, 'getAvailableWarehousesString'])->name('get-available-warehouses-string');

        Route::post('orders/set-warehouse/{orderId}', 'OrdersController@setWarehouse')->name('orders.setWarehouse');

        Route::post('orders/label-addition/{labelId}',
            'OrdersController@swapLabelsAfterLabelAddition')->name('orders.label-addition');
        Route::get('orders/products/autocomplete',
            'OrdersController@autocomplete')->name('orders.products.autocomplete');
        Route::get('orders/products/{symbol}', 'OrdersController@addProduct')->name('orders.products.add');
        Route::get('orders/{order_id}/package/{package_id}/send',
            'OrdersPackagesController@preparePackageToSend')->name('orders.package.prepareToSend');
        Route::get('orders/package/{package_id}/sticker',
            'OrdersPackagesController@getSticker')->name('orders.package.getSticker');
        Route::resource('newsletter', NewsletterController::class)->names('newsletter');
        Route::post('newsletter/import', [NewsletterController::class, 'loadJson'])->name('newsletter.import');

        Route::get('orders/packages/{package}/sticker', [OrdersPackagesController::class, 'getStickerFile'])->name('orders.packages.getStickerFile');

        Route::get('import', 'ImportController@index')->name('import.index');
        Route::post('products/stocks/changes', 'ProductStocksController@productsStocksChanges')->name('productsStocks.changes');
        Route::post('import/store', 'ImportController@store')->name('import.store');
        Route::post('import/store-nexo-controller', 'ImportController@storeNexoController')->name('import.storeNexoController');
        Route::get('store/import/{id}/{amount}', 'OrdersPaymentsController@storeFromImport');
        Route::post('/controll-subject-invoice', ControllSubjectInvoiceController::class)->name('controll-subject-invoices')->middleware(FilterOrderInvoiceValue::class);

        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

        Route::prefix('columnVisibilities')->as('columnVisibilities.')
            ->group(function () {
                Route::prefix('modules')->as('modules.')
                    ->group(function () {
                        Route::delete('destroy/{id}', 'ColumnVisibilitiesController@moduleDestroy')->name('destroy');
                        Route::get('/', 'ColumnVisibilitiesController@moduleIndex')->name('index');
                        Route::get('create', 'ColumnVisibilitiesController@moduleCreate')->name('create');
                        Route::get('{id}/edit', 'ColumnVisibilitiesController@moduleEdit')->name('edit');
                        Route::get('datatable', 'ColumnVisibilitiesController@moduleDatatable')->name('datatable');
                        Route::put('{id}/update', 'ColumnVisibilitiesController@moduleUpdate')->name('update');
                        Route::post('store', 'ColumnVisibilitiesController@moduleStore')->name('store');
                        Route::prefix('{module_id}/roles')->as('roles.')
                            ->group(function () {
                                Route::get('/', 'ColumnVisibilitiesController@rolesIndex')->name('index');
                                Route::get('datatable',
                                    'ColumnVisibilitiesController@rolesDatatable')->name('datatable');
                                Route::prefix('{role_id}/visibilities')->as('visibilities.')
                                    ->group(function () {
                                        Route::get('/',
                                            'ColumnVisibilitiesController@visibilitiesIndex')->name('index');
                                        Route::get('datatable',
                                            'ColumnVisibilitiesController@visibilitiesDatatable')->name('datatable');
                                        Route::get('{id}/edit',
                                            'ColumnVisibilitiesController@visibilitiesEdit')->name('edit');
                                        Route::get('create',
                                            'ColumnVisibilitiesController@visibilitiesCreate')->name('create');
                                        Route::put('{id}/update',
                                            'ColumnVisibilitiesController@visibilitiesUpdate')->name('update');
                                        Route::post('store',
                                            'ColumnVisibilitiesController@visibilitiesStore')->name('store');
                                        Route::delete('destroy/{id}',
                                            'ColumnVisibilitiesController@visibilitiesDestroy')->name('destroy');
                                    });

                            });

                    });
            });

        Route::prefix('planning')->as('planning.')->group(function () {
            Route::prefix('timetable')->as('timetable.')
                ->group(function () {
                    Route::get('/', 'TimetablesController@index')->name('index');
                    Route::get('/{id}/getStorekeepers', 'TimetablesController@getStorekeepers')->name('getStorekeepers');
                    Route::get('/{id}/getStorekeepersToModal', 'TimetablesController@getStorekeepersToModal')->name('getStorekeepersToModal');
                });
            Route::prefix('tasks')->as('tasks.')
                ->group(function () {
                    Route::get('/', 'TasksController@index')->name('index');
                    Route::get('/user/{id}', 'TasksController@getForUser')->name('getForUser');
                    Route::get('/datatable', 'TasksController@datatable')->name('datatable');
                    Route::post('/store', 'TasksController@store')->name('store');
                    Route::get('/create', 'TasksController@create')->name('create');
                    Route::get('/getTasksWithChildren', 'TasksController@getTasksWithChildren')->name('getTasksWithChildren');
                    Route::get('/{taskId}/getChildren', 'TasksController@getChildren')->name('getChildren');
                    Route::get('/{id}/edit', 'TasksController@edit')->name('edit');
                    Route::get('/{id}/delete', 'TasksController@destroy')->name('destroy');
                    Route::put('/{id}/update', 'TasksController@update')->name('update');
                    Route::post('/addNewTask', 'TasksController@addNewTask')->name('addNewTask');
                    Route::get('/{id}/getTasks', 'TasksController@getTasks')->name('getTasks');
                    Route::put('/{id}/updateTaskTime', 'TasksController@updateTaskTime')->name('updateTaskTime');
                    Route::put('/{id}/moveTask', 'TasksController@moveTask')->name('moveTask');
                    Route::put('/{id}/updateTask', 'TasksController@updateTask')->name('updateTask');
                    Route::get('/{id}/getTask', 'TasksController@getTask')->name('getTask');
                    Route::post('/allowTaskMove/', 'TasksController@allowTaskMoveGet')->name('allowTaskMoveGet');
                    Route::get('/{id}/getTasksForUser/{user_id}', 'TasksController@getTasksForUser')->name('getTasksForUser');
                    Route::post('/acceptTask', 'TasksController@acceptTask')->name('acceptTask');
                    Route::post('/rejectTask', 'TasksController@rejectTask')->name('rejectTask');
                    Route::post('/produce', 'TasksController@produceOrders')->name('produceOrders');
                    Route::post('/produce-redirect', 'TasksController@produceOrdersRedirect')->name('produceOrdersRedirect');
                    Route::post('/produce-break-down', 'TasksController@breakDownTask')->name('breakDownTask');
                    Route::post('/mark-denied/', 'TasksController@deny')->name('deny');

                    Route::post('/adding-task-to-planner', 'TasksController@addingTaskToPlanner')->name('addingTaskToPlanner');
                    Route::post('/store/planner', 'TasksController@saveTaskToPlanner')->name('storePlanner');

                    Route::get('/{taskId}/checkQuantityInStock', 'TasksController@checkQuantityInStock')->name('checkQuantityInStock');
                    Route::get('/{orderId}/checkOrderQuantityInStock', 'TasksController@checkOrderQuantityInStock')->name('checkOrderQuantityInStock');

                });
            Route::prefix('reports')->as('reports.')
                ->group(function () {
                    Route::get('/', 'ReportsController@index')->name('index');
                    Route::get('/datatable', 'ReportsController@datatable')->name('datatable');
                    Route::get('/create', 'ReportsController@create')->name('create');
                    Route::post('/store', 'ReportsController@store')->name('store');
                    Route::get('/{id}/generateReport', 'ReportsController@generateReport')->name('generateReport');
                    Route::get('/{id}/generatePdfReport', 'ReportsController@generatePdfReport')->name('generatePdfReport');
                    Route::delete('/{id}/delete', [
                        'uses' => 'ReportsController@destroy',
                    ])->name('destroy');
                });
            Route::prefix('archive')->as('archive.')
                ->group(function () {
                    Route::get('/', 'ArchiveController@index')->name('index');
                    Route::get('/datatable', 'ArchiveController@datatable')->name('datatable');
                    Route::get('/{id}/view', 'ArchiveController@view')->name('edit');
                });
        });

        Route::prefix('invoices')->as('invoices.')
            ->group(function () {
                Route::post('/addInvoice', 'InvoicesController@addInvoice')->name('addInvoice');
                Route::get('/getSubiekt/{id}', 'InvoicesController@getSubiektInvoice')->name('subiektInvoices');
                Route::get('/get/{id}', 'InvoicesController@getInvoice')->name('getInvoice');
                Route::get('/import/payments', 'ImportPaymentsController@importPayments')->name('importPayments');
                Route::post('/import/payments', 'ImportPaymentsController@store')->name('storePaymentsPdf');
            });

        Route::post('users/workHours/', 'UserWorksController@addWorkHours')->name('users.addWorkHours');
        Route::get('actualizationPrice', 'ActualizationController@sendActualization')->name('actualizationPrice');

        Route::get('/dispatch-job/recalculate-prices', 'DispatchJobController@recalculatePrices')->name('job.recalculatePrices');
        Route::get('/dispatch-job/generate-jpgs', 'DispatchJobController@generateJpgs')->name('job.generateJpgs');
        Route::get('/chat/{all?}/{orderId?}', 'MessagesController@index')->name('chat.index');

        Route::get('/transport', 'DelivererController@list')->name('transportPayment.list');
        Route::get('/transport/create', 'DelivererController@create')->name('transportPayment.create');
        Route::post('/transport/store', 'DelivererController@store')->name('transportPayment.store');
        Route::get('/transport/edit/{delivererId}', 'DelivererController@edit')->name('transportPayment.edit');
        Route::post('/transport/update/{delivererId}', 'DelivererController@update')->name('transportPayment.update');
        Route::get('/transport/delete', 'DelivererController@delete')->name('transportPayment.delete');
        Route::post('/transport/update-pricing', 'DelivererController@updatePricing')->name('transportPayment.update_pricing');

        Route::get('/cacheClear', 'Controller@refreshCache')->name('admin.refresh');

        Route::get('/edit-allegro-terms', 'AllegroController@editTerms')->name('allegro.edit-terms');
        Route::post('/edit-allegro-terms', 'AllegroController@saveTerms')->name('allegro.update-terms');

        Route::prefix('allegro')->as('allegro.')->group(function () {
            Route::post('/getNewPendingDisputes', 'AllegroDisputeController@getNewPendingDisputes')->name('getNewPendingDisputes');
            Route::post('/bookDispute', 'AllegroDisputeController@bookDispute')->name('bookDispute');
            Route::post('/exitDispute', 'AllegroDisputeController@exitDispute')->name('exitDispute');

            Route::get('/chat', 'AllegroChatController@chatWindow')->name('chatWindow');

            Route::post('/checkUnreadedThreads', 'AllegroChatController@checkUnreadedThreads')->name('checkUnreadedThreads');
            Route::post('/bookThread', 'AllegroChatController@bookThread')->name('bookThread');
            Route::post('/getMessages/{threadId}', 'AllegroChatController@getMessages')->name('getMessages');
            Route::post('/getNewMessages/{threadId}', 'AllegroChatController@getNewMessages')->name('getNewMessages');
            Route::post('/writeNewMessage', 'AllegroChatController@writeNewMessage')->name('writeNewMessage');
            Route::post('/downloadAttachment/{attachmentId}', 'AllegroChatController@downloadAttachment')->name('downloadAttachment');
            Route::post('/exitChat/{threadId}', 'AllegroChatController@exitChat')->name('exitChat');
            Route::post('/messagesPreview/{threadId}', 'AllegroChatController@messagesPreview')->name('messagesPreview');
            Route::post('/newAttachmentDeclaration', 'AllegroChatController@newAttachmentDeclaration')->name('newAttachmentDeclaration');
            Route::post('/uploadAttachment/{attachmentId}', 'AllegroChatController@uploadAttachment')->name('uploadAttachment');

            Route::get('return-payment/{order}', [AllegroReturnPaymentController::class, 'index'])->name('returnPaymentPreview');
            Route::post('return-payment/{order}', [AllegroReturnPaymentController::class, 'store'])->name('returnPayment');
        });

        Route::resource('low-quantity-alerts', LowOrderQuantityAlertController::class)->names('low-quantity-alerts');
        Route::resource('/form-creator', FormCreatorController::class)->names('form-creator');
    });

    Route::get('/generate-real-cost-for-company-invoice-report', GenerateRealCostsForCompanyReportController::class)
        ->name('generateRealCostForCompanyInvoiceReport');
    Route::get('/create-package-product-order/{order}', [PackageProductOrderController::class, 'create'])
        ->name('createPackageProductOrder');
    Route::post('/create-package-product-order/{order}', [PackageProductOrderController::class, 'store'])
        ->name('storePackageProductOrder');

    Route::get('/delete-buying-invoice/{id}', [InvoicesController::class, 'deleteBuying'])
        ->name('deleteBuyingInvoice');

    Route::group(['prefix' => 'tracker', 'as' => 'tracker.'], __DIR__ . '/web/TrackerLogsRoutes.php');
    Route::group(['as' => 'transactions.'], __DIR__ . '/web/TransactionsRoutes.php');
    Route::group(['as' => 'workingEvents.'], __DIR__ . '/web/WorkingEventsRoutes.php');


    Route::get('/email/settings', [EmailSettingsController::class, 'index'])->name('emailSettings');
    Route::get('/email/settings/add', [EmailSettingsController::class, 'create'])->name('emailSettings.add');
    Route::post('/email/settings', [EmailSettingsController::class, 'store'])->name('emailSettings.store');
    Route::get('/email/settings/{emailSetting}/edit', [EmailSettingsController::class, 'edit'])->name('emailSettings.edit');
    Route::put('/email/settings/{emailSetting}/update', [EmailSettingsController::class, 'update'])->name('emailSettings.update');
    Route::delete('/email/settings/{emailSetting}/destroy', [EmailSettingsController::class, 'destroy'])->name('emailSettings.destroy');

    Route::prefix('courier')->as('courier.')->group(function () {
        Route::get('/', 'CourierController@index')->name('courier.index');
        Route::get('/{courier}/edit', 'CourierController@edit')->name('courier.edit');
        Route::put('/{courier}/update', 'CourierController@update')->name('courier.update');
    });

    Route::get('/transactions/rebook/{orderPayment}', 'OrdersPaymentsController@rebook')->name('orderPayments.rebook');
    Route::post('/transactions/rebook/{order}/{payment}', 'OrdersPaymentsController@rebookStore')->name('orderPayments.rebookStore');
    Route::post('/transactions/rebook/{payment}', 'OrdersPaymentsController@rebookStoreSingle')->name('orderPayments.rebookStoreSingle');
    Route::get('/transactions/recalculate-all-orders', 'OrdersPaymentsController@recalculateAllOrders')->name('orderPayments.recalculateAllOrders');
    Route::get('table-of-shipment-payments-errors', TableOfShipmentPaymentsErrorsController::class)->name('table-of-shipment-payments-errors');

    Route::post('/upload-invoice', 'InvoicesController@uploadInvoice')->name('uploadInvoice');

    Route::get('/twsu/create', [ProductStocksController::class, 'createTWSOAdminOrders'])->name('admin-order-TWSU.create');
    Route::post('/twsu/create', [ProductStocksController::class, 'storeTWSOAdminOrders']);

    Route::post('change-chat-visibility', [OrdersMessagesController::class, 'changeChatVisibility'])->name('change-chat-visibility');

    Route::post('/allegro-billing', ImportAllegroBillingController::class)->name('import-allegro-billing');
    Route::get('/allegro-billing', [AllegroBillingController::class, 'index']);

    Route::group([], __DIR__ . '/web/DiscountRoutes.php');

    Route::post('/add-labels-from-csv-file', AddLabelsCSVController::class)->name('add-labels-from-csv-file');

    Route::get('/email-reports', [MailReportController::class, 'index'])->name('email-reports.index');

    Route::get('/accept-products/{order}', [ConfirmProductStockOrderController::class, 'create']);
    Route::post('/accept-products/{order}', [ConfirmProductStockOrderController::class, 'store']);

    Route::get('/set-logs-permissions', FilePermissionsController::class)->name('set-logs-permissions');

    Route::get('complaint-index', [ComplaintController::class, 'index'])->name('complaint.index');

    Route::resource('product-packets', ProductPacketController::class)->names('product-packets');

    Route::resource('newsletter-packets', NewsletterPacketController::class)->names('newsletter-packets');

    Route::get('orderDatatable', OrderDatatableController::class)->name('orders.index');
    Route::get('orderDatatableColumnsFiltering', [OrderDatatableColumnsManagementController::class, 'index'])->name('orderDatatableColumnsFiltering');

    Route::post('recalculate-labels-in-orders-based-on-period', RecalculateLabelsInOrdersBasedOnPeriod::class)->name('recalculate-labels-in-orders-based-on-period');
    Route::get('mark-order-as-selfpickup/{order}', [OrdersController::class, 'markAsSelfPickup']);
});

Route::get('delete-invoice', 'InvoicesController@delete')->name('orders.deleteInvoice');


Route::get('/dispatch-job/order-status-change', 'DispatchJobController@orderStatusChange');

Route::get('/order-offer-pdf/{id}', 'OrderOfferController@getPdf');
Route::get('/order-proform-pdf/{id}', 'OrderOfferController@getProform');
Route::get('/dispatch-job/order-status-change', 'DispatchJobController@orderStatusChange');

Route::get('save-contact-to-driver/{order}', [OrdersController::class, 'saveContactToDriver'])->name('save-contact-to-driver');
Route::group([], __DIR__ . '/web/AuctionsRoutes.php');

Route::get('/debug', 'DebugController@index');
Route::get('/communication/{warehouseId}/{orderId}', 'OrdersMessagesController@communication');
Route::get('/communication/{orderId}', 'OrdersMessagesController@userCommunication');
Route::post('/communication/storeWarehouseMessage',
    'OrdersMessagesController@storeWarehouseMessage')->name('storeWarehouseMessage');
Route::get('/customer/{orderId}/confirmation/{invoice}', 'OrdersController@confirmCustomerInformation')->name('customerConfirmation');
Route::get('/customer/{orderId}/confirmation', 'OrdersController@confirmCustomerInformationWithoutData')->name('customerConfirmationWithoutData');
Route::post('/customer/confirmation', 'OrdersController@confirmCustomer')->name('confirmation');

Route::get('/payment/confirmation/{token}', 'OrdersPaymentsController@warehousePaymentConfirmation')->name('ordersPayment.warehousePaymentConfirmation');
Route::post('/payment/confirmation/{token}', 'OrdersPaymentsController@warehousePaymentConfirmationStore')->name('ordersPayment.warehousePaymentConfirmationStore');

Route::get('/chat/{token}', 'MessagesController@show')->name('chat.show');
Route::get('/chat-show-or-new/{orderId}/{userId}', 'MessagesController@showOrNew')->name('chat.show-or-new');
Route::get('/chat/getUrl/{mediaId}/{postCode}/{email}/{phone}', 'MessagesController@getUrl')->name('messages.details');

Route::get('shipment-groups', 'ShipmentGroupController@index')->name('shipment-groups.index');
Route::get('shipment-groups/datatable/', 'ShipmentGroupController@datatable')->name('shipment-groups.datatable');
Route::get('shipment-groups/{id}/package-datatable/', 'ShipmentGroupController@packageDatatable')->name('shipment-groups.packageDatatable');
Route::delete('shipment-groups/{id}/remove-package/{packageId}', 'ShipmentGroupController@removePackage')->name('shipment-groups.removePackage');
Route::get('shipment-groups/{id}/show', 'ShipmentGroupController@show')->name('shipment-groups.show');
Route::get('shipment-groups/{id}/print', 'ShipmentGroupController@print')->name('shipment-groups.print');
Route::get('shipment-groups/create/', 'ShipmentGroupController@create')->name('shipment-groups.create');
Route::post('shipment-groups/store/', 'ShipmentGroupController@store')->name('shipment-groups.store');
Route::get('shipment-groups/{id}/edit', 'ShipmentGroupController@edit')->name('shipment-groups.edit');
Route::put('shipment-groups/{id}/update', [
    'uses' => 'ShipmentGroupController@update',
])->name('shipment-groups.update');
Route::delete('shipment/{id}/', [
    'uses' => 'ShipmentGroupController@destroy',
])->name('shipment-groups.destroy');
Route::post('/order-invoice-documents/store', [OrderInvoiceDocumentsController::class, 'store'])->name('order-invoice-documents.store');
Route::get('/order-invoice-documents/{id}/delete', [OrderInvoiceDocumentsController::class, 'destroy'])->name('order-invoice-documents.delete');
Route::get('/fast-response/jsonIndex', [FastResponseController::class, 'jsonIndex'])->name('fast-response.jsonIndex');
Route::resource('/fast-response', FastResponseController::class)->names('fast-response');
Route::post('/fast-response/{fastResponse}/{order}/send', [FastResponseController::class, 'send'])->name('fast-response.send');
Route::post('/product-stock-logs/{productStockLog}/edit', [ProductStockLogsController::class, 'update'])->name('product-stock-logs.update');

Route::get('/form/{form:name}/{order}', [FormController::class, 'index'])->name('form');
Route::post('/form/{actionName}/{order}', [FormController::class, 'executeAction'])->name('execute-form-action');
Route::get('newsletter/generate/{category}', [NewsletterController::class, 'generate'])->name('newsletter.generate');


Route::get('polecenia', [ContactApproachController::class, 'index'])->name('contact-aproach.index');
Route::get('set-aproach-as-non-interested/{id}', [ContactApproachController::class, 'notInterested'])->name('set-approach-as-non-interested');
Route::get('set-aproach-as-interested/{id}', [ContactApproachController::class, 'interested'])->name('set-approach-as-interested');
Route::post('set-aproach-as-interested/{id}', [ContactApproachController::class, 'storeInterested']);

Route::post('create-confirmation/{id}', [OrderPaymentConfirmationController::class, 'store'])->name('store-payment-confirmation');
Route::get('create-confirmation/{id}/confirm', [OrderPaymentConfirmationController::class, 'confirm'])->name('store-payment-confirmation-confirm');

Route::delete('delete-message/{message}', [MessagesController::class, 'delete'])->name('delete-message');
Route::get('mark-chat-as-finished/{chat}', [MessagesController::class, 'markChatAsFinished'])->name('mark-chat-as-finished');

Route::get('/make-order/{firm:symbol}/{order}', [AuctionsController::class, 'makeOrder'])->name('create-order-from-auction');
Route::post('/submit-order/{order}', [AuctionsController::class, 'submitOrder']);

Route::get('/mark-as-non-represent-policy/{firm}', [FirmRepresentController::class, 'markFirmAsNonRepresentsPolicy'])->name('mark-as-non-represent-policy');
Route::get('/create-represents/{firm}/{email}', [FirmRepresentController::class, 'referRepresentative'])->name('create-represents');
Route::post('/store-represents/{firm}/{email}', [FirmRepresentController::class, 'storeRepresentatives'])->name('store-represents');

Route::get('/represents', [FirmRepresentController::class, 'index'])->name('represents.index');
Route::post('/representatives/{id}', [FirmRepresentController::class, 'create'])->name('representatives.create');
Route::delete('/representatives/{id}', [FirmRepresentController::class, 'delete'])->name('representatives.delete');

Route::get('styroLeads', [\App\Http\Controllers\StyroLeadController::class, 'index'])->name('styro-lead.index');
Route::get('styroLeads/get-tracking-img/{id}', [\App\Http\Controllers\StyroLeadController::class, 'getLogoWithTracker'])->name('styro-lead.tracking-img');
Route::post('styroLeads/import-csv', [\App\Http\Controllers\StyroLeadController::class, 'importCSV'])->name('styro-lead.load-csv');
Route::get('goto-website/{id}', [\App\Http\Controllers\StyroLeadController::class, 'visitWebsite'])->name('visit-website');
Route::get('dates-order-dates/{orderId}', [OrdersController::class, 'orderView']);
Route::get('sms/send/{token}', [SmsController::class, 'sendSms']);

Route::get('avizate-order/{order}', [OrderWarehouseNotificationController::class, 'createAvisation'])->name('createAvisation');
Route::post('avizate-order/{order}', [OrderWarehouseNotificationController::class, 'storeAvisation'])->name('storeAvisation');

Route::get('recalculate-order', function () {
    $orders = Order::whereHas('labels', function ($q) {
        $q->where('labels.id', 263);
    })->get();

    foreach ($orders as $order) {
        RecalculateBuyingLabels::recalculate($order);
    }
});

Route::get('/order/{order}/getMails', [MailReportController::class, 'getMailsByOrder'])->name('order.getMails');

Route::get('/styro-chatrs/{order}', function (Order $order) {
    $apiUrl = "https://api.anthropic.com/v1/messages";
    $apiKey = "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";
    $anthropicVersion = "2023-06-01";

    $order = Order::findOrFail($order->id); // Assume $orderId is provided

    $invoices = $order->invoices;

    $text = '';

    foreach ($invoices as $invoice) {
        $invoicePath = 'public/invoices/' . $invoice->invoice_name;

        if (!Storage::exists($invoicePath)) {
            throw new \Exception('Invoice file not found in storage.');
        }

        $invoiceContent = Storage::get($invoicePath);

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseContent($invoiceContent);
        $text .= '--------------------------' . $pdf->getText();
    }
$prompt = [
    [
        "role" => "user",
        "content" =>  [
            [
                'type' => 'text',
                'text' => $text . '
    i pasted my pdf content of all invoices attached to this order with i got from db convert it to xml format for invoice program so it will look like this

    Warning take data of ivoice witch doesnt have proforma name and is vat invoice

<?xml version="1.0"?>
<PreDokument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <Klient>
        <Typ>Firma</Typ>
        <Symbol>ARTUR</Symbol>
        <Nazwa>Kiosk ARTUR</Nazwa>
        <NazwaPelna>Kiosk ARTUR</NazwaPelna>
        <OsobaImie />
        <OsobaNazwisko />
        <NIP>836-84-63-635</NIP>
        <NIPUE />
        <Email>info@artur.insert.pl</Email>
        <Telefon>333-53-64</Telefon>
        <RodzajNaDok>Nabywca</RodzajNaDok>
        <NrRachunku>10202502-56648889798787878556</NrRachunku>
        <ChceFV>true</ChceFV>
        <AdresGlowny>
            <Nazwa>Kiosk ARTUR</Nazwa>
            <Ulica>Legnicka 57/2</Ulica>
            <Miasto>Lublin</Miasto>
            <Kod>96-534</Kod>
            <Panstwo>Polska</Panstwo>
        </AdresGlowny>
    </Klient>
    <UslugaTransportu />
    <UslugaTransportuCenaNetto>0</UslugaTransportuCenaNetto>
    <UslugaTransportuCenaBrutto>0</UslugaTransportuCenaBrutto>
    <Numer>2</Numer>
    <NumerPelny>ZK 2/SF/MAG/2017</NumerPelny>
    <NumerZewnetrzny />
    <NumerZewnetrzny2 />
    <DataUtworzenia>2017-02-15T00:00:00</DataUtworzenia>
    <DataDostawy xsi:nil="true" />
    <TerminPlatnosci>2017-02-15T00:00:00</TerminPlatnosci>
    <Produkty>
        <PrePozycja>
            <Towar>
                <Rodzaj>Towar</Rodzaj>
                <Symbol>PESO20</Symbol>
                <SymbolDostawcy />
                <NazwaDostawcy />
                <SymbolProducenta />
                <NazwaProducenta />
                <Nazwa>So perfumy 20ml</Nazwa>
                <CenaKartotekowaNetto>150</CenaKartotekowaNetto>
                <CenaNetto>300</CenaNetto>
                <JM>szt.</JM>
                <KodKreskowy>5902812179392</KodKreskowy>
                <Vat>8</Vat>
                <PKWiU />
                <Opis>Perfumy o mocnym i długotrwałym zapachu</Opis>
                <OpisPelny />
                <Uwagi />
                <AdresWWW />
                <SymboleSkladnikow />
                <IloscSkladnikow />
                <Zdjecia />
                <Wysokosc>0</Wysokosc>
                <Dlugosc>0</Dlugosc>
                <Szerokosc>0</Szerokosc>
                <Waga>0</Waga>
                <PoleWlasne />
            </Towar>
            <RabatProcent>0.0000</RabatProcent>
            <CenaNettoPrzedRabatem>270</CenaNettoPrzedRabatem>
            <CenaNettoPoRabacie>270</CenaNettoPoRabacie>
            <CenaBruttoPrzedRabatem>291.6</CenaBruttoPrzedRabatem>
            <CenaBruttoPoRabacie>291.6</CenaBruttoPoRabacie>
            <Ilosc>3</Ilosc>
            <Vat>8</Vat>
            <OpisPozycji />
            <KodDostawy />
            <WartoscCalejPozycjiNettoZRabatem>810</WartoscCalejPozycjiNettoZRabatem>
            <WartoscCalejPozycjiBruttoZRabatem>874.8</WartoscCalejPozycjiBruttoZRabatem>
            <WartoscCalejPozycjiNetto>810</WartoscCalejPozycjiNetto>
            <WartoscCalejPozycjiBrutto>874.8</WartoscCalejPozycjiBrutto>
        </PrePozycja>
    </Produkty>
    <Uwagi />
    <RodzajPlatnosci>Gotówka</RodzajPlatnosci>
    <Waluta>PLN</Waluta>
    <WartoscPoRabacieNetto>810</WartoscPoRabacieNetto>
    <WartoscPoRabacieBrutto>874.8</WartoscPoRabacieBrutto>
    <WartoscNetto>0</WartoscNetto>
    <WartoscBrutto>0</WartoscBrutto>
    <WartoscWplacona>0.0</WartoscWplacona>
    <TypDokumentu>ZK</TypDokumentu>
    <StatusDokumentuWERP />
    <Kategoria>Sprzedaż</Kategoria>
    <Magazyn>MAG</Magazyn>
    <MagazynDo />
</PreDokument>
This format

Invoice is buying and use "szt" not "szt."

Provide only xml text nbo other additional info because it is used in systsem directly
',
            ],
        ],
    ]
];
$data = [
"model" => "claude-3-5-sonnet-20240620",
"max_tokens" => 4096,
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
    $response = json_decode($response)->content[0]->text;

    $xmlStart = strpos($response, '<?xml');

    if ($xmlStart !== false) {
        // Cut everything before the XML starts
        $xmlContent = substr($response, $xmlStart);
    } else {
        // If no XML tag is found, use the entire response
        $xmlContent = $response;
    }

    $name = Str::random(32);
    Storage::put('public/buyinginvoices/' . $name . '.xml', $xmlContent);

    $order->invoice_buying_warehouse_file = 'https://admin.mega1000.pl/storage/buyinginvoices/' . $name . '.xml';
    $order->save();

    return redirect()->back();
});

Route::get('recalculate-order', function () {

})->name('recalculateOrder');

Route::get('/styro-chatrs', function () {
    $orders = Order::whereHas('items', function ($query) {
        $query->whereHas('product', function ($subQuery) {
            $subQuery->where('variation_group', 'styropiany');
        });
    })->select(
        DB::raw('DATE(created_at) as date'),
        DB::raw('COUNT(*) as total')
    )
        ->groupBy('date')
        ->orderBy('date');

    $ordersGroupedByDay = $orders->where('created_at', '>', now()->subDays(30))->get()->groupBy(function ($order) {
        return Carbon::parse($order->date)->format('Y-m-d');
    });

    $ordersGroupedByWeek = $orders->where('created_at', '>', now()->subDays(60))->get()->groupBy(function ($order) {
        return Carbon::parse($order->date)->format('Y-W');
    });

    $ordersGroupedByMonth = $orders->where('created_at', '>', now()->subDays(120))->get()->groupBy(function ($order) {
        return Carbon::parse($order->date)->format('Y-m');
    });

    $dayLabels = [];
    $dayData = [];

    foreach ($ordersGroupedByDay as $day => $dayOrders) {
        $dayLabels[] = Carbon::parse($day)->format('M d, Y');
        $dayData[] = $dayOrders->sum('total');
    }

    $weekLabels = [];
    $weekData = [];

    foreach ($ordersGroupedByWeek as $weekNumber => $weekOrders) {
        $weekLabels[] = 'Week ' . $weekNumber;
        $weekData[] = $weekOrders->sum('total');
    }

    $monthLabels = [];
    $monthData = [];

    foreach ($ordersGroupedByMonth as $month => $monthOrders) {
        $monthLabels[] = Carbon::parse($month)->format('M Y');
        $monthData[] = $monthOrders->sum('total');
    }

    return view('charts', compact('dayLabels', 'dayData', 'weekLabels', 'weekData', 'monthLabels', 'monthData'));
});

Route::get('/firm-panel-actions/{firm}', FirmPanelActionsController::class);
Route::get('/firm-panel-actions/order/{order}', [FirmPanelActionsController::class, 'show']);

Route::get('all-auctions-map', function (Request $request) {
    return view('all-auctions-map');
})->name('all-auctions-map');

Route::get('/change-products-variations/{order}/{manufacturer}', function (Order $order, string $manufacturer) {
    $orderBuilder = OrderBuilderFactory::create();
//    $order->items()->delete();
    $companies = [];

    foreach ($order->items as $product) {
        $product = Product::where('product_group', $product->product->product_group)->where('manufacturer', $manufacturer)->first();

        $productId = $product->id;
        $quantity = $product->amount;

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
})->name('change-products-variations');
