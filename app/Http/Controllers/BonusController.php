<?php

namespace App\Http\Controllers;

use App\Entities\BonusAndPenalty;
use App\Http\Requests\CreateNewBonus;
use App\Http\Requests\DeleteNewBonus;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BonusController extends Controller
{

    public function index()
    {
        return view('bonus.index',
            ['bonuses' => BonusAndPenalty::getAll(),
                'users' => User::all()]);
    }

    public function create(CreateNewBonus $request) {
        if (!Gate::allows('create-bonus')) {
            return back()->with(['message' => __('bonus.authorization_error'),
                'alert-type' => 'error']);
        }
        $data = $request->validated();
        BonusAndPenalty::create($data);
        if ($data['amount'] > 0) {
            $message = __('bonus.create.success_bonus');
        } else {
            $message = __('bonus.create.success_penalty');
        }
        return back()->with(['message' => $message,
            'alert-type' => 'success']);
    }

    public function destroy(DeleteNewBonus $request) {
        if (!Gate::allows('create-bonus')) {
            return back()->with(['message' => __('bonus.authorization_error'),
                'alert-type' => 'error']);
        }
        $data = $request->validated();
        BonusAndPenalty::destroy($data['id']);
        return back()->with(['message' => __('bonus.destroy.success'),
            'alert-type' => 'success']);
    }
}
