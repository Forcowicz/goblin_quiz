<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\Game;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUserMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ChatMessage $message, public Game $game, public string|null $aiConversationId = null, public string|null $imagePath = null) {}
}
