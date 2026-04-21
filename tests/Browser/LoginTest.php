<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                ->type('email', 'client@admin.com')
                ->type('password', 'password')
                ->press('ZALOGUJ')
                ->assertPathIs('/admin/orders')
                ->assertSee('Zamówienia')
                ;
        });
    }
}
