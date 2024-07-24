<?php

namespace Elegant\Utils\Authorization\Http\Controllers;

use Elegant\Utils\Form;
use Elegant\Utils\Http\Controllers\AdminController;
use Elegant\Utils\Show;
use Elegant\Utils\Table;

class PermissionController extends AdminController
{
    protected $method = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function title()
    {
        return trans('admin.permissions');
    }

    public function model()
    {
        return config('elegant-utils.authorization.permissions.model');
    }

    /**
     * Make a table builder.
     *
     * @return Table
     */
    protected function table()
    {
        $table = new Table(new $this->model());

        $table->column('id', 'ID')->sortable();
        $table->column('menu.title', __('admin.menus'));
        $table->column('name', __('admin.name'));
        $table->column('method', __('admin.http_method'))->display(function ($method) {
            return collect($method)->map(function ($name) {
                return "<span class='badge badge-info'>{$name}</span>";
            })->implode('&nbsp;');
        });
        $table->column('uri', __('admin.http_uri'))->display(function ($uri) {
            return collect(explode("\n", $uri))->map(function ($path) {
                return $path;
            })->implode("<br/>");
        });

        return $table;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show($this->model::findOrFail($id));
;
        $show->field('id', 'ID');
        $show->field('menu_id', __('admin.menus'));
        $show->field('name', __('admin.name'));
        $show->field('method', __('admin.http_method'));
        $show->field('uri', __('admin.http_uri'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $menuModel = config('elegant-utils.admin.database.menus_model');

        $form = new Form(new $this->model());

        $form->select('menu_id', __('admin.menus'))->options($menuModel::selectOptions());
        $form->text('name', __('admin.name'));
        $form->multipleSelect('method', __('admin.http_method'))->options(array_combine($this->method, $this->method));
        $form->textarea('uri', __('admin.http_uri'));

        return $form;
    }
}
