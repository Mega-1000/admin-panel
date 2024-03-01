<?php

namespace App\Http\Controllers;

use App\Entities\ContactApproach;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactApproachController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        return response()->json(ContactApproach::create([
            'phone_number' => $request->get('phone_number'),
            'referred_by_user_id' => $request->get('referred_by_user_id'),
            'done' => false,
        ]));
    }
}
