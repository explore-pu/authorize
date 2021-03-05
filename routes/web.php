<?php

use Encore\Authorize\Http\Controllers\UserController;
use Encore\Authorize\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

Route::group([
//    'prefix'     => config('admin.route.prefix'),
//    'middleware' => config('admin.route.middleware'),
    'as'         => config('admin.route.as') . '.',
], function (Router $router) {
    $userController = config('admins.authorize.users_controller', UserController::class);
    $router->resource('auth_users', $userController)->names('auth_users');

    $roleController = config('admins.authorize.roles_controller', RoleController::class);
    $router->resource('auth_roles', $roleController)->names('auth_roles');
    $router->put('auth_roles/{auth_role}/restore', $roleController.'@restore')->name('auth_roles.restore');
    $router->delete('auth_roles/{auth_role}/delete', $roleController.'@delete')->name('auth_roles.delete');
});
