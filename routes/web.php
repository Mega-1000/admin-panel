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
        Route::group(['prefix' => 'products/sets', 'as' => 'sets.'], __DIR__ . '/web/ProductsSetsRoutes.php');
        Route::group(['prefix' => 'bonus', 'as' => 'bonus.'], __DIR__ . '/web/BonusRoutes.php');
        Route::group(['prefix' => 'pages', 'as' => 'pages.'], __DIR__ . '/web/PagesRoutes.php');
        Route::group(['prefix' => 'firms', 'as' => 'firms.'], __DIR__ . '/web/FirmsRoutes.php');
        Route::group(['prefix' => 'warehouses', 'as' => 'warehouses.'], __DIR__ . '/web/WarehousesRoutes.php');
        Route::group(['prefix' => 'employees', 'as' => 'employees.'], __DIR__ . '/web/EmployeesRoutes.php');
        Route::group(['prefix' => 'statuses', 'as' => 'statuses.'], __DIR__ . '/web/StatusesRoutes.php');
        Route::group(['prefix' => 'labels', 'as' => 'labels.'], __DIR__ . '/web/LabelsRoutes.php');
        Route::group(['prefix' => 'label-groups', 'as' => 'label_groups.'], __DIR__ . '/web/LabelsRoutes.php');
        Route::group(['prefix' => 'customers', 'as' => 'customers.'], __DIR__ . '/web/CustomersRoutes.php');
        Route::group(['prefix' => 'packageTemplates', 'as' => 'package_templates.'], __DIR__ . '/web/PackageTemplatesRoutes.php');
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], __DIR__ . '/web/OrdersRoutes.php');
        Route::group(['prefix' => 'employeeRoles', 'as' => 'employee_role.'], __DIR__ . '/web/EmployeeRolesRoutes.php');

        Route::group(['prefix' => 'users', 'as' => 'users.'], __DIR__ . '/web/UsersRoutes.php');
        Route::delete('users-destroy/{id}/', ['uses' => 'UserController@destroy',])->name('users.destroy');

        //Different custom routes
        Route::get('prices/allegro-prices/{id}', 'ProductPricesController@getAllegroPrices')->name('prices.allegroPrices');
        Route::get('/getDelivererImportLog/{id}', 'DelivererController@getDelivererImportLog')->name('deliverer.getImportLog');
        Route::get('/warehouse/{id}', 'OrdersController@getCalendar');
        Route::get('/get/user/{id}', 'OrdersController@getUserInfo');
        Route::get('/order/task/create/', 'TasksController@createTask');


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
        Route::post('positions/{from}/{to}/quantity/move',
            'ProductStockPositionsController@quantityMove')->name('product_stocks.quantity_move');


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

        Route::get('/cacheClear', 'Controller@refreshCache')->name('admin.refresh');
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
