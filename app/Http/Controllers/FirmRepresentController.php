<?php

namespace App\Http\Controllers;

use App\Entities\Firm;
use App\FirmRepresent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FirmRepresentController extends Controller
{
    public function markFirmAsNonRepresentsPolicy(Firm $firm): string
    {
        $firm->practices_representatives_policy = true;
        $firm->save();

        return '<script>alert("Dane zostały zapisane! Dziękujemy za kożystanie z naszego serwisu!")</script>';
    }

    public function referRepresentative(Firm $firm, string $emailOfEmployee): View
    {
        return view('representatives.create', compact('firm', 'emailOfEmployee'));
    }

    public function storeRepresentatives(Firm $firm, string $emailOfEmployee, Request $request): string
    {
        $validated = $request->validate([
            'products.*.contact_info' => 'nullable|string|max:255',
        ]);

        foreach ($validated['products'] as $productData) {
            if (empty($productData['contact_info'])) {
                continue;
            }

            $represent = new FirmRepresent();
            $represent->contact_info = $productData['contact_info'];
            $represent->email_of_employee = $emailOfEmployee;
            $represent->firm_id = $firm->id;
            $represent->save();
        }

        return '<script>alert("Dane zostały zapisane! Dziękujemy za kożystanie z naszego serwisu!")</script>';
    }

    public function index(): View
    {
        return \view('representatives.index', ['represents' => FirmRepresent::orderBy('id', 'desc')->paginate(20)]);
    }

    public function create($id, Request $request): RedirectResponse
    {
        $represent = new FirmRepresent();
        $represent->email_of_employee = $request->get('email_of_employee');
        $represent->phone = $request->get('phone');
        $represent->email = $request->get('email');
        $represent->contact_info = $request->get('phone');
        $represent->firm_id = $id;
        $represent->save();

        return redirect()->back();
    }
}
