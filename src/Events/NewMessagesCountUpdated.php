<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Khonik\Chats\Models\Message;

class NewMessagesCountUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $chat_id;
    public $new_messages_count;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->chat_id = $message->chat_id;
        $this->new_messages_count = Message::getNewMessagesCount();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $userIds = DB::table("chat_user")
            ->where("chat_id", $this->chat_id)
            ->where("user_id", "!=", auth()->id())
            ->pluck("user_id");

        return $userIds->map(function ($user_id) {
            return new PrivateChannel("user-$user_id");
        })->toArray();
    }
}
