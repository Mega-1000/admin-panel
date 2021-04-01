<?php

namespace App\Http\Controllers;

use App\Entities\ProductStockLog;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Menu;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $different
     * @param $stockId
     * @param $positionId
     */
    public function createLog($different, $stockId, $positionId)
    {
        $val = substr($different, 0, 1);
        switch ($val[0]) {
            case '+':
                $action = 'ADD';
                break;
            case '-':
                $action = 'DELETE';
                break;
            default:
                Log::info(
                    'Unsupported action when add quantity',
                    ['class' => get_class($this), 'line' => __LINE__]
                );
                die();
        }
        $quantity = explode($val, $different);
        $userId = Auth::user()->id;

        ProductStockLog::create([
            'product_stock_id' => $stockId,
            'product_stock_position_id' => $positionId,
            'action' => $action,
            'quantity' => $quantity[1],
            'user_id' => $userId
        ]);
    }
    public function test() {
        Menu::display('main');
        Voyager::setting('site.title');
    }

    /**
     * Czyszczenie cache
     */
    public function refreshCache(){
        Artisan::call('view:clear', []);
        Artisan::call('cache:clear', []);
        return redirect('/');
    }
}
