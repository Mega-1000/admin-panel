<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->bind(\App\Repositories\FirmRepository::class, \App\Repositories\FirmRepositoryEloquent::class);
$app->bind(\App\Repositories\FirmAddressRepository::class, \App\Repositories\FirmAddressRepositoryEloquent::class);
$app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
$app->bind(\App\Repositories\EmployeeRepository::class, \App\Repositories\EmployeeRepositoryEloquent::class);
$app->bind(\App\Repositories\WarehouseRepository::class, \App\Repositories\WarehouseRepositoryEloquent::class);
$app->bind(\App\Repositories\WarehouseAddressRepository::class, \App\Repositories\WarehouseAddressRepositoryEloquent::class);
$app->bind(\App\Repositories\WarehousePropertyRepository::class, \App\Repositories\WarehousePropertyRepositoryEloquent::class);
$app->bind(\App\Repositories\LabelRepository::class, \App\Repositories\LabelRepositoryEloquent::class);
$app->bind(\App\Repositories\StatusRepository::class, \App\Repositories\StatusRepositoryEloquent::class);
$app->bind(\App\Repositories\CustomerRepository::class, \App\Repositories\CustomerRepositoryEloquent::class);
$app->bind(\App\Repositories\CustomerAddressRepository::class, \App\Repositories\CustomerAddressRepositoryEloquent::class);
$app->bind(\App\Repositories\TagRepository::class, \App\Repositories\TagRepositoryEloquent::class);
$app->bind(\App\Repositories\WarehouseOrdersRepository::class, \App\Repositories\WarehouseOrdersRepositoryEloquent::class);
$app->bind(\App\Repositories\WarehouseOrdersItemsRepository::class, \App\Repositories\WarehouseOrdersItemsRepositoryEloquent::class);
$app->bind(\App\Repositories\PaymentRepository::class, \App\Repositories\PaymentRepositoryEloquent::class);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
