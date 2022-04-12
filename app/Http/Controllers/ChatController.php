<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessagesHistoryRequest;
use App\Http\Requests\NewMessageRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use KMLaravel\GeographicalCalculator\Facade\GeoFacade;

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
        $endUser = User::find($request->to_user_id);
        $messages = $authUser->chatsWith($endUser->id)->map(function($chat) use ($authUser) {
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
        $distance = GeoFacade::setPoints([
            [$authUser->latitude,$authUser->longitude],
            [$endUser->latitude,$endUser->longitude]
        ])->setOptions(['units' => ['m']])->getDistance(function(Collection $result){
            return $result->first();
        });
        return response([
            'messages' => $messages,
            'distance' => $distance
        ]);
    }
}
