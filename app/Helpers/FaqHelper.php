<?php

namespace App\Helpers;

final readonly class FaqHelper
{
    public static function stringifyQuestionThree(array $questionThree): string
    {
        $stringified = '';

        foreach ($questionThree as $question) {
            $stringified .= $question['question'] . ' ' . $question['answer'] . ' ';
        }

        return $stringified;
    }
}
