<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\OrderPaymentConfirmation;
use App\Facades\Mailer;
use App\Mail\OrderPaymentConfirmationAttachedMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderPaymentConfirmationController extends Controller
{
    public function store(Request $request, $orderId): RedirectResponse|JsonResponse
    {
        $fileName = Str::random(32);
        $file = $request->file('file');

        if ($file && $file->isValid()) {
            $filePath = 'uploads/' . $fileName . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName . '.' . $file->getClientOriginalExtension());

            $confirmation = OrderPaymentConfirmation::create([
                'order_id' => $orderId,
                'file_url' => asset($filePath),
            ]);

            Mailer::create()
                ->to('antoniwoj@o2.pl')
                ->send(new OrderPaymentConfirmationAttachedMail($confirmation, false));
        } else {
            // Handle the error appropriately
            return response()->json(['error' => 'Invalid file or upload error'], 400);
        }


        return redirect()->back();
    }

    public function confirm($orderId): string
    {
        Order::find($orderId)->paymentConfirmation->update(['confirmed', true]);

        return 'Dziękujemy za potwierdzenie otrzymania tej wiadomości!';
    }
}
