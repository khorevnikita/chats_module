<?php

namespace Khonik\Chats\Models;

use App\Events\MessageCreated;
use App\Events\MessageDeleted;
use App\Events\MessageUpdated;
use App\Events\NewMessagesCountUpdated;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**

 * @OA\Schema(
 * @OA\Xml(name="Message"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="chat_id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="user_id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="type", type="string", readOnly="true", example="text"),
 * @OA\Property(property="body", type="string", readOnly="true", example="Hello, world!"),
 * @OA\Property(property="created_at", type="string", format="date-time",example="2019-02-25 12:59:20"),
 * @OA\Property(property="updated_at", type="string", format="date-time",example="2019-02-25 12:59:20"),
 * @OA\Property(property="author", type="object", ref="#/components/schemas/User"),
 * )
 *
 * Class Message
 *
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'body', 'chat_id'];

    static $PAGINATION_SIZE = 30;

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        self::creating(function ($model) {
            $model->user_id = auth()->id();
        });

        self::created(function ($model) {
            // Fire to channel
            broadcast(new MessageCreated($model))->toOthers();
            broadcast(new NewMessagesCountUpdated($model))->toOthers();
        });

        self::updated(function ($model) {
            // Fire to channel
            broadcast(new MessageUpdated($model))->toOthers();
        });

        self::deleting(function ($model) {
            // Fire to channel
            broadcast(new MessageDeleted($model))->toOthers();
        });
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select(User::$PUBLIC_INFO);
    }

    public static function getNewMessagesCount(): int
    {
        $auth_id = auth()->id();
        $data = DB::selectOne("select count(*) as count from chat_user cu join messages m on m.chat_id = cu.chat_id and m.created_at > cu.last_opened_at where cu.user_id = $auth_id ");
        return $data->count;
    }
}
