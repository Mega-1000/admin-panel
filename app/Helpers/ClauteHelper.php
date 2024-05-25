<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Anthropic\Anthropic;


class ClauteHelper
{
    protected $anthropic;

    public function __construct()
    {
        $headers = [
            'anthropic-version' => '2023-06-01',
            'anthropic-beta' => 'messages-2023-12-15',
            'content-type' => 'application/json',
            'x-api-key' => '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'
        ];

        $this->anthropic = Anthropic::factory()
            ->withHeaders($headers)
            ->make();
    }

    /**
     * For sending server event
     */
    private function send($event, $data): void
    {
        echo "event: {$event}\n";
        echo 'data: ' . $data;
        echo "\n\n";
        ob_flush();
        flush();
    }
}
