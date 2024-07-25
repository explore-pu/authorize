<?php

use Elegant\Utils\Authorization\Http\Controllers\AdministratorController;
use Elegant\Utils\Authorization\Http\Controllers\RoleController;
use Elegant\Utils\Authorization\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

Route::group([
    'middleware' => config('elegant-utils.admin.route.middleware'),
    'as'         => config('elegant-utils.admin.route.as'),
], function (Router $router) {
    $administratorController = config('elegant-utils.authorization.administrators.controller', AdministratorController::class);
    $administrator_table = config('elegant-utils.admin.database.administrator_table');
    $router->resource($administrator_table, $administratorController)->names($administrator_table);

    $roleController = config('elegant-utils.authorization.roles.controller', RoleController::class);
    $role_table = config('elegant-utils.authorization.roles.table');
    $router->resource($role_table, $roleController)->names($role_table);
    $router->put($role_table . '/{' . Str::singular($role_table) . '}/restore', $roleController.'@restore')->name($role_table . '.restore');
    $router->delete($role_table . '/{' . Str::singular($role_table) . '}/delete', $roleController.'@delete')->name($role_table . '.delete');

    $permissionController = config('elegant-utils.authorization.permissions.controller', PermissionController::class);
    $permissions_table = config('elegant-utils.authorization.permissions.table');
    $router->resource($permissions_table, $permissionController)->names($permissions_table);
    $router->put($permissions_table . '/{' . Str::singular($permissions_table) . '}/restore', $permissionController.'@restore')->name($permissions_table . '.restore');
    $router->delete($permissions_table . '/{' . Str::singular($permissions_table) . '}/delete', $permissionController.'@delete')->name($permissions_table . '.delete');
});
