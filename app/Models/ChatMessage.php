<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model {
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function chatConversation(): BelongsTo {
        return $this->belongsTo(ChatConversation::class);
    }
}
