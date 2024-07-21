<?php

use Elegant\Utils\Authorization\Http\Controllers\UserController;
use Elegant\Utils\Authorization\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

Route::group([
    'as'         => config('elegant-utils.admin.route.as') . '.',
], function (Router $router) {
    $userController = config('elegant-utils.authorization.users_controller', UserController::class);
    $router->resource('auth_users', $userController)->names('auth_users');

    $roleController = config('elegant-utils.authorization.roles_controller', RoleController::class);
    $router->resource('auth_roles', $roleController)->names('auth_roles');
    $router->put('auth_roles/{auth_role}/restore', $roleController.'@restore')->name('auth_roles.restore');
    $router->delete('auth_roles/{auth_role}/delete', $roleController.'@delete')->name('auth_roles.delete');
});
