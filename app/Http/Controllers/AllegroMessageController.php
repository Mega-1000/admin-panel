<?php

namespace App\Http\Controllers;

use App\Facades\Mailer;
use App\Helpers\FaqHelper;
use App\Http\Requests\CreateAllegroMessageRequest;
use App\Mail\AllegroMessageInformationMail;
use Illuminate\Http\JsonResponse;


class AllegroMessageController extends Controller
{
    public function __invoke(CreateAllegroMessageRequest $request): JsonResponse
    {
        Mailer::create()
            ->to('ksiegowosc@ephpolska.pl')
            ->send(new AllegroMessageInformationMail(
                $request->validated('message'),
                FaqHelper::stringifyQuestionThree($request->validated('questionsTree')),
                auth()->user(),
            ));

        return response()->json([
            'message' => 'OK',
        ]);
    }
}
