<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],
        'import' => [
            'driver' => 'daily',
            'path' => storage_path('logs/import.log'),
            'level' => 'debug',
            'days' => 14,
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],
        'allegro_chat' => [
            'driver' => 'daily',
            'path' => storage_path('logs/allegro_chat.log'),
            'level' => 'debug',
            'days' => 3,
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],

        'papertrail' => [
            'driver'  => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
            'tap' => [App\Logging\CustomLogFormatter::class]
        ],
    ],

];
