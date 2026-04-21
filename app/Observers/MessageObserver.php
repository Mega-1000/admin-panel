<?php

namespace App\Observers;

use App\Entities\Message;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     *
     * @param Message $message
     * @return void
     */
    public function created(Message $message)
    {
        //
    }

    /**
     * Handle the Message "updated" event.
     *
     * @param Message $message
     * @return void
     */
    public function updated(Message $message)
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     *
     * @param Message $message
     * @return void
     */
    public function deleted(Message $message)
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     *
     * @param Message $message
     * @return void
     */
    public function restored(Message $message)
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     *
     * @param Message $message
     * @return void
     */
    public function forceDeleted(Message $message)
    {
        //
    }
}
