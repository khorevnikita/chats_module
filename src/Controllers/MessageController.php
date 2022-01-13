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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Message $message
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Message $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chat $chat,Message $message): JsonResponse
    {
        $message->delete();
        return response()->json([
            'status' => 'success',
        ]);
    }
}
