<?php

namespace App\Http\DTO;

readonly class AIResponseDTO
{
    public function __construct(
        public array $messages,
        public string|null $conversationId
    ) {}
}
