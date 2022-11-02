<?php

namespace Khonik\Chats\Controllers;

use Khonik\Chats\Models\ChatUser;
use Khonik\Chats\Requests\Chat\CreateChatRequest;
use Khonik\Chats\Models\Chat;
use Khonik\Chats\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/chats",
     *      tags={"Chat"},
     *      summary="get available chats",
     *      description="List of chats",
     *      security={ {"sanctum": {} }},
     *
     *      @OA\Parameter(
     *          description="page",
     *          in="query",
     *          name="page",
     *          example="1"
     *       ),
     *
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="success"),
     *          @OA\Property(property="chats", type="array", @OA\Items(ref="#/components/schemas/Chat")),
     *          @OA\Property(property="total", type="integer", example=100),
     *        )
     *     ),
     *  ),
     * )
     */
    public function myChats(Request $request): JsonResponse
    {
        $take = Chat::$PAGINATION_SIZE;
        $page = (int)$request->page ?: 1;
        $skip = ($page - 1) * $take;

        $user = User::find(auth()->id());

        $chats = $user->chats()
            ->with("targetUser", 'lastMessage', 'lastMessage.author')
            ->withCount("newMessages")
            ->whereTargetUser($request->search);

        if ($request->only_friends) {
            $friendIds = User::getFriendIds();
            $chats = $chats->whereTargetUserIdIn($friendIds);
        }

        $total = $chats->count();

        $chats = $chats->orderBy('updated_at','desc')->skip($skip)->take($take)->get();

        return response()->json([
            'status' => 'success',
            'chats' => $chats,
            'total' => $total,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/chats/new-message-count",
     *      tags={"Chat"},
     *      summary="new messages count for all chats",
     *      description="Total count of unread messages",
     *      security={ {"sanctum": {} }},
     *
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="success"),
     *          @OA\Property(property="new_messages_count", type="integer", example=13),
     *        )
     *     ),
     *  ),
     * )
     */
    public function newMessageCount(): JsonResponse
    {
        $newMessagesCount = Message::getNewMessagesCount();
        return response()->json([
            'status' => 'success',
            'new_messages_count' => $newMessagesCount,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/chats/find-by-user",
     *      tags={"Chat"},
     *      summary="get chat id by user id",
     *      description="Find a chat with user",
     *      security={ {"sanctum": {} }},
     *
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="success"),
     *          @OA\Property(property="chat", type="object", @OA\Property(property="id", type="integer", example="1")),
     *        )
     *     ),
     *  ),
     * )
     */
    public function findOrCreateChat(CreateChatRequest $request): JsonResponse
    {
        $chat = Chat::findOrCreateByUser($request->user_id);
        $chat->load('targetUser');
        return response()->json([
            'status' => 'success',
            'chat' => $chat
        ]);
    }

    /**
     * @OA\Delete (
     *      path="/api/chats/{chat_id}",
     *      tags={"Chat"},
     *      summary="delete a chat (clear all history)",
     *      description="delete chat with",
     *      @OA\Parameter( in="query", name="chat_id", example="1"),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="success"),
     *        )
     *     ),
     * )
     */
    public function destroy($chat_id)
    {
        $perm = ChatUser::where("chat_id", $chat_id)
            ->where("user_id", auth()->id())
            ->first();
        if (!$perm) {
            abort(403);
        }
        Chat::where("id", $chat_id)->delete();
        return response()->json([
            'status' => 'success'
        ]);
    }
}
