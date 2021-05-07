<?php

namespace App\Http\Controllers;

use App\Entities\BonusAndPenalty;
use App\Http\Requests\CreateNewBonus;
use App\Http\Requests\DeleteNewBonus;
use App\Services\BonusService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BonusController extends Controller
{

    /**
     * @var BonusService
     */
    private $service;

    public function __construct(BonusService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('bonus.index',
            ['bonuses' => BonusAndPenalty::getAll(),
                'users' => User::all()]);
    }

    public function create(CreateNewBonus $request)
    {
        if (!Gate::allows('create-bonus')) {
            return back()->with(['message' => __('bonus.authorization_error'),
                'alert-type' => 'error']);
        }
        $data = $request->validated();
        if (in_array($data['user_id'], [BonusService::CONSULTANT_INDEX, BonusService::WAREHOUSE_INDEX])) {
            $users = $this->service->findResponsibleUsers($data['order_id']);
            if ($data['user_id'] == BonusService::CONSULTANT_INDEX) {
                $data['user_id'] = $users['consultant']->id;
            } else {
                $data['user_id'] = $users['warehouse']->id;
            }
        }
        $bonus = BonusAndPenalty::create($data);
        if ($data['amount'] > 0) {
            $message = __('bonus.create.success_bonus');
        } else {
            $message = __('bonus.create.success_penalty');
        }
        $this->service->updateLabels($bonus);
        return back()->with(['message' => $message,
            'alert-type' => 'success']);
    }

    public function getResponsibleUsers(int $taskId): \Illuminate\Http\JsonResponse
    {
        $users = $this->service->findResponsibleUsers($taskId);
        $consultantName = is_string($users['consultant']) ?
            'BRAK' : $users['consultant']->firstname . ' ' . $users['consultant']->lastname;
        $warehouseName = is_string($users['warehouse']) ?
            'BRAK' : $users['warehouse']->firstname . ' ' . $users['warehouse']->lastname;

        return response()->json([
            'consultant' => $consultantName,
            'warehouse' => $warehouseName
        ]);
    }

    public function destroy(DeleteNewBonus $request)
    {
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
