<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model {
    public function messages(): HasMany {
        return $this->hasMany(ChatMessage::class);
    }
}
