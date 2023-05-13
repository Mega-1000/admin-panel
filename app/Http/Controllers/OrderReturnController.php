<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\OrderReturn;
use App\Entities\ProductStock;
use App\Helpers\EmailTagHandlerHelper;
use App\Helpers\OrdersHelper;
use App\Services\OrderReturnService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Entities\ProductStockLog;

class OrderReturnController extends Controller
{
    protected $orderReturnService;

    public function __construct(OrderReturnService $orderReturnService) {
        $this->orderReturnService = $orderReturnService;
    }

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
        if (empty($order)) {
            abort(404);
        }
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
    public function store(Request $request): RedirectResponse
    {
        $files = $request->file('photo');
        foreach($request->return as $v=>$return){
            if($return['sum_of_return']) {
                $order = Order::find($request->get('order_id'));

                OrderPayment::create([
                    'value' => $return['sum_of_return'] * -1,
                    'order_id' => $order->id,
                    'operation_type' => 'zwrot towaru',
                    'payer' => $order->customer->login,
                ]);
            }

            if($return['check'] > 0){
                $return['photoPath'] = null;
                $orderReturn = null;

                if($return['id'] > 0) {
                    $orderReturn = OrderReturn::find($return['id']);
                }

                if(isset($files[$v])){
                    if($return['id'] > 0){
                        $return['photoPath'] = $this->orderReturnService->updateFile($return['id'],$files[$v]);
                    }else{
                        $return['photoPath'] = $this->orderReturnService->saveFile($files[$v]);
                    }
                }

                $productStock = ProductStock::where('product_id', $return['product_id'])->first();

                if(isset($return['positions'])){
                    $return['position_id'] = $this->orderReturnService->saveStockPosition($productStock->id, $return['positions']);
                }
                if($return['undamaged'] >= 0 || $return['damaged'] >= 0){
                    $this->orderReturnService->saveReturn($return);
                }
                if($return['damaged'] !== null && $return['damaged'] >= 0) {
                    $this->orderReturnService->updateStockPositionDamaged($orderReturn, $return['position_id'], $return['damaged'], $productStock->id, $return['order_id']);
                }
                if($return['undamaged'] !== null && $return['undamaged'] >= 0) {
                    $this->orderReturnService->updateStockPosition($orderReturn, $return['position_id'], $return['undamaged'], $productStock->id, $return['order_id']);
                }
            }
        }

        if ($request->submit == 'updateAndStay') {
            return redirect()->route('order_return.index', ['order_id' => $request->id])->with([
                'message' => 'Zwrot zostało dodany pomyślnie!',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->route('orders.index', ['order_id' => $request->id])->with([
            'message' => 'Zwrot zostało dodany pomyślnie!',
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function print($id){
        $order = Order::where('id',$id)->orWhere('token',$id)->first();
        if (empty($order)) {
            abort(404);
        }
        $tagHelper = new EmailTagHandlerHelper();
        $tagHelper->setOrder($order);

        $showPosition = is_a(Auth::user(), User::class);
        $similar = OrdersHelper::findSimilarOrders($order);
        return View::make('orderReturn.print-return', [
            'order' => $order,
            'similar' => $similar ?? [],
            'tagHelper' => $tagHelper,
            'showPosition' => $showPosition
        ]);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function getImgFullScreen($id){
        $orderReturn = OrderReturn::find($id);
        return View::make('orderReturn.image',  [
            'orderReturn' =>$orderReturn
        ]);
    }
}
