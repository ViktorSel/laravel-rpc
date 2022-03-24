<?php

namespace IceCloud\RpcClient\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class IceCloudRpcProvider
 * @author a.kazakov
 * @package IceCloud\RpcServer\Providers
 */
class IceCloudRpcClientProvider extends ServiceProvider
{
    /**
     * Регистрация bindings и singletons
     */
    public function register()
    {

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
//                MakeProcedure::class
//                MakeModel::class
            ]);
        }
//        $this->loadRoutesFrom($this->srcRelativePath(__DIR__, self::ROUTES_PATH . '/package-routes.php'));
//        $this->publishes([
//            $this->srcRelativePath(__DIR__, 'config/icecms-core.php') => config_path('icecms-core.php'),
//        ], 'config');
//        $this->publishes([
//            $this->srcRelativePath(__DIR__, 'config/icecms-utm.php') => config_path('icecms-utm.php'),
//        ], 'config');
    }
}
