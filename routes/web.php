<?php

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
    Route::group(['middleware' => 'admin'], function () {

        Route::get('/bonus', 'BonusController@index')->name('bonus.index');
        Route::post('/bonus', 'BonusController@create')->name('bonus.create');
        Route::post('/bonus/delete', 'BonusController@destroy')->name('bonus.destroy');

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

        Route::get('/getDelivererImportLog/{id}', 'DelivererController@getDelivererImportLog')->name('deliverer.getImportLog');


        Route::get('/warehouse/{id}',
            'OrdersController@getCalendar');

        Route::get('/get/user/{id}',
            'OrdersController@getUserInfo');

        Route::get('/order/task/create/',
            'TasksController@createTask');

        Route::get('users', 'UserController@index')->name('users.index');
        Route::get('users/datatable/all', 'UserController@datatable')->name('users.datatable');
        Route::get('users/create', 'UserController@create')->name('users.create');
        Route::post('users/store', 'UserController@store')->name('users.store');
        Route::get('users/{id}/editItem', 'UserController@edit')->name('users.edit');
        Route::put('users/{id}/update', [
            'uses' => 'UserController@update',
        ])->name('users.update');
        Route::delete('users-destroy/{id}/', [
            'uses' => 'UserController@destroy',
        ])->name('users.destroy');
        Route::put('users/{id}/change-status', [
            'uses' => 'UserController@changeStatus',
        ])->name('users.change.status');

        Route::get('firms', 'FirmsController@index')->name('firms.index');
        Route::get('firms/datatable', 'FirmsController@datatable')->name('firms.datatable');
        Route::get('firms/create', 'FirmsController@create')->name('firms.create');
        Route::post('firms/store', 'FirmsController@store')->name('firms.store');
        Route::get('firms/{id}/edit', 'FirmsController@edit')->name('firms.edit');
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

        Route::get('sello-import', 'OrdersController@selloImport')->name('orders.sello_import');
        Route::get('send_tracking_numbers', 'OrdersController@sendTrackingNumbers')->name('orders.send_tracking_numbers');

        Route::get('products/stocks', 'ProductStocksController@index')->name('product_stocks.index');
        Route::post('products/stocks/datatable', 'ProductStocksController@datatable')->name('product_stocks.datatable');
        Route::get('products/stocks/print', 'ProductStocksController@print')->name('product_stocks.print');
        Route::get('products/stocks/{id}/edit', 'ProductStocksController@edit')->name('product_stocks.edit');
        Route::put('products/stocks/{id}/update', 'ProductStocksController@update')->name('product_stocks.update');
        Route::put('products/stocks/{id}/change-status',
            'ProductStocksController@changeStatus')->name('product_stocks.change.status');
        Route::get('products/stocks/{id}/positions/create',
            'ProductStockPositionsController@create')->name('product_stocks.position.create');
        Route::get('products/stocks/{id}/positions/datatable',
            'ProductStockPositionsController@datatable')->name('product_stocks.position.datatable');
        Route::post('products/stocks/{id}/positions/store',
            'ProductStockPositionsController@store')->name('product_stocks.position.store');
        Route::get('products/stocks/{id}/positions/{position_id}/edit',
            'ProductStockPositionsController@edit')->name('product_stocks.position.edit');
        Route::put('products/stocks/{id}/positions/{position_id}/update',
            'ProductStockPositionsController@update')->name('product_stocks.position.update');
        Route::delete('products/stocks/{id}/positions/{position_id}',
            'ProductStockPositionsController@destroy')->name('product_stocks.position.destroy');
        Route::get('products/stocks/{id}/logs/datatable',
            'ProductStockLogsController@datatable')->name('product_stocks.logs.datatable');
        Route::get('products/stocks/{id}/logs/{log_id}/show',
            'ProductStockLogsController@show')->name('product_stocks.logs.show');
        Route::get('products/stocks/packets/create',
            'ProductStockPacketsController@create')->name('product_stock_packets.create');
        Route::post('products/stocks/packets',
            'ProductStockPacketsController@store')->name('product_stock_packets.store');
        Route::delete('products/stocks/packets/{packetId}',
            'ProductStockPacketsController@delete')->name('product_stock_packets.delete');
        Route::get('products/stocks/packets',
            'ProductStockPacketsController@index')->name('product_stock_packets.index');
        Route::post('products/stocks/packets/{packetId}/orderItem/{orderItemId}/assign',
            'Api\ProductStockPacketsController@assign')->name('product_stock_packets.assign');
        Route::post('products/stocks/packets/orderItem/{orderItemId}/retain',
            'Api\ProductStockPacketsController@retain')->name('product_stock_packets.retain');
        Route::get('products/stocks/packets/product/stock/check',
            'Api\ProductStockPacketsController@checkProductStockForPacketAssign')->name('product_stock_packets.product.stock.check');
        Route::get('products/stocks/packets/{packetId}',
            'ProductStockPacketsController@edit')->name('product_stock_packets.edit');
        Route::put('products/stocks/packets',
            'ProductStockPacketsController@update')->name('product_stock_packets.update');
        Route::post('positions/{from}/{to}/quantity/move',
            'ProductStockPositionsController@quantityMove')->name('product_stocks.quantity_move');
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::post('orders/update-notices', 'OrdersController@updateNotices')->name('orders.updateNotice');
        Route::post('orders/returnItemsFromStock',
            'OrdersController@returnItemsFromStock')->name('orders.returnItemsFromStock');
        Route::post('orders/acceptItemsToStock',
            'OrdersController@acceptItemsToStock')->name('orders.acceptItemsToStock');
        Route::post('orders/sendSelfOrderToWarehouse/{id}',
            'OrdersController@sendSelfOrderToWarehouse')->name('orders.sendSelfOrderToWarehouse');
        Route::post('orders/findPackage', 'OrdersController@findPackage')->name('orders.findPackage');
        Route::post('orders/accept-deny', 'OrdersController@acceptDeny')->name('accept-deny');
        Route::post('orders/datatable', 'OrdersController@datatable')->name('orders.datatable');
        Route::post('orders/printAll', 'OrdersController@printAll')->name('orders.printAll');
        Route::post('orders/sendVisibleCouriers', 'OrdersController@sendVisibleCouriers')->name('orders.sendVisibleCouriers');
        Route::get('orders/create', 'OrdersController@create')->name('orders.create');
        Route::get('orders/{id}/edit', 'OrdersController@edit')->name('orders.edit');
        Route::get('orders/{id}/edit/packages', 'OrdersController@editPackages')->name('orders.editPackages');
        Route::post('orders/{id}/files/add', 'OrdersController@addFile')->name('orders.fileAdd');
        Route::get('orders/{id}/files/{file_id}', 'OrdersController@getFile')->name('orders.getFile');
        Route::get('orders/files/delete/{file_id}', 'OrdersController@deleteFile')->name('orders.fileDelete');
        Route::post('orders/find-page/{id}', 'OrdersController@findPage')->name('orders.findPage');
        Route::delete('orders/{id}/', 'OrdersController@destroy')->name('orders.destroy');
        Route::put('orders/{id}/update', [
            'uses' => 'OrdersController@update',
        ])->name('orders.update');
        Route::put('orders/{id}/updateSelf', [
            'uses' => 'OrdersController@updateSelf',
        ])->name('orders.updateSelf');
        Route::get('orders/{token}/print', 'OrdersController@print')->name('orders.print');
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
        Route::get('orderPayments/create/{id}/master',
            'OrdersPaymentsController@createMaster')->name('order_payments.createMaster');
        Route::get('orderPayments/create/{id}/master/without',
            'OrdersPaymentsController@createMasterWithoutOrder')->name('order_payments.createMasterWithoutOrder');
        Route::get('payments', 'OrdersPaymentsController@payments')->name('payments.index');
        Route::get('payments/{id}/list', 'OrdersPaymentsController@paymentsEdit')->name('payments.edit');
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
        Route::post('orderPackages/duplicate/{packageId}', 'OrdersPackagesController@duplicate')->name('order_packages.duplicate');
        Route::get('orderPackages/{id}/edit', 'OrdersPackagesController@edit')->name('order_packages.edit');
        Route::put('orderPackages/{id}/update', [
            'uses' => 'OrdersPackagesController@update',
        ])->name('order_packages.update');
        Route::delete('orderPackages/{id}/', [
            'uses' => 'OrdersPackagesController@destroy',
        ])->name('order_packages.destroy');
        Route::get('orderPackages/{id}/sendRequestForCancelled',
            'OrdersPackagesController@sendRequestForCancelled')->name('order_packages.sendRequestForCancelled');
        Route::post('orderPackages/protocols',
            'OrdersPackagesController@getProtocols')->name('order_packages.getProtocols');
        Route::get('orderPackages/{courier_name}/letters',
            'OrdersPackagesController@letters')->name('order_packages.letters');
        Route::get('orderPackages/{package_id}/send',
            'OrdersPackagesController@prepareGroupPackageToSend')->name('orders.package.prepareToSend');
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

        Route::post('orders/set-warehouse-and-remove-label', 'OrdersController@setWarehouseAndLabels')->name('order.warehouse.set');

        Route::get('orders/status/{id}/message', 'OrdersController@getStatusMessage')->name('order.status.message');

        Route::get('invoice/{id}/delete', 'OrdersController@deleteInvoice')->name('order.deleteInvoice');

        Route::post('orders/detach-label',
            'LabelsController@detachLabelFromOrder')->name('orders.detachLabel');

        Route::post('orders/label-removal/{orderId}/{labelId}',
            'OrdersController@swapLabelsAfterLabelRemoval')->name('orders.label-removal');
        Route::post('orders/payment-deadline', 'OrdersController@setPaymentDeadline')->name('orders.payment-deadline');
        Route::post('orders/label-addition/{labelId}',
            'OrdersController@swapLabelsAfterLabelAddition')->name('orders.label-addition');
        Route::get('orders/products/autocomplete',
            'OrdersController@autocomplete')->name('orders.products.autocomplete');
        Route::get('orders/products/{symbol}', 'OrdersController@addProduct')->name('orders.products.add');
        Route::get('orders/{order_id}/package/{package_id}/send',
            'OrdersPackagesController@preparePackageToSend')->name('orders.package.prepareToSend');
        Route::get('orders/package/{package_id}/sticker',
            'OrdersPackagesController@getSticker')->name('orders.package.getSticker');

        Route::get('import', 'ImportController@index')->name('import.index');
        Route::post('products/stocks/changes', 'ProductStocksController@productsStocksChanges')->name('productsStocks.changes');
        Route::post('import/store', 'ImportController@store')->name('import.store');
        Route::get('store/import/{id}/{amount}', 'OrdersPaymentsController@storeFromImport');

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
                    Route::get('/{id}/edit', 'TasksController@edit')->name('edit');
                    Route::get('/{id}/delete', 'TasksController@destroy')->name('destroy');
                    Route::put('/{id}/update', 'TasksController@update')->name('destroy');
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
                    Route::post('/mark-denied/', 'TasksController@deny')->name('deny');
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
    });
});

Route::get('/dispatch-job/order-status-change', 'DispatchJobController@orderStatusChange');

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
Route::get('/chat/getUrl/{mediaId}/{postCode}/{email}/{phone}', 'MessagesController@getUrl')->name('messages.get-url');
