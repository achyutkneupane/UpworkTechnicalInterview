<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function chatsSent() {
        return $this->hasMany(Chat::class, 'from_user_id');
    }
    public function chatsReceived() {
        return $this->hasMany(Chat::class, 'to_user_id');
    }
    public function chats() {
        return Chat::where('from_user_id',$this->id)->orWhere('to_user_id',$this->id);
    }
    public function chatsWith($id) {
        $senderId = $this->id;
        $receiverId = $id;
        $chats = Chat::where(function($chat) use($senderId,$receiverId) {
            return $chat->where('from_user_id',$senderId)->where('to_user_id',$receiverId);
        })->orWhere(function($chat) use($senderId,$receiverId){
            return $chat->where('from_user_id',$receiverId)->where('to_user_id',$senderId);
        })->orderByDesc('created_at')->get();
        return $chats;
    }
}
