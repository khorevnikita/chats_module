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