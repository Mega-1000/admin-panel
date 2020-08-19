<?php

namespace App\Http\Controllers;

use App\Entities\OrderAllegroCommission;
use App\Entities\SelTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AllegroController extends Controller
{

    const START_STRING = 'Numer zamówienia: ';

    public function setCommission(Request $request)
    {
        try {
            $file = $request->file('file');
            $maxFileSize = 20000000;
            if ($file->getSize() > $maxFileSize) {
                return redirect()->route('orders.index')->with([
                    'message' => __('transport.errors.too-big-file'),
                    'alert-type' => 'error'
                ]);
            }
            $path = Storage::put('user-files/', $file);
            $path = Storage::path($path);

            if (($handle = fopen($path, "r")) === FALSE) {
                throw new \Exception('Nie można otworzyć pliku');
            }
            $firstline = true;
            $updatingOrders = [];
            while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
                if ($firstline) {
                    $firstline = false;
                    continue;
                }
                try {
                    $this->parseCsv($line, $updatingOrders);
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            fclose($handle);
            Storage::delete($path);
            if ($errors) {
                return redirect()->route('orders.index')->with(
                    'allegro_commission_errors', $errors
                );
            }
            return redirect()->route('orders.index')->with(['message' => __('voyager.generic.successfully_updated'),
                'alert-type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with(['message' => __('voyager.generic.update_failed'),
                'alert-type' => 'error']);
        }
    }

    /**
     * @param array $line
     * @param array $updatingOrders
     * @throws \Exception
     */
    private function parseCsv(array $line, array &$updatingOrders): void
    {
        if ($line[7] == '') {
            return;
        }

        $start = strpos($line[7], self::START_STRING);
        if ($start === false) {
            return;
        }
        if (strpos($line[3], 'Prowizja') === false) {
            return;
        }
        $start += strlen(self::START_STRING);

        $end = strpos($line[7], ',', $start);

        if ($end === false) {
            $end = strlen($line[7]);
        }

        $formId = substr($line[7], $start, $end - $start);
        $transaction = SelTransaction::where('tr_CheckoutFormId', $formId)->where('tr_Group', 1)->first();
        if (empty($transaction)) {
            throw new \Exception('Brak zlecenia sello o id zamówienia: ' . $formId);
        }

        $order = $transaction->order;
        $amount = floatval(str_replace(',', '.', $line[5]));
        if (empty($order)) {
            throw new \Exception('Brak zamówienia dla zlecenie sello o id zamówienia: ' . $formId);
        }

        if (empty($updatingOrders[$order->id])) {
            $updatingOrders[$order->id] = true;
            if ($order->detailedCommissions()->count() > 0) {
                \DB::table('order_allegro_commissions')->where('order_id', $order->id)->delete();
                $order->detailedCommissions->each->delete();
            }
        }

        $commission = new OrderAllegroCommission();
        $commission->order_id = $order->id;
        $commission->amount = $amount;
        $commission->save();
    }
}
