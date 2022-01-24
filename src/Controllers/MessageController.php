<?php

namespace Khonik\Chats\Controllers;

use Khonik\Chats\Requests\Message\MessageListRequest;
use Khonik\Chats\Requests\Message\MessageRequest;
use Khonik\Chats\Models\Chat;
use Khonik\Chats\Models\Message;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Message::class, 'message');
    }

    /**
     * @OA\Get(
     *      path="/api/chats/{chat_id}/messages",
     *      tags={"Message"},
     *      summary="get messages in the chat",
     *      description="List of messages",
     *      security={ {"sanctum": {} }},
     *
     *      @OA\Parameter(
     *          description="chat_id",
     *          in="path",
     *          name="chat_id",
     *          example="1"
     *       ),
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
     *          @OA\Property(property="messages", type="array", @OA\Items(ref="#/components/schemas/Message")),
     *          @OA\Property(property="total", type="integer", example=33),
     *        )
     *     ),
     *  ),
     * )
     */
    public function index($chat_id, MessageListRequest $request): JsonResponse
    {
        $chat = Chat::findOrFail($chat_id);

        $take = Message::$PAGINATION_SIZE;
        $page = (int)$request->page ?: 1;
        $skip = ($page - 1) * $take;

        $messages = Message::with("author")->where("chat_id", $chat->id);

        $total = $messages->count();

        $messages = $messages->skip($skip)->take($take)->get();

        $chat->readAllMessages();

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'total' => $total,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/chats/{chat_id}/messages",
     *      tags={"Message"},
     *      summary="store new message",
     *      description="Write new message in the chat",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          description="chat_id",
     *          in="path",
     *          name="chat_id",
     *          example="1"
     *       ),
     *      @OA\RequestBody(
     *          @OA\JsonContent(
                    @OA\Property(property="type",type="string", example="text"),
                    @OA\Property(property="body",type="string", example="Hello here!"),
     *         ),
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="success"),
     *          @OA\Property(property="message", type="object", ref="#/components/schemas/Message"),
     *        )
     *     ),
     *  ),
     * )
     */
    public function store($chat_id, MessageRequest $request): JsonResponse
    {
        $message = new Message([
            'chat_id' => $chat_id,
        ]);
        $message->fill($request->all());
        $message->save();

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Message $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Message $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * @OA\Put(
     *      path="/api/chats/{chat_id}/messages/{message_id}",
     *      tags={"Message"},
     *      summary="update the message",
     *      description="Update a message in the chat",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          description="chat_id",
     *          in="path",
     *          name="chat_id",
     *          example="1"
     *       ),
     *     @OA\Parameter(
     *          description="message_id",
     *          in="path",
     *          name="message_id",
     *          example="1"
     *       ),
     *
     *      @OA\RequestBody(
     *          @OA\JsonContent(
    @OA\Property(property="type",type="string", example="text"),
    @OA\Property(property="body",type="string", example="Hello here!"),
     *         ),
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="success"),
     *          @OA\Property(property="message", type="object", ref="#/components/schemas/Message"),
     *        )
     *     ),
     *  ),
     * )
     */
    public function update(MessageRequest $request, Chat $chat, Message $message): JsonResponse
    {
        $message->fill($request->all());
        $message->save();
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    /**
     * @OA\Delete (
     *      path="/api/chats/{chat_id}/messages/{message_id}",
     *      tags={"Message"},
     *      summary="delete a message",
     *      description="delete the message from chat",
     *      @OA\Parameter( in="query", name="chat_id", example="1"),
     *      @OA\Parameter( in="query", name="message_id", example="1"),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="success"),
     *        )
     *     ),
     * )
     */
    public function destroy(Chat $chat,Message $message): JsonResponse
    {
        $message->delete();
        return response()->json([
            'status' => 'success',
        ]);
    }
}
