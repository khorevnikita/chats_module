<?php

namespace Khonik\Chats\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**

 * @OA\Schema(
 * @OA\Xml(name="Chat"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="created_at", type="string", format="date-time",example="2019-02-25 12:59:20"),
 * @OA\Property(property="updated_at", type="string", format="date-time",example="2019-02-25 12:59:20"),
 * @OA\Property(property="target_user", type="object", ref="#/components/schemas/User"),
 * @OA\Property(property="last_message", type="object", ref="#/components/schemas/Message"),
 * @OA\Property(property="new_messages_count", type="integer", example=3),
 * )
 *
 * Class Chat
 *
 */
class Chat extends Model
{
    use HasFactory;

    /**
     * @var mixed
     */
    static $PAGINATION_SIZE = 30;

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot("last_opened_at");
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function targetUser()
    {
        return $this->hasOneThrough(User::class, ChatUser::class, 'chat_id', 'id', 'id', 'user_id')
            ->where("users.id", "!=", auth()->id())
            ->select(User::$PUBLIC_INFO);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->orderBy("created_at", "desc");
    }

    public function newMessages()
    {
        return $this->messages()->where("messages.user_id", "!=", auth()->id())
            ->join("chat_user", function ($q) {
                $q->on("chat_user.chat_id", "=", "chats.id")
                    ->where("chat_user.user_id", "=", auth()->id());
            })
            ->whereRaw("messages.created_at > chat_user.last_opened_at");
    }

    public static function findOrCreateByUser(int $target_id): Chat
    {
        $chat = Chat::findByTargetId($target_id);
        if (!$chat) {
            $chat = Chat::createWithUser($target_id);
        }
        return $chat;
    }

    private static function createWithUser(int $target_id): Chat
    {
        $chat = new Chat();
        $chat->save();
        $chat->users()->attach([$target_id, auth()->id()]);
        return $chat;
    }

    private static function findByTargetId(int $target_id)
    {
        $user = auth()->user();
        return $user->chats()->whereHas("users", function ($q) use ($target_id) {
            $q->where("users.id", $target_id);
        })->first();
    }

    public function isMine(): bool
    {
        return DB::table("chat_user")
            ->where("user_id", auth()->id())
            ->where("chat_id", $this->id)
            ->exists();
    }

    public function readAllMessages(): void
    {
        DB::table("chat_user")
            ->where("chat_id", $this->id)
            ->where("user_id", auth()->id())
            ->update(['last_opened_at' => Carbon::now()]);
    }
}
