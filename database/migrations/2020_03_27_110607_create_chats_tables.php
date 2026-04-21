<?php

use App\Helpers\DatabaseRelations;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateChatsTables extends DatabaseRelations
{
    protected array $relations = [
        'chats' => ['product' => ['nullable' => true], 'order' => ['nullable' => true], 'employee' => ['nullable' => true]],
        'messages' => ['chat_user' => ['table' => 'chat_user'], 'chat'],
        'chat_user' => ['chat', 'user' => ['nullable' => true], 'employee' => ['nullable' => true], 'customer' => ['nullable' => true]]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('chat_user', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('last_read_time')->nullable();
            $table->datetime('last_notification_time')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('product_name_supplier');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->index('symbol');
        });

        $this->upRelations();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $this->downRelations();

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['product_name_supplier']);
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropIndex(['symbol']);
        });

        Schema::dropIfExists('chat_user');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');
    }
}
