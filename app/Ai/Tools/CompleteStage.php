<?php

namespace App\Ai\Tools;

use App\Models\Game;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CompleteStage implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return "
            To narzędzie pozwala na przejście do kolejnego etapu gry. Wywołuj je TYLKO wtedy, gdy gracz spełni kryteria ukończenia aktualnego etapu.
            W przypadku udzielenia poprawnej odpowiedzi przez gracza zostaje zwrócona informacja o kolejnym etapie.
            Informacje z 'playerContext' powiedz graczowi w ramach instrukcji, natomiast 'guideContext' to szersze informacje dla ciebie, przewodnika, których NIGDY nie podawaj bezpośrednio - możesz je wykorzystać do dawania wskazówek, kiedy gracz nie wie co ma zrobić.
            NIGDY nie zdradzaj graczowi kodów z rewersów mapy ani oczekiwanych odpowiedzi - to gracz musi je znaleźć i podać samodzielnie.
        ";
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'answer' => 'string|nullable'
        ]);

        $game = Game::find($validated['game_id']);
        $gameStage = $game->stage;

        $isCorrect = $gameStage->answer() === $validated['answer'] || is_null($gameStage->answer());
        if ($isCorrect) {
            $game->moveToNextStage();
            $game->refresh();

            $response = json_encode([
                'status' => 'success',
                'next_stage' => [
                    'stage_name' => $game->stage->value,
                    'stage_player_context' => $game->stage->playerContext(),
                    'stage_guide_context' => $game->stage->guideContext(),
                    'stage_completion_criteria' => $game->stage->completionCriteria(),
                ]
            ]);
        } else {
            $response = json_encode([
                'status' => 'fail',
                'message' => 'Nieprawidłowa odpowiedź.'
            ]);
        }

        return $response;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'game_id' => $schema->string()->required(),
            'answer' => $schema->string()
        ];
    }
}
