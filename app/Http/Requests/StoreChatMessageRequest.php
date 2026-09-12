<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['string', 'min:1', 'required'],
            'chat_conversation_id' => ['required', 'exists:chat_conversations,id'],
            'ai_conversation_id' => ['string', 'nullable', 'exists:agent_conversations,id'],
            'game_id' => ['integer', 'required', 'exists:games,id'],
            'image' => ['nullable', 'file', 'image', 'max:10240'],
        ];
    }
}
