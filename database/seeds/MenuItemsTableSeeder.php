<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $menu = Menu::where('name', 'admin')->firstOrFail();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('voyager::seeders.menu_items.dashboard'),
            'url' => '',
            'route' => 'voyager.dashboard',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-boat',
                'color' => null,
                'parent_id' => null,
                'order' => 1,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('users.title'),
            'url' => '',
            'route' => 'users.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-person',
                'color' => null,
                'parent_id' => null,
                'order' => 3,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('voyager::seeders.menu_items.roles'),
            'url' => '',
            'route' => 'voyager.roles.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-lock',
                'color' => null,
                'parent_id' => null,
                'order' => 2,
            ])->save();
        }

        $toolsMenuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('voyager::seeders.menu_items.tools'),
            'url' => '',
        ]);
        if (!$toolsMenuItem->exists) {
            $toolsMenuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-tools',
                'color' => null,
                'parent_id' => null,
                'order' => 9,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('voyager::seeders.menu_items.menu_builder'),
            'url' => '',
            'route' => 'voyager.menus.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-list',
                'color' => null,
                'parent_id' => $toolsMenuItem->id,
                'order' => 10,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('voyager::seeders.menu_items.compass'),
            'url' => '',
            'route' => 'voyager.compass.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-compass',
                'color' => null,
                'parent_id' => $toolsMenuItem->id,
                'order' => 12,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('voyager::seeders.menu_items.settings'),
            'url' => '',
            'route' => 'voyager.settings.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-settings',
                'color' => null,
                'parent_id' => null,
                'order' => 14,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('firms.title'),
            'url' => '',
            'route' => 'firms.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-company',
                'color' => null,
                'parent_id' => null,
                'order' => 15,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('statuses.title'),
            'url' => '',
            'route' => 'statuses.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-tag',
                'color' => null,
                'parent_id' => null,
                'order' => 16,
            ])->save();
        }

        $labelsMenuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('labels.title'),
            'url' => '',
        ]);
        if (!$labelsMenuItem->exists) {
            $labelsMenuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-character',
                'color' => null,
                'parent_id' => null,
                'order' => 16,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('labels.list'),
            'url' => '',
            'route' => 'labels.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-character',
                'color' => null,
                'parent_id' => $labelsMenuItem->id,
                'order' => 1,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('label_groups.title'),
            'url' => '',
            'route' => 'label_groups.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-character',
                'color' => null,
                'parent_id' => $labelsMenuItem->id,
                'order' => 2,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('customers.title'),
            'url' => '',
            'route' => 'customers.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-people',
                'color' => null,
                'parent_id' => null,
                'order' => 17,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('product_stocks.title'),
            'url' => '',
            'route' => 'product_stocks.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-book',
                'color' => null,
                'parent_id' => null,
                'order' => 17,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('orders.title'),
            'url' => '',
            'route' => 'orders.index',

        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-receipt',
                'color' => null,
                'parent_id' => null,
                'order' => 18,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('import.title'),
            'url' => '',
            'route' => 'import.index',

        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-move',
                'color' => null,
                'parent_id' => null,
                'order' => 20,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('warehouse_orders.title'),
            'url' => '',
            'route' => 'warehouse.orders.index',
        ]);

        MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Billing allegro',
            'url' => '',
            'route' => 'allegro-billing.index',
        ]);

        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-news',
                'color' => null,
                'parent_id' => null,
                'order' => 21,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('warehouse_orders.list'),
            'url' => '',
            'route' => 'warehouse.orders.all',

        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-browser',
                'color' => null,
                'parent_id' => null,
                'order' => 22,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Płatności',
            'url' => '',
            'route' => 'payments.index',

        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-credit-card',
                'color' => null,
                'parent_id' => null,
                'order' => 23,
            ])->save();
        }

        $planning = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Planowanie pracy',
            'url' => '',
            'route' => '',

        ]);
        if (!$planning->exists) {
            $planning->fill([
                'target' => '_self',
                'icon_class' => 'voyager-calendar',
                'color' => null,
                'parent_id' => null,
                'order' => 19,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Terminarz',
            'url' => '',
            'route' => 'planning.timetable.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-calendar',
                'color' => null,
                'parent_id' => $planning->id,
                'order' => 1,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Wszystkie zadania',
            'url' => '',
            'route' => 'planning.tasks.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-pen',
                'color' => null,
                'parent_id' => $planning->id,
                'order' => 2,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Raporty',
            'url' => '',
            'route' => 'planning.reports.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-documentation',
                'color' => null,
                'parent_id' => $planning->id,
                'order' => 3,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Archiwum',
            'url' => '',
            'route' => 'planning.archive.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-archive',
                'color' => null,
                'parent_id' => $planning->id,
                'order' => 3,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => __('reports.title'),
            'url' => '',
            'route' => 'planning.reports.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-tag',
                'color' => null,
                'parent_id' => $planning->id,
                'order' => 17,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Generator Stron',
            'url' => '',
            'route' => 'pages.index'
        ]);
        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Sprawdź wszystkie czaty',
            'url' => '',
            'route' => 'pages.getAllChats'
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-window-list',
                'color' => null,
                'parent_id' => null,
                'order' => 24,
            ])->save();
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => 'Ustawienia e-mail',
            'url' => '',
            'route' => 'emailSettings',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => 'voyager-settings',
                'color' => null,
                'parent_id' => null,
                'order' => 7,
            ])->save();
        }
    }
}
