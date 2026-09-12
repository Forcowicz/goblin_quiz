<?php

namespace App\Models;

use App\GameStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    protected $fillable = ['user_id'];

    protected $casts = [
        'stage' => GameStage::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moveToNextStage(): void
    {
        $this->stage = $this->stage->next();
        $this->save();
    }
}
