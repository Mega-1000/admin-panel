<?php

namespace App\Http\Controllers;

use App\Entities\Firm;
use App\FirmRepresent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FirmRepresentController extends Controller
{
    public function markFirmAsNonRepresentsPolicy(Firm $firm): string
    {
        $firm->practices_representatives_policy = false;
        $firm->save();

        return '<script>alert("Dane zostały zapisane! Dziękujemy za kożystanie z naszego serwisu!")</script>';
    }

    public function referRepresentative(Firm $firm): View
    {
        return view('representatives.create', compact('firm'));
    }

    public function storeRepresentatives(Firm $firm, Request $request): string
    {
        $validated = $request->validate([
            'products.*.contact_info' => 'required|string|max:255',
        ]);

        foreach ($validated['products'] as $productData) {
            $represent = new FirmRepresent();
            $represent->contact_info = $productData['contact_info'];
            $represent->firm_id = $firm->id;
            $represent->save();
        }

        return '<script>alert("Dane zostały zapisane! Dziękujemy za kożystanie z naszego serwisu!")</script>';
    }
}
