<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sender() {
        return $this->belongsTo(User::class,'from_user_id','id');
    }
    public function receiver() {
        return $this->belongsTo(User::class,'to_user_id','id');
    }

    public function markAsRead() {
        if($this->status == '1') {
            $this->status = '0';
            $this->save();
        }
    }
}
