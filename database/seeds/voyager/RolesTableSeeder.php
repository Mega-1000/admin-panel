<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $role = Role::firstOrNew(['name' => 'super_admin']);
        if (!$role->exists) {
            $role->fill([
                    'display_name' => 'Super Administrator',
                ])->save();
        }

        $role = Role::firstOrNew(['name' => 'admin']);
        if (!$role->exists) {
            $role->fill([
                    'display_name' => 'Administrator',
                ])->save();
        }

        $role = Role::firstOrNew(['name' => 'accountant']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Księgowy',
            ])->save();
        }

        $role = Role::firstOrNew(['name' => 'consultant']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Konsultant',
            ])->save();
        }

        $role = Role::firstOrNew(['name' => 'storekeeper']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Magazynier',
            ])->save();
        }
    }
}
