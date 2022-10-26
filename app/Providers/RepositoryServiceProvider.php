<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Repositories\FirmRepository::class, \App\Repositories\FirmRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FirmAddressRepository::class, \App\Repositories\FirmAddressRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WarehouseRepository::class, \App\Repositories\WarehouseRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EmployeeRepository::class, \App\Repositories\EmployeeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WarehouseAddressRepository::class, \App\Repositories\WarehouseAddressRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WarehousePropertyRepository::class, \App\Repositories\WarehousePropertyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\LabelRepository::class, \App\Repositories\LabelRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StatusRepository::class, \App\Repositories\StatusRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CustomerRepository::class, \App\Repositories\CustomerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CustomerAddressRepository::class, \App\Repositories\CustomerAddressRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TagRepository::class, \App\Repositories\TagRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderRepository::class, \App\Repositories\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderMessageRepository::class, \App\Repositories\OrderMessageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderTaskRepository::class, \App\Repositories\OrderTaskRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderPaymentRepository::class, \App\Repositories\OrderPaymentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderPackageRepository::class, \App\Repositories\OrderPackageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderItemRepository::class, \App\Repositories\OrderItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderMonitorNoteRepository::class, \App\Repositories\OrderMonitorNoteRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CategoryRepository::class, \App\Repositories\CategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductRepository::class, \App\Repositories\ProductRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductPhotoRepository::class, \App\Repositories\ProductPhotoRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductPriceRepository::class, \App\Repositories\ProductPriceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductPackingRepository::class, \App\Repositories\ProductPackingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductStockRepository::class, \App\Repositories\ProductStockRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductStockPositionRepository::class, \App\Repositories\ProductStockPositionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductStockRepository::class, \App\Repositories\ProductStockRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductPriceRepository::class, \App\Repositories\ProductPriceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductPhotoRepository::class, \App\Repositories\ProductPhotoRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductStockLogRepository::class, \App\Repositories\ProductStockLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderAddressRepository::class, \App\Repositories\OrderAddressRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderWarehouseNotificationRepository::class, \App\Repositories\OrderWarehouseNotificationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\LabelGroupRepository::class, \App\Repositories\LabelGroupRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserEmailRepository::class, \App\Repositories\UserEmailRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderMessageAttachmentRepository::class, \App\Repositories\OrderMessageAttachmentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderInvoiceRepository::class, \App\Repositories\OrderInvoiceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderLabelSchedulerRepository::class, \App\Repositories\OrderLabelSchedulerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderLabelSchedulerAwaitRepository::class, \App\Repositories\OrderLabelSchedulerAwaitRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TaskRepository::class, \App\Repositories\TaskRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TaskTimeRepository::class, \App\Repositories\TaskTimeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportRepository::class, \App\Repositories\ReportRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserWorkRepository::class, \App\Repositories\UserWorkRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TaskSalaryDetailsRepository::class, \App\Repositories\TaskSalaryDetailsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TransactionRepository::class, \App\Repositories\TransactionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProviderTransactionRepository::class, \App\Repositories\ProviderTransactionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SpeditionExchangeRepository::class, \App\Repositories\SpeditionExchangeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SpeditionExchangeItemRepository::class, \App\Repositories\SpeditionExchangeItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SpeditionExchangeOfferRepository::class, \App\Repositories\SpeditionExchangeOfferRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentImportRepository::class, \App\Repositories\PaymentImportRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportPropertyRepository::class, \App\Repositories\ReportPropertyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportDailyRepository::class, \App\Repositories\ReportDailyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderPaymentLogRepository::class, \App\Repositories\OrderPaymentLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductStockPacketRepository::class, \App\Repositories\ProductStockPacketRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductStockPacketItemRepository::class, \App\Repositories\ProductStockPacketItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PackageTemplateRepository::class, \App\Repositories\PackageTemplateRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FaqRepository::class, \App\Repositories\FaqRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderAllegroRepository::class, \App\Repositories\OrderAllegroRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WorkingEventRepository::class, \App\Repositories\WorkingEventRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ShipmentGroupRepository::class, \App\Repositories\ShipmentGroupRepositoryEloquent::class);
        //:end-bindings:
    }
}
