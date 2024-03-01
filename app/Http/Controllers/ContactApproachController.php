<?php

namespace App\Http\Controllers;

use App\Entities\ContactApproach;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactApproachController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (User::where('phone', $request->get('phone_number'))->exists() || ContactApproach::where('phone_number', $request->get('phone_number'))->exists()) {
            return response()->json([
                'success' => false,
            ]);
        }

        return response()->json(ContactApproach::create([
            'phone_number' => $request->get('phone_number'),
            'referred_by_user_id' => $request->get('referred_by_user_id'),
            'done' => false,
        ]) + ['success' => true]);
    }

    public function getApproachesByUser(int $userId): JsonResponse
    {
        return response()->json(
            ContactApproach::query()->where('referred_by_user_id', $userId)->get(),
        );
    }
}
