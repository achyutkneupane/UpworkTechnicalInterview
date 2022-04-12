<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessagesHistoryRequest;
use App\Http\Requests\NewMessageRequest;

class ChatController extends Controller
{
    public function newMessage(NewMessageRequest $request) {
        $authUser = $request->user();
        $authUser->chatsSent()->create([
            'to_user_id' => $request->to_user_id,
            'message' => $request->message
        ]);
        if($request->latitude && $request->longitude) {
            $authUser->latitude = $request->latitude;
            $authUser->longitude = $request->longitude;
            $authUser->ip_address = $request->ip();
            $authUser->save();
        }
        else {
            $authUser->ip_address = $request->ip();
            $authUser->save();
        }
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
