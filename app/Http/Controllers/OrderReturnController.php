<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\OrderReturn;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Helpers\EmailTagHandlerHelper;
use App\Helpers\OrdersHelper;

use App\Services\OrderReturnService;

use App\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

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
            if($return['check'] > 0){

                $return['photoPath'] = null;
                $updateReturn = 0;
                
                if($return['id'] > 0){
                    $updateReturn = OrderReturn::find($return['id'])->quantity_undamaged;
                }

                if(isset($files[$v])){
                    if($return['id'] > 0){
                        $return['photoPath'] = $this->orderReturnService->updateFile($return['id'],$files[$v]);
                    }else{
                        $return['photoPath'] = $this->orderReturnService->saveFile($files[$v]);
                    }
                }

                if(isset($return['positions'])){
                    $productStock = ProductStock::where('product_id',$return['product_id'])->get();
                    
                    $return['position_id'] = $this->orderReturnService->saveStockPosition($productStock->first()->id,$return['positions']);
                }

                if($return['undamaged'] > 0 || $return['damaged'] > 0){
                    $this->orderReturnService->saveReturn($return);
                }
                if($return['undamaged'] > 0){
                    $return['undamaged'] = $return['undamaged'] - $updateReturn;
                    $this->orderReturnService->updateStockPosition($return['position_id'],$return['undamaged']);
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
