<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessagesHistoryRequest;
use App\Http\Requests\NewMessageRequest;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function newMessage(NewMessageRequest $request) {
        $chatCreate = $request->user()->chatsSent()->create($request->all());
        return response([
            'status' => true,
        ],201);
    }

    public function messagesHistory(MessagesHistoryRequest $request) {
        $authUser = $request->user();
        $endUser = $request->to_user_id;
        $messages = $authUser->chatsWith($endUser)->map(function($chat) use ($authUser,$endUser) {
            if($chat->sender->id == $authUser->id) {
                $direction = 'outgoing';
            }
            else {
                $direction = 'incoming';
            }
            $status = $chat->status;
            if($chat->receiver->id == (int)$authUser->id)
                $chat->markAsRead();
            return [
                'message' => $chat->message,
                'status_count' => $status,
                'direction' => $direction
            ];
        });
        return response([
            'messages' => $messages,
        ]);
    }
}
