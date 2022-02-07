<?php

use Khonik\Chats\Controllers\ChatController;
use Khonik\Chats\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware("auth")->prefix("api")->group(function(){
    Route::prefix("chats")->group(function () {
        Route::get("/", [ChatController::class, 'myChats']);
        Route::get("/new-message-count", [ChatController::class, 'newMessageCount']);
        Route::get("find-by-user", [ChatController::class, 'findOrCreateChat']);
        Route::delete("{chat_id}",[ChatController::class,'destroy']);
    });
    Route::resource("chats.messages", MessageController::class);
});
