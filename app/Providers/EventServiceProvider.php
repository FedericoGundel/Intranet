<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogUserLogin;
use App\Listeners\LogFailedLogin;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogUserLogin::class,
        ],
        Failed::class => [
            LogFailedLogin::class,  // Agregar el listener para login fallido
        ],
    ];

    public function boot()
    {
        //
    }
}

