<?php

namespace Database\Seeders;

use Elegant\Utils\Authorization\Models\Role;
use Elegant\Utils\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorizationTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        $administrator_table = config('elegant-utils.admin.database.administrator_table');
        $menus_table = config('elegant-utils.admin.database.menus_table');
        $roles_table = config('elegant-utils.authorization.roles.model');
        $permisssions_table = config('elegant-utils.authorization.permissions.model');

        // create a role.
        $roles_table::query()->truncate();
        $roles_table::query()->create([
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]);

        // add default permissions.
        $permisssions_table::query()->truncate();
        $permisssions_table::query()->insert([
            [
                'menu_id' => 0,
                'name' => 'all',
                'method' => '*',
                'uri' => '*',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 1,
                'name' => 'dashboard',
                'method' => 'GET,HEAD',
                'uri' => '/',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'name' => 'list',
                'method' => 'GET,HEAD',
                'uri' => '/' . $administrator_table,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'name' => 'create',
                'method' => 'GET,HEAD,POST',
                'uri' => '/' . $administrator_table . '/create
/' . $administrator_table,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'name' => 'edit',
                'method' => 'GET,HEAD,PUT,PATCH',
                'uri' => '/' . $administrator_table . '/{' . Str::singular($administrator_table) . '}/edit
/' . $administrator_table . '/{' . Str::singular($administrator_table) . '}',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'name' => 'show',
                'method' => 'GET,HEAD',
                'uri' => '/' . $administrator_table . '/{' . Str::singular($administrator_table) . '}',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'name' => 'destroy',
                'method' => 'DELETE',
                'uri' => '/' . $administrator_table . '/{' . Str::singular($administrator_table) . '}',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'name' => 'delete',
                'method' => 'DELETE',
                'uri' => '/' . $administrator_table . '/{' . Str::singular($administrator_table) . '}/delete',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'name' => 'restore',
                'method' => 'PUT',
                'uri' => '/' . $administrator_table . '/{' . Str::singular($administrator_table) . '}/restore',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 3,
                'name' => 'list',
                'method' => 'GET,HEAD',
                'uri' => '/' . $menus_table,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 3,
                'name' => 'create',
                'method' => 'POST',
                'uri' => '/' . $menus_table,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 3,
                'name' => 'edit',
                'method' => 'GET,HEAD,PUT,PATCH',
                'uri' => '/' . $menus_table . '/{' . Str::singular($menus_table) . '}/edit
/' . $menus_table . '/{' . Str::singular($menus_table) . '}',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 3,
                'name' => 'destroy',
                'method' => 'DELETE',
                'uri' => '/' . $menus_table . '/{' . Str::singular($menus_table) . '}',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 3,
                'name' => 'delete',
                'method' => 'DELETE',
                'uri' => '/' . $menus_table . '/{' . Str::singular($menus_table) . '}/delete',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 3,
                'name' => 'restore',
                'method' => 'PUT',
                'uri' => '/' . $menus_table . '/{' . Str::singular($menus_table) . '}/restore',
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ]);

        // add default menus.
        Menu::query()->truncate();
        Menu::query()->insert([
            [
                'parent_id' => 0,
                'order' => 4,
                'title' => 'Roles',
                'icon' => 'fas fa-user',
                'uri' => '/roles',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'parent_id' => 0,
                'order' => 5,
                'title' => 'Permissions',
                'icon' => 'fas fa-ban',
                'uri' => '/permissions',
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ]);
    }
}
