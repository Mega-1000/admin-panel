<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class CreateFileManagerTables extends Migration
{
    public function up()
    {
        Schema::create('file_manager_favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('path', 500);
            $table->timestamps();
            $table->unique(['user_id', 'path']);
        });

        $menu = Menu::where('name', 'admin')->first();
        if (!$menu) return;

        MenuItem::firstOrCreate(
            ['menu_id' => $menu->id, 'route' => 'file-manager.index'],
            [
                'title'      => 'Menedżer plików',
                'url'        => '',
                'target'     => '_self',
                'icon_class' => 'voyager-images',
                'color'      => null,
                'parent_id'  => null,
                'order'      => 99,
                'parameters' => null,
            ]
        );
    }

    public function down()
    {
        Schema::dropIfExists('file_manager_favorites');

        $menu = Menu::where('name', 'admin')->first();
        if ($menu) {
            MenuItem::where('menu_id', $menu->id)->where('route', 'file-manager.index')->delete();
        }
    }
}
