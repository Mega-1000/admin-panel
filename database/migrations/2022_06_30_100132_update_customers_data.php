<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustomersData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $customers = \App\Entities\Customer::where('login', 'like', '%+%@user.allegrogroup.pl')
            ->get();
        foreach ($customers as $customer) {
            if (!preg_match('/\+([a-zA-Z0-9]+)@/', $customer->login, $matches)) {
                continue;
            }
            $buyerEmail = str_replace('+' . $matches[1], '', $customer->login);
            $customer->login = $buyerEmail;
            $customer->save();
        }
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
