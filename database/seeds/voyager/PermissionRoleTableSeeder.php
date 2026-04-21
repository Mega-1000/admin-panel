<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Role::where('name', 'super_admin')->firstOrFail();
        $superAdminPermissions = Permission::all();
        $superAdmin->permissions()->sync(
            $superAdminPermissions->pluck('id')->all()
        );

        $admin = Role::where('name', 'admin')->firstOrFail();
        $adminPermissions = Permission::where('key', 'browse_admin')
            ->orWhere('table_name', 'users')
            ->orWhere('table_name', 'firms')
            ->orWhere('table_name', 'firm_addresses')
            ->orWhere('table_name', 'warehouses')
            ->orWhere('table_name', 'warehouse_addresses')
            ->orWhere('table_name', 'warehouse_properties')
            ->orWhere('table_name', 'employees')
            ->orWhere('table_name', 'statuses')
            ->orWhere('table_name', 'labels')
            ->orWhere('table_name', 'customers')
            ->orWhere('table_name', 'customer_addresses')
            ->orWhere('table_name', 'orders')
            ->orWhere('table_name', 'order_addresses')
            ->orWhere('table_name', 'order_items')
            ->orWhere('table_name', 'order_labels')
            ->orWhere('table_name', 'order_mails')
            ->orWhere('table_name', 'order_messages')
            ->orWhere('table_name', 'order_monitor_notes')
            ->orWhere('table_name', 'order_packages')
            ->orWhere('table_name', 'order_payments')
            ->orWhere('table_name', 'order_tasks')
            ->orWhere('table_name', 'order_task_employees')
            ->orWhere('table_name', 'products')
            ->orWhere('table_name', 'products_packings')
            ->orWhere('table_name', 'products_photos')
            ->orWhere('table_name', 'products_prices')
            ->orWhere('table_name', 'products_stocks')
            ->orWhere('table_name', 'products_stock_logs')
            ->orWhere('table_name', 'products_stock_positions')
            ->orWhere('table_name', 'tags');

        $admin->permissions()->sync(
            $adminPermissions->pluck('id')->all()
        );

        $accountant = Role::where('name', 'accountant')->firstOrFail();
        $accountantPermissions = Permission::where('key', 'browse_admin')
            ->orWhere('table_name', 'users')
            ->orWhere('table_name', 'firms')
            ->orWhere('table_name', 'firm_addresses')
            ->orWhere('table_name', 'warehouses')
            ->orWhere('table_name', 'warehouse_addresses')
            ->orWhere('table_name', 'warehouse_properties')
            ->orWhere('table_name', 'employees')
            ->orWhere('table_name', 'statuses')
            ->orWhere('table_name', 'labels')
            ->orWhere('table_name', 'customers')
            ->orWhere('table_name', 'customer_addresses')
            ->orWhere('table_name', 'orders')
            ->orWhere('table_name', 'order_addresses')
            ->orWhere('table_name', 'order_items')
            ->orWhere('table_name', 'order_labels')
            ->orWhere('table_name', 'order_mails')
            ->orWhere('table_name', 'order_messages')
            ->orWhere('table_name', 'order_monitor_notes')
            ->orWhere('table_name', 'order_packages')
            ->orWhere('table_name', 'order_payments')
            ->orWhere('table_name', 'order_tasks')
            ->orWhere('table_name', 'order_task_employees')
            ->orWhere('table_name', 'products')
            ->orWhere('table_name', 'products_packings')
            ->orWhere('table_name', 'products_photos')
            ->orWhere('table_name', 'products_prices')
            ->orWhere('table_name', 'products_stocks')
            ->orWhere('table_name', 'products_stock_logs')
            ->orWhere('table_name', 'products_stock_positions')
            ->orWhere('table_name', 'tags');

        $accountant->permissions()->sync(
            $accountantPermissions->pluck('id')->all()
        );

        $consultant = Role::where('name', 'consultant')->firstOrFail();
        $consultantPermissions = Permission::where('key', 'browse_admin')
            ->orWhere('table_name', 'users')
            ->orWhere('table_name', 'firms')
            ->orWhere('table_name', 'firm_addresses')
            ->orWhere('table_name', 'warehouses')
            ->orWhere('table_name', 'warehouse_addresses')
            ->orWhere('table_name', 'warehouse_properties')
            ->orWhere('table_name', 'employees')
            ->orWhere('table_name', 'statuses')
            ->orWhere('table_name', 'labels')
            ->orWhere('table_name', 'customers')
            ->orWhere('table_name', 'customer_addresses')
            ->orWhere('table_name', 'orders')
            ->orWhere('table_name', 'order_addresses')
            ->orWhere('table_name', 'order_items')
            ->orWhere('table_name', 'order_labels')
            ->orWhere('table_name', 'order_mails')
            ->orWhere('table_name', 'order_messages')
            ->orWhere('table_name', 'order_monitor_notes')
            ->orWhere('table_name', 'order_packages')
            ->orWhere('table_name', 'order_payments')
            ->orWhere('table_name', 'order_tasks')
            ->orWhere('table_name', 'order_task_employees')
            ->orWhere('table_name', 'products')
            ->orWhere('table_name', 'products_packings')
            ->orWhere('table_name', 'products_photos')
            ->orWhere('table_name', 'products_prices')
            ->orWhere('table_name', 'products_stocks')
            ->orWhere('table_name', 'products_stock_logs')
            ->orWhere('table_name', 'products_stock_positions')
            ->orWhere('table_name', 'tags');

        $consultant->permissions()->sync(
            $consultantPermissions->pluck('id')->all()
        );

        $storekeeper = Role::where('name', 'storekeeper')->firstOrFail();
        $storekeeperPermissions = Permission::where('key', 'browse_admin')
            ->orWhere('table_name', 'users')
            ->orWhere('table_name', 'firms')
            ->orWhere('table_name', 'firm_addresses')
            ->orWhere('table_name', 'warehouses')
            ->orWhere('table_name', 'warehouse_addresses')
            ->orWhere('table_name', 'warehouse_properties')
            ->orWhere('table_name', 'employees')
            ->orWhere('table_name', 'statuses')
            ->orWhere('table_name', 'labels')
            ->orWhere('table_name', 'customers')
            ->orWhere('table_name', 'customer_addresses')
            ->orWhere('table_name', 'orders')
            ->orWhere('table_name', 'order_addresses')
            ->orWhere('table_name', 'order_items')
            ->orWhere('table_name', 'order_labels')
            ->orWhere('table_name', 'order_mails')
            ->orWhere('table_name', 'order_messages')
            ->orWhere('table_name', 'order_monitor_notes')
            ->orWhere('table_name', 'order_packages')
            ->orWhere('table_name', 'order_payments')
            ->orWhere('table_name', 'order_tasks')
            ->orWhere('table_name', 'order_task_employees')
            ->orWhere('table_name', 'products')
            ->orWhere('table_name', 'products_packings')
            ->orWhere('table_name', 'products_photos')
            ->orWhere('table_name', 'products_prices')
            ->orWhere('table_name', 'products_stocks')
            ->orWhere('table_name', 'products_stock_logs')
            ->orWhere('table_name', 'products_stock_positions')
            ->orWhere('table_name', 'tags');

        $storekeeper->permissions()->sync(
            $storekeeperPermissions->pluck('id')->all()
        );
    }
}
