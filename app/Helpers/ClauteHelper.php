<?php

namespace App\Helpers;

class ClauteHelper
{
    public function getResponse(): mixed
    {
        $url = 'https://api.anthropic.com/v1/messages';
        $apiKey = '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';
        $model = 'claude-instant-1.2';
        $maxTokens = 2048;
        $temperature = 0.6;
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a travel advisor that will deliver a detailed itinerary based on the information provided by the user during th  season. I am traveling to Denver, CO.'
            ]
        ];
        $data = [
            'model' => $model,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
            'messages' => $messages
        ];
        $jsonData = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01',
                'content-type: application/json'
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);
        $content = $responseData['content'][0]['text'];

        return $content;
    }
}
