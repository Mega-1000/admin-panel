<?php

namespace App\Http\Controllers;

use App\Entities\Customer;
use App\Facades\Mailer;
use App\Helpers\FaqHelper;
use App\Http\Requests\CreateAllegroMessageRequest;
use App\Mail\AllegroMessageInformationMail;
use App\Services\AllegroApiService;
use App\Services\Label\AddLabelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AllegroMessageController extends Controller
{
    public function __invoke(CreateAllegroMessageRequest $request, AllegroApiService $allegroApiService): JsonResponse
    {
        $apiResponse = $allegroApiService->request('GET', 'https://api.allegro.pl/messaging/threads', []);
        $allegroChatId = $apiResponse['threads'][0]['id'];

        $allegroApiService->request('PUT', 'https://api.allegro.pl/messaging/threads/' . $allegroChatId . '/read', [
            'read' => false,
        ]);

        $user = Customer::find(auth()->id());

        $mail = new AllegroMessageInformationMail(
            $request->validated('message'),
            FaqHelper::stringifyQuestionThree($request->validated('questionsTree')),
            $user,
        );

        Mailer::create()->to('ksiegowosc@ephpolska.pl')->send($mail);
        Mailer::create()->to($user->login)->send($mail);

        $lastOrderOfThisUser = $user->orders()->latest()->first();

        $arr = [];
        AddLabelService::addLabels($lastOrderOfThisUser, [238], $arr, []);

        return response()->json([
            'message' => 'OK',
        ]);
    }
}
