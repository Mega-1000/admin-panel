<?php

namespace App\Http\Controllers;

use App\Entities\Label;
use App\Entities\Order;
use App\Http\Requests\AddLabelsCSVRequest;
use App\Services\Label\AddLabelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddLabelsCSVController extends Controller
{
    public function __invoke(AddLabelsCSVRequest $request): RedirectResponse
    {
        $file = $request->file('file');

        $labelsToDelete = explode(',', $request->labels_to_delete);
        $labelsToAdd = explode(',', $request->labels_to_add);

        $file = fopen($file, 'r');
        $fileData = [];
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            $fileData[] = $data[0];
        }
        fclose($file);

        foreach ($fileData as $data) {
            $arr = [];

            $order = Order::where('allegro_payment_id', $data)->first();
            if (!$order) {
                continue;
            }

            AddLabelService::addLabels($order, $labelsToAdd, $arr, []);

            foreach ($labelsToDelete as $label) {
                $order->labels()->detach($label);
            }
        }

        return redirect()->back()->with([
            'message' => 'Etykiety zostały dodane',
        ]);
    }
}
