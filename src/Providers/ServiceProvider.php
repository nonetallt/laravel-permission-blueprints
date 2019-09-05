<?php

namespace Nonetallt\Laravel\Permission\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Nonetallt\Laravel\Permission\Console\Commands\UpdateRolesCommand;
use Nonetallt\Laravel\Permission\Console\Commands\UpdatePermissionsCommand;
use Nonetallt\Laravel\Permission\Contracts\PermissionsBlueprint;
use Nonetallt\Laravel\Permission\Blueprint\JsonPermissionsBlueprint;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;
use Nonetallt\Laravel\Permission\Blueprint\JsonRolesBlueprint;

/**
 * Service provider
 */
class ServiceProvider extends BaseServiceProvider
{
    CONST CONFIG_FILE_NAME = 'permission_blueprints';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /* $this->loadMigrationsFrom(__DIR__.'/../database/migrations'); */
        /* $this->loadRoutesFrom(__DIR__.'/../../routes.php'); */
        /* $this->loadViewsFrom(__DIR__.'/../views', 'packagename'); */

        $this->publishes([
            __DIR__.'/../templates/config.php' => config_path(self::CONFIG_FILE_NAME . '.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/seeds/RoleSeeder.php' => database_path('seeds/RoleSeeder.php'),
            __DIR__.'/../database/seeds/PermissionSeeder.php' => database_path('seeds/PermissionSeeder.php'),
        ], 'seeders');

        $this->publishes([
            __DIR__.'/../templates/roles.php' => resource_path('roles.json'),
            __DIR__.'/../templates/permissions.php' => resource_path('permission.json'),
        ], 'examples');

        if ($this->app->runningInConsole()) {
            $this->commands([
                UpdateRolesCommand::class,
                UpdatePermissionsCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../templates/config.php', SELF::CONFIG_FILE_NAME);

        $this->app->singleton(PermissionsBlueprint::class, function ($app) {
            $filepath = config(self::CONFIG_FILE_NAME . '.permissions_path');
            return new JsonPermissionsBlueprint($filepath);
        });

        $this->app->singleton(RolesBlueprint::class, function ($app) {
            $filepath = config(self::CONFIG_FILE_NAME . '.roles_path');
            return new JsonRolesBlueprint($filepath);
        });
    }
}
