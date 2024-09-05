<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessageStatus extends Model
{
    public $table = 'user_message_statuses';
    protected $fillable = ['user_id', 'conversation_id', 'partner_id', 'lasted_message_seen_id','delete_id_mess'];
    public function user()
    {
    	return $this->hasOne('App\User', 'id', 'user_id');
    }
}