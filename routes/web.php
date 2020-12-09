<?php

use Encore\Authorize\Http\Controllers\UserController;
use Encore\Authorize\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

$userController = config('admin.database.users_controller', UserController::class);
Route::resource('admin_users', $userController)->names('admin_users');

$roleController = config('admins.authorize.roles_controller', RoleController::class);
Route::resource('admin_roles', $roleController)->names('admin_roles');
Route::put('admin_roles/{admin_user}/restore', $roleController.'@restore')->name('admin_roles.restore');
Route::delete('admin_roles/{admin_user}/delete', $roleController.'@delete')->name('admin_roles.delete');
