<?php

namespace App\Http\Controllers;

use App\Entities\BonusAndPenalty;
use App\Entities\Label;
use App\Entities\Order;
use App\Http\Requests\CreateNewBonus;
use App\Http\Requests\DeleteNewBonus;
use App\Services\BonusService;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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

    public function create(CreateNewBonus $request): RedirectResponse
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
        /** @var BonusAndPenalty $bonus */
        $bonus = BonusAndPenalty::query()->create($data);
        if ($data['amount'] > 0) {
            $message = __('bonus.create.success_bonus');
        } else {
            $message = __('bonus.create.success_penalty');
        }
        $loopPrevention = [];
        AddLabelService::addLabels(
            $bonus->order, [180],
            $loopPrevention,
            ['added_type' => Label::BONUS_TYPE],
            Auth::user()->id,
        );
        return redirect()->route('bonus.chat', ['id' => $bonus->id])->with(['message' => $message,
            'alert-type' => 'success']);
    }

    public function firstOrderChat($id): Response
    {
        $order = Order::find($id);
        $bonus = BonusAndPenalty::where('order_id', '=', $order->id)->orderBy('updated_at', 'desc')->first();
        $chat = $this->service->getChat($bonus);
        return response()->view('bonus.chat', [
            'bonus' => $bonus,
            'chat' => $chat
        ]);
    }

    public function getChat($id): Response
    {
        $bonus = BonusAndPenalty::find($id);
        $chat = $this->service->getChat($bonus);
        return response()->view('bonus.chat', [
            'bonus' => $bonus,
            'chat' => $chat
        ]);
    }

    public function sendMessage(Request $request, $id): RedirectResponse
    {
        $bonus = BonusAndPenalty::find($id);
        $this->service->sendMessage($bonus, $request->message, $request->user());
        $loopPrevention = [];
        if (Gate::allows('create-bonus')) {
            AddLabelService::addLabels(
                $bonus->order, [180],
                $loopPrevention,
                ['added_type' => Label::BONUS_TYPE],
                Auth::user()->id,
            );
            RemoveLabelService::removeLabels(
                $bonus->order, [91],
                $loopPrevention,
                [],
                Auth::user()->id
            );
        } else {
            AddLabelService::addLabels(
                $bonus->order, [91],
                $loopPrevention,
                ['added_type' => Label::BONUS_TYPE],
                Auth::user()->id,
            );
            RemoveLabelService::removeLabels(
                $bonus->order,
                [180],
                $loopPrevention,
                [],
                Auth::user()->id
            );
        }

        return redirect()->back();
    }

    public function getResponsibleUsers(int $taskId): JsonResponse
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

        $prev = [];
        RemoveLabelService::removeLabels($bonus->order, [91], $prev, [], Auth::user()->id);
        RemoveLabelService::removeLabels($bonus->order, [180], $prev, [], Auth::user()->id);
        return back()->with(['message' => 'Zamknięto dyskusję.',
            'alert-type' => 'error']);
    }

    public function destroy(DeleteNewBonus $request): RedirectResponse
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
