<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat-{chat_id}', function ($user, $chat_id) {
    return DB::table("chat_user")->where("user_id", $user->id)->where("chat_id", $chat_id)->exists();
});
Broadcast::channel('user-{user_id}', function ($user, $user_id) {
    return (int)$user->id === (int)$user_id;
});

