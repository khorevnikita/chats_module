<?php

namespace Khonik\Chats\Traits;

use Khonik\Chats\Models\Chat;
use Khonik\Chats\Models\Message;

trait Chatable
{
    public function chats()
    {
        return $this->belongsToMany(Chat::class)->withPivot("last_opened_at");
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}