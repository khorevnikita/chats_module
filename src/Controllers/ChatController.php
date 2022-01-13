<?php

namespace Khonik\Chats\Controllers;

use App\Http\Requests\Chat\CreateChatRequest;
use Khonik\Chats\Models\Chat;
use Khonik\Chats\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function myChats(Request $request): JsonResponse
    {
        $take = Chat::$PAGINATION_SIZE;
        $page = (int)$request->page ?: 1;
        $skip = ($page - 1) * $take;

        $user = User::find(auth()->id());

        $chats = $user->chats()->with("targetUser", 'lastMessage', 'lastMessage.author')
            ->withCount("newMessages");

        $total = $chats->count();

        $chats = $chats->skip($skip)->take($take)->get();

        return response()->json([
            'status' => 'success',
            'chats' => $chats,
            'total' => $total,
        ]);
    }

    public function newMessageCount(): JsonResponse
    {
        $newMessagesCount = Message::getNewMessagesCount();
        return response()->json([
            'status' => 'success',
            'new_messages_count' => $newMessagesCount,
        ]);
    }

    public function findOrCreateChat(CreateChatRequest $request): JsonResponse
    {
        $chat = Chat::findOrCreateByUser($request->user_id);
        return response()->json([
            'status' => 'success',
            'chat' => $chat
        ]);
    }
}
