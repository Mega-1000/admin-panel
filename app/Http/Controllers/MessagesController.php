<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MessagesHelper;
use App\Entities\Product;
use App\User;
use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Order;
use App\Entities\Chat;
use App\Entities\ChatUser;
use App\Helpers\Exceptions\ChatException;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            $product = $helper->getProduct();
            $order = $helper->getOrder();
            return view('chat.show')->with([
                'chat' => $chat,
                'product' => $product,
                'order' => $order,
                'title' => $helper->getTitle(),
                'route' => route('api.messages.post-new-message', ['token' => $helper->encrypt()]),
                'routeRefresh' => route('api.messages.get-messages', ['token' => $helper->encrypt()])
            ]);
        } catch (ChatException $e) {
            \Log::error('Trying to access chat: '.$e->getMessage());
            return redirect(env('FRONT_URL'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
