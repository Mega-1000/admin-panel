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
        $mail = new AllegroMessageInformationMail(
            $request->validated('message'),
            FaqHelper::stringifyQuestionThree($request->validated('questionsTree')),
            auth()->user(),
        );

        Mailer::create()->to('ksiegowosc@ephpolska.pl')->send($mail);
        Mailer::create()->to(auth()->user()->login)->send($mail);

        return response()->json([
            'message' => 'OK',
        ]);
    }
}
