<?php

namespace Khonik\Chats\Providers;

use Illuminate\Support\ServiceProvider;

class ChatsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishMigrations();
        include __DIR__ . '/../routes.php';
    }

    private function publishMigrations()
    {
        $path = $this->getMigrationsPath();
        $this->publishes([$path => database_path('migrations')], 'migrations');
    }

    private function getMigrationsPath(): string
    {
        return __DIR__ . '/../migrations/';
    }
}
