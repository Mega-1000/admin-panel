<?php
/**
 * Created by PhpStorm.
 * User: Adam Mac
 * Date: 17.12.2018
 * Time: 15:12
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class Helper
{
    const USER_ROLE = [
        '1' => 'super_admin',
        '2' => 'admin',
        '3' => 'accountant',
        '4' => 'consultant',
        '5' => 'storekeeper'
    ];

    public static function checkRole($module, $field)
    {
        $currentUser = Auth::user()->role_id;
        $roleName = self::USER_ROLE[$currentUser];
        if($roleName !== 'super_admin' && $roleName !== 'admin') {
            $config = config('permissions.' . $roleName . '.' . $module . '.' . $field);
            if($config) {
                return "true";
            } else {
                return "false";
            }
        } else {
            return true;
        }
    }

    public static function setColumnVisibilityGroup($module, $groupName, $method)
    {
        $currentUser = Auth::user()->role_id;
        $roleName = self::USER_ROLE[$currentUser];

        $config = config('column_visibilities.' . $module . '.' . $groupName . '.' . $roleName . '.' . $method);

        $columns = [];
        foreach ($config as $item) {
            array_push($columns, $item);
        }

        return $columns;
    }
}