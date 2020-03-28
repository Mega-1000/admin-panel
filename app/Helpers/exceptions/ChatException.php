<?php

namespace App\Helpers\Exceptions;

class ChatException extends \Exception
{
    public function log()
    {
        \Log::error('Chat exception: ',
            ['exception' => $this->getMessage(), 'class' => $this->getFile(), 'line' => $this->getLine()]
        );
    }
}
