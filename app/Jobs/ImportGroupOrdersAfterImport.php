<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use Carbon\Carbon;
use App\User;
use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\Warehouse;

class ImportGroupOrdersAfterImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        // echo 'Job runned!!';
        // die;
        // get all order
        $ordersToTask = array();
        $orders = Order::all();
        foreach ($orders as $order){
            // get all orders for customer
            $customerOrders = Order::where('customer_id', $order->customer_id)->get();
            
            foreach ($customerOrders as $customerOrder){
                $isLabel = $customerOrder->labels()->where('label_id', 47)->where('label_id', '!=', 50)->get()->count();
               
                if($isLabel){
                    // var_dump($isLabel);
                    $products = $customerOrder->items()->get();
                    foreach ($products as $product){
                        $isProductSup = Product::where('id', $product->product_id)->where('symbol', 'LIKE', '%SUP%')->count();
                        
                        if($isProductSup){
                            $ordersToTask[] = $order->id;
                            // var_dump('1111');
                        } else {
                            // var_dump('2222');
                        }
                    }
                } else {
                
                }
                //die;
                //var_dump($customerOrder->id);
                /*if($order->product->long_term){
                    //var_dump(111);
                    //
                    //var_dump($orderItem->product->symbol);
                } else {
        
                }*/
            }
            
            //die;

        }
        
        $result = array_unique($ordersToTask);
        // var_dump($result);
        if($ordersToTask){
            // Create group task
            // var_dump('add order');
            /*$date = Carbon::now();
            $taskPrimal = Task::create([
                'warehouse_id' => Warehouse::OLAWA_WAREHOUSE_ID,
                'user_id' => User::OLAWA_USER_ID,
                'created_by' => 1,
                'name' => 'Grupa zadań - ' . $date->format('d-m'),
                'color' => Task::DEFAULT_COLOR,
                'status' => Task::WAITING_FOR_ACCEPT
            ]);
            $taskSalaryDetails = TaskSalaryDetails::create([
                'task_id' => $taskPrimal->id,
                'consultant_value' => 0,
                'warehouse_value' => 0
            ]);
            
            foreach ($ordersToTask as $orderId) {
                $order = Order::find($orderId);
                $order->createNewTask(5, $taskPrimal->id);
            }*/
            
        }
        die;
    }
}
