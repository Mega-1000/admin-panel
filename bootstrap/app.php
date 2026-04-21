<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all the various parts.
|
*/

use App\Repositories\CustomerAddressRepository;
use App\Repositories\CustomerAddressRepositoryEloquent;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerRepositoryEloquent;
use App\Repositories\EmployeeRepository;
use App\Repositories\EmployeeRepositoryEloquent;
use App\Repositories\FirmAddressRepository;
use App\Repositories\FirmAddressRepositoryEloquent;
use App\Repositories\FirmRepository;
use App\Repositories\FirmRepositoryEloquent;
use App\Repositories\LabelRepository;
use App\Repositories\LabelRepositoryEloquent;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentRepositoryEloquent;
use App\Repositories\StatusRepository;
use App\Repositories\StatusRepositoryEloquent;
use App\Repositories\TagRepository;
use App\Repositories\TagRepositoryEloquent;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\WarehouseAddressRepository;
use App\Repositories\WarehouseAddressRepositoryEloquent;
use App\Repositories\WarehouseOrdersItemsRepository;
use App\Repositories\WarehouseOrdersItemsRepositoryEloquent;
use App\Repositories\WarehouseOrdersRepository;
use App\Repositories\WarehouseOrdersRepositoryEloquent;
use App\Repositories\WarehousePropertyRepository;
use App\Repositories\WarehousePropertyRepositoryEloquent;
use App\Repositories\WarehouseRepository;
use App\Repositories\WarehouseRepositoryEloquent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

$app->bind(FirmRepository::class, FirmRepositoryEloquent::class);
$app->bind(FirmAddressRepository::class, FirmAddressRepositoryEloquent::class);
$app->bind(UserRepository::class, UserRepositoryEloquent::class);
$app->bind(EmployeeRepository::class, EmployeeRepositoryEloquent::class);
$app->bind(WarehouseRepository::class, WarehouseRepositoryEloquent::class);
$app->bind(WarehouseAddressRepository::class, WarehouseAddressRepositoryEloquent::class);
$app->bind(WarehousePropertyRepository::class, WarehousePropertyRepositoryEloquent::class);
$app->bind(LabelRepository::class, LabelRepositoryEloquent::class);
$app->bind(StatusRepository::class, StatusRepositoryEloquent::class);
$app->bind(CustomerRepository::class, CustomerRepositoryEloquent::class);
$app->bind(CustomerAddressRepository::class, CustomerAddressRepositoryEloquent::class);
$app->bind(TagRepository::class, TagRepositoryEloquent::class);
$app->bind(WarehouseOrdersRepository::class, WarehouseOrdersRepositoryEloquent::class);
$app->bind(WarehouseOrdersItemsRepository::class, WarehouseOrdersItemsRepositoryEloquent::class);
$app->bind(PaymentRepository::class, PaymentRepositoryEloquent::class);
// TODO Wszystko podmienić na bezpośrednie klasy fasady
$app->bind('DB', DB::class);
$app->bind('Log', Log::class);
/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script, so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
