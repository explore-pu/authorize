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
    public function boot(Authorize $extension)
    {
        if (! Authorize::boot()) {
            return ;
        }

        if ($views = $extension->views) {
            $this->loadViewsFrom($views, 'admin-authorize-view');
        }

        $this->app->booted(function () {
            Authorize::routes(__DIR__.'/../routes/web.php');
        });

        if ($this->app->runningInConsole() && $migrations = $extension->migrations) {
            $this->publishes([$migrations => database_path('migrations')], 'admin-authorize-migrations');
        }

        if ($this->app->runningInConsole() && $migrations = $extension->config) {
            $this->publishes([$migrations => config_path('admins')], 'admin-authorize-config');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        app('router')->aliasMiddleware('admin.authorize', AuthorizeMiddleware::class);

        // 替换配置文件
        config([
            'admin.auth.providers.admin.model' => config('elegant-utils.authorization.users_model', Administrator::class),
            'admin.database.users_model' => config('elegant-utils.authorization.users_model', Administrator::class),
            'admin.route.middleware.authorize' => 'admin.authorize',
        ]);

        $this->commands($this->commands);

        Form::extend('checkboxGroup', Fields\CheckBoxGroup::class);
    }
}
