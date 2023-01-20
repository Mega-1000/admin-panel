<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade as PDF;
use App\Entities\Quotation;
use App\Entities\Order;
use Illuminate\Http\Request;
use App\Helpers\EmailTagHandlerHelper;
use App\Repositories\TagRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class QuotationsController extends Controller
{
    public function getPdf(TagRepository $tagRepository, EmailTagHandlerHelper $emailTagHandler, $id) {
        $order = Quotation::findorFail($id)->order()->with('employee', 'items')->first();

        $tags = $tagRepository->all();
        $emailTagHandler->setOrder($order);
        $message = Quotation::findorFail($id)->message;
        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }
        
        $order->labels_log .= 'Oferta została wyświetlona dnia ' . date('Y-m-d H:i:s') . ' przez ' . $order->customer()->first()->login . PHP_EOL;
        $order->save();
    
        $name = 'Oferta dla: ' . $order->customer()->first()->login . '_' . $order->id . '_' . date('Y-m-d_H-i-s') . '.html';
        Storage::disk('local')->put('/archive-files/' . $name, $message);
        return view('pdf.quotation', compact('message', 'order'));
    }

    public function getProform(TagRepository $tagRepository, EmailTagHandlerHelper $emailTagHandler, $id) {
        $order = Quotation::findorFail($id)->order()->with('employee', 'status')->first();
        $order->labels_log .= 'Proforma została wyświetlona dnia ' . date('Y-m-d H:i:s') . ' przez ' . $order->customer()->first()->login . PHP_EOL;
        $order->save();
        
        $proformDate = Carbon::now()->format('m/Y');
	    $date = Carbon::now()->toDateString();

        $name = 'Proforma dla: ' . $order->customer()->first()->login . '_' . $order->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf = PDF::loadView('pdf.proform', compact('date', 'proformDate', 'order'))->setPaper('a4');

        Storage::disk('local')->put('/archive-files/' . $name, $pdf->output());

        return $pdf->stream('faktura-proforma.pdf');
    }
}