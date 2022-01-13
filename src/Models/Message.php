<?php

namespace Khonik\Chats\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        });

        self::updated(function ($model) {
            // Fire to channel
        });

        self::deleted(function ($model) {
            // Fire to channel
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
