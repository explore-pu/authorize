<?php

namespace Encore\Authorize\Http\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Http\Controllers\UserController as Controller;
use Encore\Admin\Show;
use Encore\Admin\Table;

class UserController extends Controller
{
    /**
     * Make a table builder.
     *
     * @return Table
     */
    public function table()
    {
        $table = parent::table();
        $table->column('roles', trans('admin.roles'))->pluck('name')->label()->insertAfter('name');
        $table->column('permissions', trans('admin.permissions'))->width(500)->display(function ($permissions) {
            $permissions = array_reduce($this->roles->pluck('permissions')->toArray(), 'array_merge', $permissions);
            $names = [];
            foreach (set_permissions() as $key => $value) {
                if ($permissions && in_array($value, $permissions)) {
                    array_push($names, $key);
                }
            }
            return $names;
        })->label()->insertAfter('roles');

        return $table;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('admins.authorize.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permissions) {
            $permissions = array_reduce($this->roles->pluck('permissions')->toArray(), 'array_merge', $permissions);
            $names = [];
            foreach (set_permissions() as $key => $value) {
                if ($permissions && in_array($value, $permissions)) {
                    array_push($names, $key);
                }
            }
            return $names;
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admins.authorize.users_model');
        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $roleModel = config('admins.authorize.roles_model');

        $form = new Form(new $userModel());
        $form->horizontal();

        $form->display('id', 'ID');
        $form->text('username', trans('admin.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);

        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))
            ->options($roleModel::pluck('name', 'id'))
            ->optionDataAttributes('permissions', $roleModel::pluck('permissions', 'id'));
        $form->checkboxGroup('permissions', trans('admin.permissions'))->options(group_permissions())->related('roles', 'permissions');

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }
}
