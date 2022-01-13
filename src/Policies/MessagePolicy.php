<?php

namespace Khonik\Chats\Policies;

use Khonik\Chats\Models\Chat;
use Khonik\Chats\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    protected $chat;

    public function __construct()
    {
        $this->chat = Chat::findOrFail(request()->route("chat"));
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        $chat = $this->chat;
        return $chat && $chat->isMine();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Message $message
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $chat = $this->chat;
        return $chat && $chat->isMine();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Message $message
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Message $message)
    {
        return $message->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Message $message
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Message $message)
    {
        return $message->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Message $message
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Message $message
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Message $message)
    {
        //
    }
}
