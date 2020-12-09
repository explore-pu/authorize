<?php

namespace Encore\Authorize\Http\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Http\Controllers\AdminController;
use Encore\Admin\Show;
use Encore\Admin\Table;

class RoleController extends AdminController
{
    /**
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function title()
    {
        return trans('admin.roles');
    }

    /**
     * Make a table builder.
     *
     * @return Table
     */
    public function table()
    {
        $roleModel = config('admins.authorize.roles_model');
        $table = new Table(new $roleModel());

        $table->column('id', 'ID')->sortable();
        $table->column('slug', trans('admin.slug'));
        $table->column('name', trans('admin.name'));
        $table->column('permissions', trans('admin.permissions'))->width(500)->display(function ($permissions) {
            $names = [];
            foreach (set_permissions() as $key => $value) {
                if ($permissions && in_array($value, $permissions)) {
                    array_push($names, $key);
                }
            }
            return $names;
        })->label();

        $table->column('created_at', trans('admin.created_at'));
        $table->column('updated_at', trans('admin.updated_at'));

        $table->actions(function (Table\Displayers\Actions $actions) {
            if ($actions->row->slug == 'administrator') {
                $actions->disableDestroy();
            }
            if ($actions->row->deleted_at) {
                $actions->disableEdit();
                $actions->disableView();
                $actions->disableDestroy();
                $actions->add(new Table\Actions\Restore());
                $actions->add(new Table\Actions\Delete());
            }
        });

        $table->tools(function (Table\Tools $tools) {
            $tools->batch(function (Table\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        $table->filter(function(Table\Filter $filter){
            $filter->disableIdFilter();
            $filter->scope('trashed', trans('admin.trashed'))->onlyTrashed();
            $filter->like('slug', trans('admin.slug'));
            $filter->like('name', trans('admin.name'));
        });

        return $table;
    }

    /**
     * Make a show builder.
     *
     * @param $id
     * @return Show
     */
    protected function detail($id)
    {
        $roleModel = config('admins.authorize.roles_model');

        $show = new Show($roleModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('slug', trans('admin.slug'));
        $show->field('name', trans('admin.name'));
        $show->field('permissions', trans('admin.permissions'))->as(function ($permissions) {
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
        $form = new Form(new $roleModel());
        $form->horizontal();

        $form->text('name', trans('admin.name'))->required();
        $form->text('slug', trans('admin.slug'))->with(function ($value, Form\Field $field) {
            if ($value == 'administrator') {
                $field->readonly();
            }
        })->required();
        $form->checkboxGroup('permissions', trans('admin.permissions'))->options(group_permissions());

        return $form;
    }
}
