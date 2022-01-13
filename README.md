# Пакет для быстрого создания чата

## Установка

### 1. Ставим пакет
composer require khonik/chats

### 2. Добавляем в config/app.php в массив providers
Khonik\Chats\Providers\ChatsServiceProvider::class

### 3. Публикуем миграции
php artisan vendor:publish --provider="Khonik\Chats\Providers\ChatsServiceProvider"

### 4. Выполняем миграции
php artisan migrate

### 5. Добавляем trait в модель User
use Chatable

## Использование

### Открыть чат с пользователем

#### METHOD: GET

####URL: /api/chats/find-by-user

####BODY: 

```
{"user_id":2} 
```

####RESPONSE: 
```
{
    "status": "success",
    "chat": {
        "updated_at": "2022-01-13T12:17:16.000000Z",
        "created_at": "2022-01-13T12:17:16.000000Z",
        "id": 2
    }
}
```

### Список моих чатов

#### METHOD: GET

####URL: /api/chats

####RESPONSE: 
```
{
    "status": "success",
    "chats": [
        {
            "id": 2,
            "created_at": "2022-01-13T12:17:16.000000Z",
            "updated_at": "2022-01-13T12:17:16.000000Z",
            "new_messages_count": 0,
            "pivot": {
                "user_id": 1,
                "chat_id": 2,
                "last_opened_at": null
            },
            "target_user": {
                "id": 2,
                "name": "Ira",
                "username": "IrSink",
                "avatar": "http://localhost/storage/1641842698-cnr.png",
                "lat": 55.777216,
                "lon": 37.52706,
                "status": "online",
                "laravel_through_key": 2
            },
            "last_message": null
        }
    ],
    "total": 3
}
```

### Кол-во новых сообщений

#### METHOD: GET

####URL: /api/chats/new-message-count

####RESPONSE: 
```
{
    "status": "success",
    "chats": [
        {
            "id": 2,
            "created_at": "2022-01-13T12:17:16.000000Z",
            "updated_at": "2022-01-13T12:17:16.000000Z",
            "new_messages_count": 0,
            "pivot": {
                "user_id": 1,
                "chat_id": 2,
                "last_opened_at": null
            },
            "target_user": {
                "id": 2,
                "name": "Ira",
                "username": "IrSink",
                "avatar": "http://localhost/storage/1641842698-cnr.png",
                "lat": 55.777216,
                "lon": 37.52706,
                "status": "online",
                "laravel_through_key": 2
            },
            "last_message": null
        }
    ],
    "total": 3
}
```

### Список сообщений в чате

#### METHOD: GET

####URL: /api/chats/{CHAT_ID}/messages

####RESPONSE: 
```
{
    "status": "success",
    "messages": [
        {
            "id": 1,
            "chat_id": 2,
            "user_id": 2,
            "type": "text",
            "body": "Чего молчим?(",
            "created_at": "2022-01-13T12:18:32.000000Z",
            "updated_at": "2022-01-13T12:18:32.000000Z",
            "author": {
                "id": 2,
                "name": "Ira",
                "username": "IrSink",
                "avatar": "http://localhost/storage/1641842698-cnr.png",
                "lat": 55.777216,
                "lon": 37.52706,
                "status": "online"
            }
        }
    ],
    "total": 1
}
```

### Отправить сообщение в чат

#### METHOD: POST

####URL: /api/chats/{CHAT_ID}/messages

#### BODY

```
{
    "type":"text",
    "body":"Чего молчим?("
}
```

####RESPONSE: 
```
{
    "status": "success",
    "message": {
        "chat_id": "2",
        "type": "text",
        "body": "Чего молчим?(",
        "user_id": 2,
        "updated_at": "2022-01-13T12:18:32.000000Z",
        "created_at": "2022-01-13T12:18:32.000000Z",
        "id": 1
    }
}
```

### Изменить сообщение

#### METHOD: PUT

####URL: /api/chats/{CHAT_ID}/messages/{MESSAGE_ID}

#### BODY

```
{
    "type":"text",
    "body":"Чего молчим?("
}
```

####RESPONSE: 
```
{
    "status": "success",
    "message": {
        "chat_id": "2",
        "type": "text",
        "body": "Чего молчим?(",
        "user_id": 2,
        "updated_at": "2022-01-13T12:18:32.000000Z",
        "created_at": "2022-01-13T12:18:32.000000Z",
        "id": 1
    }
}
```


### Удалить сообщение

#### METHOD: DELETE

####URL: /api/chats/{CHAT_ID}/messages/{MESSAGE_ID}

####RESPONSE: 
```
{
    "status": "success",
}
```



