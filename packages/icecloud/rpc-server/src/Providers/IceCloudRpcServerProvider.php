<?php

namespace IceCloud\RpcServer\Providers;

use IceCloud\RpcServer\Console\Commands\Cleanup;
use IceCloud\RpcServer\Console\Commands\Generate\FrontendClient;
use IceCloud\RpcServer\Console\Commands\Generate\TestCommand;
use IceCloud\RpcServer\Console\Commands\MakeProcedure;
use IceCloud\RpcServer\Facades\RpcInputExtractor;
use Illuminate\Support\ServiceProvider;

/**
 * Class IceCloudRpcProvider
 * @author a.kazakov
 * @package IceCloud\RpcServer\Providers
 */
class IceCloudRpcServerProvider extends ServiceProvider
{
    const RESOURCE_PATH = __DIR__ . '/../resources';
    /**
     * Регистрация bindings и singletons
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../config/rpc-server.php", "rpc-server");
        // merge user configs
//        $this->mergeConfigFrom(
//            $this->srcRelativePath(__DIR__, 'config/icecms-core.php'), 'icecms-core'
//        );
//        $this->mergeConfigFrom(
//            $this->srcRelativePath(__DIR__, 'config/icecms-utm.php'), 'icecms-utm'
//        );
//        // CMS
//        $this->app->singleton('IceCMS', function ($app) {
//            return new IceCMS();
//        });
//        $this->app->alias('IceCMS', IceCMS::class);
//        // UTM
//        $this->app->singleton('UTMManager', function ($app) {
//            return new UTMManager();
//        });
//        $this->app->alias('UTM', UTMManager::class);
//
//        parent::register();
    }

    /**
     * Запускается после отработки всех регистраторов сервис-провайдеров
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . "/../resources/views", "rpc-server");

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeProcedure::class,
                FrontendClient::class,
                TestCommand::class,
                Cleanup::class
            ]);
        }
//        $this->loadRoutesFrom($this->srcRelativePath(__DIR__, self::ROUTES_PATH . '/package-routes.php'));
        $this->publishes([
            __DIR__ . '/../config/rpc-server.php' => config_path('rpc-server.php')
        ], 'config');

        $this->app->singleton(RpcInputExtractor::class, function () {
            return new RpcInputExtractor();
        });
//        $this->publishes([
//            $this->srcRelativePath(__DIR__, 'config/icecms-utm.php') => config_path('icecms-utm.php'),
//        ], 'config');
    }
}
