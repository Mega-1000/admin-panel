<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $keys = [
            'browse_admin',
            'browse_bread',
            'browse_database',
            'browse_media',
            'browse_compass',
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key' => $key,
                'table_name' => null,
            ]);
        }

        Permission::generateFor('menus');

        Permission::generateFor('roles');

        Permission::generateFor('users');

        Permission::generateFor('settings');

        Permission::generateFor('firms');

        Permission::generateFor('firm_addresses');

        Permission::generateFor('warehouses');

        Permission::generateFor('warehouse_addresses');

        Permission::generateFor('warehouse_properties');

        Permission::generateFor('employees');

        Permission::generateFor('statuses');

        Permission::generateFor('labels');

        Permission::generateFor('customers');

        Permission::generateFor('customer_addresses');

        Permission::generateFor('tags');

        Permission::generateFor('orders');

        Permission::generateFor('order_addresses');

        Permission::generateFor('order_items');

        Permission::generateFor('order_labels');

        Permission::generateFor('order_mails');

        Permission::generateFor('order_messages');

        Permission::generateFor('order_monitor_notes');

        Permission::generateFor('order_packages');

        Permission::generateFor('order_payments');

        Permission::generateFor('order_tasks');

        Permission::generateFor('order_task_employees');

        Permission::generateFor('products');

        Permission::generateFor('product_packings');

        Permission::generateFor('product_photos');

        Permission::generateFor('product_prices');

        Permission::generateFor('product_stocks');

        Permission::generateFor('product_stock_logs');

        Permission::generateFor('product_stock_positions');
    }
}
