<?php

namespace Elegant\Utils\Authorization;

use Elegant\Utils\Form;
use Elegant\Utils\Authorization\Http\Middleware\AuthorizeMiddleware;
use Elegant\Utils\Authorization\Models\Administrator;
use Illuminate\Support\ServiceProvider;

class AuthorizeServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\InitCommand::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin-authorize-view');

        if (file_exists($routes = __DIR__.'/../routes/web.php')) {
            $this->loadRoutesFrom($routes);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'admin-authorize-migrations');
            $this->publishes([__DIR__ . '/../config' => config_path('elegant-utils')], 'admin-authorize-config');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // 替换配置文件
        config([
            'elegant-utils.admin.auth.providers.admin.model' => config('elegant-utils.authorization.users_model', Administrator::class),
            'elegant-utils.admin.database.users_model' => config('elegant-utils.authorization.users_model', Administrator::class),
            'elegant-utils.admin.route.middleware.authorize' => 'admin.authorize',
        ]);

        app('router')->aliasMiddleware('admin.authorize', AuthorizeMiddleware::class);

        $this->commands($this->commands);

        Form::extend('checkboxGroup', Fields\CheckBoxGroup::class);
    }
}
