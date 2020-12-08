<?php

namespace Encore\Authorize\Http\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Http\Controllers\UserController as Controller;
use Encore\Admin\Show;
use Encore\Admin\Table;
use Encore\Authorize\Models\User;

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
        $show = parent::detail($id);

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
        $roleModel = config('admins.authorize.roles_model');

        $form = parent::form();

        $form->multipleSelect('roles', trans('admin.roles'))
            ->options($roleModel::pluck('name', 'id'))
            ->optionDataAttributes('permissions', $roleModel::pluck('permissions', 'id'));
        $form->checkboxGroup('permissions', trans('admin.permissions'))->options(group_permissions())->related('roles', 'permissions');

        return $form;
    }
}
