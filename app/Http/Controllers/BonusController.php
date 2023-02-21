<?php

namespace App\Http\Controllers;

use App\Entities\BonusAndPenalty;
use App\Entities\Label;
use App\Entities\Order;
use App\Http\Requests\CreateNewBonus;
use App\Http\Requests\DeleteNewBonus;
use App\Jobs\AddLabelJob;
use App\Jobs\RemoveLabelJob;
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

    public function create(CreateNewBonus $request): \Illuminate\Http\RedirectResponse
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
        $loopPrevention = [];
        dispatch(new AddLabelJob($bonus->order_id, [180], $loopPrevention, [
            'added_type' => Label::BONUS_TYPE
        ]));
        return redirect()->route('bonus.chat', ['id' => $bonus->id])->with(['message' => $message,
            'alert-type' => 'success']);
    }

    public function getChat($id): \Illuminate\Http\Response
    {
        $bonus = BonusAndPenalty::find($id);
        $chat = $this->service->getChat($bonus);
        return response()->view('bonus.chat', [
            'bonus' => $bonus,
            'chat' => $chat
        ]);
    }

    public function firstOrderChat($id): \Illuminate\Http\Response
    {
        $order = Order::find($id);
        $bonus = BonusAndPenalty::where('order_id', '=', $order->id)->orderBy('updated_at', 'desc')->first();
        $chat = $this->service->getChat($bonus);
        return response()->view('bonus.chat', [
            'bonus' => $bonus,
            'chat' => $chat
        ]);
    }

    public function sendMessage(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $bonus = BonusAndPenalty::find($id);
        $this->service->sendMessage($bonus, $request->message, $request->user());
        $loopPrevention = [];
        if (Gate::allows('create-bonus')) {
            dispatch(new AddLabelJob($bonus->order_id, [180], $loopPrevention, [
                'added_type' => Label::BONUS_TYPE
            ]));
            dispatch(new RemoveLabelJob($bonus->order_id, [91]));
        } else {
            dispatch(new AddLabelJob($bonus->order_id, [91, $loopPrevention, [
                'added_type' => Label::BONUS_TYPE
            ]]));
            dispatch(new RemoveLabelJob($bonus->order_id, [180]));
        }

        return redirect()->back();
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

    public function resolve(int $id)
    {
        $bonus = BonusAndPenalty::find($id);
        $bonus->resolved = true;
        $bonus->save();
        dispatch_now(new RemoveLabelJob($bonus->order_id, [91]));
        dispatch_now(new RemoveLabelJob($bonus->order_id, [180]));
        return back()->with(['message' => 'Zamknięto dyskusję.',
            'alert-type' => 'error']);
    }

    public function destroy(DeleteNewBonus $request): \Illuminate\Http\RedirectResponse
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
