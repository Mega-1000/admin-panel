<?php

namespace App\Http\Controllers;

use App\Entities\OrderOffer;
use App\Helpers\EmailTagHandlerHelper;
use App\Helpers\Utf8Helper;
use App\Repositories\TagRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderOfferController extends Controller
{
    public function getPdf(TagRepository $tagRepository, EmailTagHandlerHelper $emailTagHandler, $id): View
    {
        $order = OrderOffer::findorFail($id)->order()->with('employee', 'items')->first();

        $tags = $tagRepository->all();
        $emailTagHandler->setOrder($order);
        $message = OrderOffer::findorFail($id)->message;

        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }

        $order->labels_log .= 'Oferta została wyświetlona dnia ' . date('Y-m-d H:i:s') . ' przez ' . $order->customer()->first()->login . PHP_EOL;
        $order->save();

        $name = 'Oferta dla: ' . $order->customer()->first()->login . '_' . $order->id . '_' . date('Y-m-d_H-i-s') . '.html';
        Storage::disk('local')->put('/archive-files/' . $name, $message);

        return view('pdf.order_offer', compact('message', 'order'));
    }

    public function getProform(TagRepository $tagRepository, EmailTagHandlerHelper $emailTagHandler, $id): JsonResponse
    {
        $order = OrderOffer::findorFail($id)->order()->with('employee', 'status')->first();
        $order->labels_log .= 'Proforma została wyświetlona dnia ' . date('Y-m-d H:i:s') . ' przez ' . $order->customer()->first()->login . PHP_EOL;
        $order->save();

        $proformDate = Carbon::now()->format('m/Y');
        $date = Carbon::now()->toDateString();

        $name = 'Proforma dla: ' . $order->customer()->first()->login . '_' . $order->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf = Pdf::loadView('pdf.proform', compact('date', 'proformDate', 'order'))->setPaper('a4');

        Storage::disk('local')->put('/archive-files/' . $name, $pdf->output());

        $file = Storage::disk('local')->get('/archive-files/' . $name);

        return response()->json(Utf8Helper::sanitizeString($file))->header('Content-Type', 'application/pdf');
    }
}
