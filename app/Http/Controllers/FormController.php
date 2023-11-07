<?php

namespace App\Http\Controllers;

use App\Entities\Form;
use App\Entities\Order;
use App\Services\FormActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FormController extends Controller
{
    public function index(Form $form, Order $order): View
    {
        return view('form', [
            'form' => $form,
            'order' => $order,
        ]);
    }

    public function executeAction(string $actionName, Order $order): string
    {
        $actions = explode(',', $actionName);

        foreach ($actions as $action) {
            FormActionService::$action($order);
        }

        return 'Akcja została przekazana do systemu';
    }
}
