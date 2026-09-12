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

        $quizTrigger = '';
        if (random_int(1, 10) <= 3) {
            $quizTrigger = ' [SYSTEM: Zanim odpowiesz na wiadomość gracza, wymyśl krótkie pytanie quizowe (np. z wiedzy ogólnej, historii, fantasy, lub o orkach) i zażądaj odpowiedzi. Dopiero po uzyskaniu poprawnej odpowiedzi kontynuuj normalną rozmowę. Sam oceń poprawność odpowiedzi gracza. Bądź wyrozumiały, ale cyniczny.]';
        }

        if ($event->aiConversationId) {
            $agent->continue($event->aiConversationId, as: $user);
            $response = $agent->prompt($event->message->content . $quizTrigger, $attachments);
        } else {
            $agent->forUser($user);
            $response = $agent->prompt($event->message->content . $quizTrigger . " SYSTEM INFO: game_id = " . $event->game->id, $attachments);
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
        $decoded = json_decode($lastContent, true);

        if (is_array($decoded) && array_key_exists('messages', $decoded)) {
            $messages = array_values(array_map('strval', $decoded['messages']));
            $reaction = $decoded['reaction'] ?? null;
        } else {
            $messages = is_array($decoded) ? array_values(array_map('strval', $decoded)) : [(string) $lastContent];
            $reaction = null;
        }

        NewAIResponseEvent::dispatch(new AIResponseDTO($messages, $response->conversationId, $reaction));
    }
}
