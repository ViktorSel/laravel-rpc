<?php

namespace App\Providers;

use App\Rpc\App\AppRpcServer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AppRpcServer::class, fn() => new AppRpcServer());
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->app->extend(AppRpcServer::class, function (AppRpcServer $server) {
            return $server->loadFromPath(
                app_path('Rpc/App/Procedures'),
                $this->app->getNamespace(),
                app_path()
            );
        });
    }
}
