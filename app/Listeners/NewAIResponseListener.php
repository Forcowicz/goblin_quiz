<?php

namespace App\Listeners;

use App\Events\NewAIResponseEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewAIResponseListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewAIResponseEvent $event): void
    {
        //
    }
}
