<?php

use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class AddCategoriesAndPricelistMenuItems extends Migration
{
    public function up()
    {
        $menu = Menu::where('name', 'admin')->first();
        if (!$menu) {
            return;
        }

        // Shift all existing top-level items with order >= 2 up by 2 to make room
        MenuItem::where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->where('order', '>=', 2)
            ->orderByDesc('order')
            ->each(function (MenuItem $item) {
                $item->order += 2;
                $item->save();
            });

        MenuItem::firstOrCreate(
            ['menu_id' => $menu->id, 'route' => 'categories.index'],
            [
                'title'      => 'Kategorie',
                'url'        => '',
                'target'     => '_self',
                'icon_class' => 'voyager-list',
                'color'      => null,
                'parent_id'  => null,
                'order'      => 2,
                'parameters' => null,
            ]
        );

        MenuItem::firstOrCreate(
            ['menu_id' => $menu->id, 'route' => 'price-list.index'],
            [
                'title'      => 'Cennik firm',
                'url'        => '',
                'target'     => '_self',
                'icon_class' => 'voyager-dollar',
                'color'      => null,
                'parent_id'  => null,
                'order'      => 3,
                'parameters' => null,
            ]
        );
    }

    public function down()
    {
        $menu = Menu::where('name', 'admin')->first();
        if (!$menu) {
            return;
        }

        MenuItem::where('menu_id', $menu->id)
            ->whereIn('route', ['categories.index', 'price-list.index'])
            ->delete();

        // Shift items back down
        MenuItem::where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->where('order', '>=', 4)
            ->orderBy('order')
            ->each(function (MenuItem $item) {
                $item->order -= 2;
                $item->save();
            });
    }
}
