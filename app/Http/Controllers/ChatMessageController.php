<?php

namespace App\Http\Controllers;

use App\Ai\Agents\Orc;
use App\Events\NewUserMessageEvent;
use App\Http\Requests\StoreChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChatMessageRequest $request)
    {
        $data = $request->validated();

        $chatMessage = new ChatMessage();
        $chatMessage->content = $data['content'];
        //        $chatMessage->chat_conversation_id = $data['chat_conversation_id'];
        $chatMessage->user_id = 1;
        $chatMessage->chat_conversation_id = 1;
        $chatMessage->save();

        $aiConversationId = $data['ai_conversation_id'] ?? null;
        $game = Game::find($data['game_id']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('chat-images', 'local');
            $imagePath = Storage::disk('local')->path($storedPath);
        }

        NewUserMessageEvent::dispatch($chatMessage, $game, $aiConversationId, $imagePath);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatMessage $chatMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatMessage $chatMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatMessage $chatMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatMessage $chatMessage)
    {
        //
    }
}
