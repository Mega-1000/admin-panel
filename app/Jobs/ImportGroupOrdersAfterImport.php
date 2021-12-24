<?php

namespace App\Jobs;

use App\Entities\OrderLabel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Entities\Order;
use App\Entities\Product;
use Carbon\Carbon;
use App\User;
use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\Warehouse;
use App\Entities\TaskTime;
use App\Helpers\TaskTimeHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\GroupOrders;

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
        // get all order
        $ordersToTask = array();
        $ordersToTaskNoLong = array();
        $orders = Order::all();

        foreach ($orders as $order) {
            // get all orders for customer
            $customerOrders = Order::where('customer_id', $order->customer_id)->get();
            
            foreach ($customerOrders as $customerOrder) {
                
                $isLabel = $customerOrder->labels()->where('label_id', 47)->where('label_id', '!=', 50)->get()->count();
                $products = $customerOrder->items()->get();

                //var_dump($customerOrder->, ); die;
                foreach ($products as $product) {
                    
                    $isLongTerm = Product::where('id', $product->product_id)->where('long_term', '=', 1)->count();
                    
                    if ($isLongTerm) {
                        
                        $isProductSup = Product::where('id', $product->product_id)->where('symbol', 'LIKE', '%SUP%')->count();
                        
                        if ($isLabel && $isProductSup) {
                            //var_dump('add'); die;
                            // add label 47 to order in label 146
                            $isLabelToOrder = $customerOrder->labels()->where('label_id', 146)->get()->count();
                            
                            if($isLabelToOrder) {
                                OrderLabel::create([
                                    'order_id' => $customerOrder->id,
                                    'label_id' => 47
                                ]);
                            }
                            //
                            // add to array orders
                            $ordersToTask[] = $customerOrder->id;
                        } else {
                            //1. проверяем товар на %-N%
                            $isProductN = Product::where('id', $product->product_id)->where('symbol', 'LIKE', '%-N%')->count();
                            
                            if ($isProductN) {
                                //var_dump('IS N'); die;
                                $this->createLabelsForN($customerOrder);
                                
                            } else {
                                //2. если нет , то проверяем товары с %-Y%
                                $isProductY = Product::where('id', $product->product_id)->where('symbol', 'LIKE', '%-Y%')->count();
                                
                                if ($isProductY) {
                                    //var_dump('IS Y'); die;
                                    $isCountGtVal = Product::where('id', $product->product_id)->first()->average_amount_of_product_in_package;
                                    //var_dump($product->quantity,$isCountGtVal); die;
                                    //1. Проверяем кол-во товара больше среднего
                                    if($product->quantity > $isCountGtVal){
                                        if(!$isLabel){
                                            $ordersToTask[] = $customerOrder->id;
                                        } else {
                                            $ordersToTaskNoLong[] = $customerOrder->id;
                                        }
                                    } else {
                                        $this->createLabelsForN($customerOrder);
                                    }
                                    
                                } else {
                                    //var_dump('IS LB'); die;
                                    //3. если нет, то проверяем этикетки заказов клиента
                                    if(!$isLabel){
                                        $ordersToTask[] = $customerOrder->id;
                                    } else {
                                        $ordersToTaskNoLong[] = $customerOrder->id;
                                    }
                                    
                                }
                            }
                            // var_dump('2222');
                            
                        }
                    } else {
                        if (!$isLabel) {
                            $ordersToTask[] = $customerOrder->id;
                        } else {
                            $ordersToTaskNoLong[] = $customerOrder->id;
                        }
                    }
                }
            }
        }
        
        $resultOrders = array_unique($ordersToTask);
        $resultOrdersNoLong = array_unique($ordersToTaskNoLong);
        
        //var_dump($resultOrders,$resultOrdersNoLong); die;
        
        if ($ordersToTask) {
            // Create group task
            // var_dump('add order');
            $date = Carbon::now();
            $taskPrimal = Task::create([
                'warehouse_id' => Warehouse::OLAWA_WAREHOUSE_ID,
                'user_id' => User::OLAWA_USER_ID,
                'created_by' => 1,
                'name' => 'Grupa zadań - ' . $date->format('d-m'),
                'color' => Task::DEFAULT_COLOR,
                'status' => Task::WAITING_FOR_ACCEPT,
                'is_import' => 1
            ]);
            
            TaskSalaryDetails::create([
                'task_id' => $taskPrimal->id,
                'consultant_value' => 0,
                'warehouse_value' => 0
            ]);
            
            $count = count($resultOrders);
            $time = ceil($count * 2 / 5) * 5;
            $time = TaskTimeHelper::getFirstAvailableTime($time);
            TaskTime::create([
                'task_id' => $taskPrimal->id,
                'date_start' => $time['start'],
                'date_end' => $time['end']
            ]);
            
            foreach ($resultOrders as $orderId) {
                $order = Order::find($orderId);
                $order->createNewTask(5, $taskPrimal->id);
            }
        }
        
        if ($resultOrdersNoLong) {
            // get last group task
            $groupTaskId = Task::whereNull('order_id')->where('is_import', '!=', 1)->orderBy('created_at', 'DESC')->first();
            
            if ($groupTaskId->id) {
                foreach ($resultOrdersNoLong as $orderId) {
                    $order = Order::find($orderId);
                    $order->createNewTask(5, $groupTaskId->id);
                }
            }
        }
        
        
        //var_dump(111);
        //die;
    }
    
    private function createLabelsForN($customerOrder){
        //1. создается задание с задание с этикеткой 146
        $resultOrdersNoLong[] = $customerOrder->id;
        OrderLabel::create([
            'order_id' => $customerOrder->id,
            'label_id' => 47
        ]);
    
        //2. Высылаем email
        \Mailer::create()
            ->to('xonzonex@gmail.com')
            ->send(new GroupOrders());
    
        //3. Удаляем этикетку 47 добавляем 49 к заданию созданному выше
        OrderLabel::where('label_id', 47)->where('order_id', $customerOrder->id)->delete();
    
        OrderLabel::create([
            'order_id' => $customerOrder->id,
            'label_id' => 49
        ]);
    
        //4. Перенести этот таск в новую строку ocykuence
    }
}
