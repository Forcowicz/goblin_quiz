<?php

namespace App\Listeners;

use App\Ai\Agents\Orc;
use App\Events\NewAIResponseEvent;
use App\Events\NewUserMessageEvent;
use App\Http\DTO\AIResponseDTO;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Models\ConversationMessage;

class NewUserMessageListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(NewUserMessageEvent $event): void
    {
        $agent = (new Orc($event->game));

        $user = $event->message->user;

        $attachments = [];
        if ($event->imagePath) {
            $attachments[] = new \Laravel\Ai\Files\LocalImage($event->imagePath);
        }

        if ($event->aiConversationId) {
            $agent->continue($event->aiConversationId, as: $user);
            $response = $agent->prompt($event->message->content, $attachments);
        } else {
            $agent->forUser($user);
            $response = $agent->prompt($event->message->content . " SYSTEM INFO: game_id = " . $event->game->id, $attachments);
        }

        // if ($event->game->has_stage_changed && $event->aiConversationId) {
        //     $gameStage = $event->game->stage;
        //     $systemMessage = new ConversationMessage();
        //     $systemMessage->id = (string) \Illuminate\Support\Str::uuid7();
        //     $systemMessage->conversation_id = $event->aiConversationId;
        //     $systemMessage->agent = 'App/Ai/Agents/Orc';
        //     $systemMessage->role = 'system';
        //     $systemMessage->content = json_encode([
        //         'stage_name' => $gameStage->value,
        //         'stage_objective' => $gameStage->context(),
        //         'stage_completion_criteria' => $gameStage->completionCriteria(),
        //     ]);
        //     $systemMessage->attachments = [];
        //     $systemMessage->tool_calls = [];
        //     $systemMessage->tool_results = [];
        //     $systemMessage->usage = [];
        //     $systemMessage->meta = [];
        //     $systemMessage->save();

        //     $event->game->has_stage_changed = false;
        //     $event->game->save();
        // }

        $lastContent = $response->messages->last()?->content ?? '[]';
        $messages = json_decode($lastContent, true);
        $messages = is_array($messages) ? array_values(array_map('strval', $messages)) : [(string) $lastContent];

        NewAIResponseEvent::dispatch(new AIResponseDTO($messages, $response->conversationId));
    }
}
