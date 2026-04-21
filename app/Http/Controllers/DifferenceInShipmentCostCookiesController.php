<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class DifferenceInShipmentCostCookiesController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $gratherOrLess = $request->input('gratherOrLess');
        $differenceInShipmentCost = $request->input('differenceInShipmentCost');

        Cookie::queue('gratherOrLess', $gratherOrLess, 60 * 24 * 30);
        Cookie::queue('differenceInShipmentCost', $differenceInShipmentCost, 60 * 24 * 30);

        return redirect()->back();
    }
}
