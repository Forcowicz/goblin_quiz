<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\UserController;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'show')->name('auth.login.show');
    Route::post('/login', 'auth')->name('auth.login.auth');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $user = Auth::user();

        if (!$user->game) {
            Game::create(['user_id' => $user->id]);
        }

        $user->refresh();

        return Inertia::render('Game', [
            'aiConversationId' => fn() => $user->conversations()->latest()->first()?->id ?? null,
            'gameId' => fn() => $user->game->id
        ]);
    })->name('game');

    Route::controller(ChatMessageController::class)->group(function () {
        Route::post('/chat_messages', 'store')->name('chatMessages.store');
    });
});
