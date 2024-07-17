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
        $user = Customer::find(auth()->id());
        $numberOfPagesWitchWeSearchFor = 50;
        $allegroChatId = null;
        $offset = 0;
        $allegroCustomerName = $user->nick_allegro;

        if ($user->nick_allegro) {
            for ($i = 0; $i <= $numberOfPagesWitchWeSearchFor; $i++) {
                $apiResponse = $allegroApiService->request('GET', 'https://api.allegro.pl/messaging/threads', [
                    'offset' => $offset,
                ]);

                Log::notice('twoja stara 4' . $apiResponse);

                $allegroChat = collect(array_filter($apiResponse['threads'], function ($thread) use ($allegroCustomerName) {
                    return $thread['interlocutor']['login'] === $allegroCustomerName;
                }));

                if ($allegroChat->count() === 0) {
                    $offset += 20;
                    continue;
                }

                $allegroChatId = $allegroChat->first()['id'];
                break;
            }

            if ($allegroChatId) {
                $allegroApiService->request('PUT', 'https://api.allegro.pl/messaging/threads/' . $allegroChatId . '/read', [
                    'read' => false,
                ]);
            }
        }


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
