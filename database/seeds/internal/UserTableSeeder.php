<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\User;

/**
 * Class UserTableSeeder
 */
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $role = Role::where('name', 'super_admin')->firstOrFail();

        $user = User::where('email', 'admin@admin.com')->first();
        if ($user === null) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'firstname' => 'suadmin',
                'lastname' => 'suadmin',
                'phone' => '722325222',
                'password' => bcrypt('password'),
                'remember_token' => str_random(60),
                'role_id' => $role->id,
                'status' => 'ACTIVE'
            ]);
        }

        $role = Role::where('name', 'admin')->firstOrFail();

        $user = User::where('email', 'client@admin.com')->first();
        if ($user === null) {
            User::create([
                'name' => 'Admin',
                'email' => 'client@admin.com',
                'firstname' => 'admin',
                'lastname' => 'admin',
                'phone' => '722322222',
                'password' => bcrypt('password'),
                'remember_token' => str_random(60),
                'role_id' => $role->id,
                'status' => 'ACTIVE'
            ]);
        }

        $role = Role::where('name', 'accountant')->firstOrFail();

        $user = User::where('email', 'client@accountant.com')->first();
        if ($user === null) {
            User::create([
                'name' => 'Accountant',
                'email' => 'client@accountant.com',
                'firstname' => 'accountant',
                'lastname' => 'accountant',
                'phone' => '722322312',
                'password' => bcrypt('password'),
                'remember_token' => str_random(60),
                'role_id' => $role->id,
                'status' => 'ACTIVE'
            ]);
        }

        $role = Role::where('name', 'consultant')->firstOrFail();

        $user = User::where('email', 'client@consultant.com')->first();
        if ($user === null) {
            User::create([
                'name' => 'Consultant',
                'email' => 'client@consultant.com',
                'firstname' => 'consultant',
                'lastname' => 'consultant',
                'phone' => '722122222',
                'password' => bcrypt('password'),
                'remember_token' => str_random(60),
                'role_id' => $role->id,
                'status' => 'ACTIVE'
            ]);
        }

        $role = Role::where('name', 'storekeeper')->firstOrFail();

        $user = User::where('email', 'client@storekeeper.com')->first();
        if ($user === null) {
            User::create([
                'name' => 'Storekeeper',
                'email' => 'client@storekeeper.com',
                'firstname' => 'storekeeper',
                'lastname' => 'storekeeper',
                'phone' => '722322827',
                'password' => bcrypt('password'),
                'remember_token' => str_random(60),
                'role_id' => $role->id,
                'status' => 'ACTIVE'
            ]);
        }

    }
}
