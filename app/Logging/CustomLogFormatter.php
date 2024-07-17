<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class CustomLogFormatter extends LineFormatter
{
    public function format(array $record): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $caller = null;

        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && $trace['class'] !== static::class && !str_starts_with($trace['class'], 'Monolog\\')) {
                $caller = $trace['class'] . '::' . $trace['function'];
                break;
            }
        }

        if ($caller) {
            $record['extra']['caller'] = $caller;
        }

        return parent::format($record);
    }
}
