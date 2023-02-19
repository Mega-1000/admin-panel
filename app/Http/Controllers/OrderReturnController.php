<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\OrderReturn;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Helpers\EmailTagHandlerHelper;
use App\Helpers\OrdersHelper;

use App\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class OrderReturnController extends Controller
{
    //

    /**
     * Show the return form of a specific resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(int $id)
    {
        $order = Order::with(['customer', 'items','orderReturn'])->find($id);
        $orderId = $id;
        return view('orderReturn.index',compact(
            'order',
            'orderId'
        ));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request){
        $files = $request->file('photo');
        foreach($request->return as $v=>$return){
            $orderReturn = new OrderReturn();
            if($return['id']){
                $orderReturn = OrderReturn::find($return['id']);
            }
            if($return['check'] > 0){
                $photo = null;
                $photoPath = null;

                if(isset($files[$v])){
                    $photo = $files[$v];
                }
                if ($photo !== null) {
                    if ($orderReturn->photo && Storage::disk('public')->exists($orderReturn->photo)) {
                        Storage::delete('public/' . $orderReturn->photo);
                    }
                    $path = 'orderReturn/' . date('F') . date('Y');
                    $photoPath = $photo->store('public/' . $path);
                }

                if(isset($return['positions'])){
                    $productStock = ProductStock::where('product_id',$return['product_id'])->get();

                    $stockPosition = new ProductStockPosition();
                    $stockPosition->product_stock_id = $productStock->first()->id;
                    $stockPosition->lane = $return['positions']['lane'];
                    $stockPosition->bookstand = $return['positions']['bookstand'];
                    $stockPosition->shelf = $return['positions']['shelf'];
                    $stockPosition->position = $return['positions']['position'];
                    $stockPosition->position_quantity = 0;
                    $stockPosition->save();

                    $return['position_id'] = $stockPosition->id;
                }

                if($return['undamaged'] > 0 || $return['damaged'] > 0){
                    $orderReturn->order_id = $return['order_id'];
                    $orderReturn->product_id = $return['product_id'];
                    $orderReturn->product_stock_position_id = $return['position_id'];
                    $orderReturn->user_id = Auth::user()->id;
                    $orderReturn->quantity_undamaged = $return['undamaged'];
                    $orderReturn->quantity_damaged = $return['damaged'];
                    $orderReturn->description = $return['description'];
                    $orderReturn->photo = $photoPath;
                    $orderReturn->save();
                }

                $stock = ProductStockPosition::find($return['position_id']);
                $quantity = $stock->position_quantity;
                $stock->position_quantity = $quantity + $return['undamaged'];
                $stock->save();
                
                unset($orderReturn);
                unset($path);
                unset($photo);
            }
        }

        if ($request->submit == 'updateAndStay') {
            return redirect()->route('order_return.index', ['order_id' => $request->id])->with([
                'message' => __('order_return.message.store'),
                'alert-type' => 'success',
            ]);
        }
        return redirect()->route('orders.index', ['order_id' => $request->id])->with([
            'message' => __('order_return.message.store'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function print($id){
        $order = Order::find($id);

        if (empty($order)) {
            abort(404);
        }
        $tagHelper = new EmailTagHandlerHelper();
        $tagHelper->setOrder($order);
        $order->print_order = true;
        $order->update();
        $showPosition = is_a(Auth::user(), User::class);
        $similar = OrdersHelper::findSimilarOrders($order);
        return View::make('orderReturn.print-return', [
            'order' => $order,
            'similar' => $similar ?? [],
            'tagHelper' => $tagHelper,
            'showPosition' => $showPosition
        ]);
    }
}
