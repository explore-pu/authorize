<?php

namespace Elegant\Utils\Authorization\Http\Controllers;

use Elegant\Utils\Facades\Admin;
use Elegant\Utils\Form;
use Elegant\Utils\Http\Controllers\AdministratorController as Controller;
use Elegant\Utils\Models\Menu;
use Elegant\Utils\Show;
use Elegant\Utils\Table;
use Elegant\Utils\Authorization\Models\Administrator;

class AdministratorController extends Controller
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
//        $table->column('permissions', trans('admin.permissions'))->width(500)->display(function ($permissions) {
//            $permissions = array_reduce($this->roles->pluck('permissions')->toArray(), 'array_merge', $permissions ?: []);
//            $names = [];
//            foreach (set_permissions() as $key => $value) {
//                if ($permissions && in_array($value, $permissions)) {
//                    array_push($names, $key);
//                }
//            }
//            return $names;
//        })->label()->insertAfter('roles');

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
            $permissions = array_reduce($this->roles->pluck('permissions')->toArray(), 'array_merge', $permissions ?: []);
            $names = [];
//            foreach (set_permissions() as $key => $value) {
//                if ($permissions && in_array($value, $permissions)) {
//                    array_push($names, $key);
//                }
//            }
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
        $roleModel = config('elegant-utils.authorization.roles.model');
        $permissionModel = config('elegant-utils.authorization.permissions.model');

        $rolePermissions = $roleModel::with('permissions')->get()->pluck('permissions', 'id')->toArray();
        array_walk($rolePermissions, function (&$value, $key) {
            $value = array_column($value, 'id');
        });

        $form = new Form(new $this->model());

        $userTable = config('elegant-utils.admin.database.administrator_model');
        $connection = config('elegant-utils.admin.database.connection');

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


        $form->multipleSelect('roles', trans('admin.roles'))
            ->options($roleModel::pluck('name', 'id'))
            ->optionDataAttributes('permissions', $rolePermissions)
            ->config('maximumSelectionLength', config('elegant-utils.authorization.users_maximum_roles', '0'));

        $form->checkboxGroup('permissions', trans('admin.menus').trans('admin.permissions'))
            ->options($permissionModel::getOptions())
            ->related('roles', 'permissions');

//        $form->row(function (Form\Layout\Row $row) {
//            $row->column(4, function (Form\Layout\Column $column) {
//                $column->checktree('menus', trans('admin.menus'))
//                    ->options(Admin::menu());
//            });
//            $row->column(8, function (Form\Layout\Column $column) {
//                $permissionModel = config('elegant-utils.authorization.permissions.model');
//                $column->checkboxGroup('permissions', trans('admin.permissions'))
//                    ->options($permissionModel::getOptions())
//                    ->related('roles', 'permissions');
//            });
//        });

//        $form->embeds('permissions', trans('admin.permissions'), function (Form\EmbeddedForm $embeds) {
//            $embeds->row(function (Form\Layout\Row $row) {
//                $row->column(8, function (Form\Layout\Column $column) {
//                    $column->checkboxGroup('routes', trans('admin.route').trans('admin.permissions'))
//                        ->options(group_permissions())
//                        ->related('roles', 'permissions->routes');
//                });
//                $row->column(4, function (Form\Layout\Column $column) {
//                    $column->checktree('menus', trans('admin.menus').trans('admin.permissions'))
//                        ->options(Admin::menu())
//                        ->related('roles', 'permissions->menus');
//                });
//            });
//        });


        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        $form->ignore(['password_confirmation']);

        return $form;
    }
}
