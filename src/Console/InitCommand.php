<?php

namespace Encore\Authorize\Console;

use Encore\Admin\Models\Menu;
use Illuminate\Console\Command;

class InitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorize:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize admin-authorize';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        $this->initDatabase();
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');

        // 如果不存在角色菜单，创建一个
        if (!Menu::query()->where('uri', 'admin_roles')->exists()) {
            // 创建菜单项
            $lastOrder = Menu::query()->max('order');
            Menu::query()->create([
                'parent_id' => 0,
                'order' => $lastOrder++,
                'title' => trans('admin.roles'),
                'icon' => 'fas fa-user',
                'uri' => 'admin_roles',
            ]);
        }

        $roleModel = config('admins.authorize.roles_model');
        $userModel = config('admins.authorize.users_model');
        // 如果不存在超管角色，创建一个
        if (!$roleModel::query()->where('slug', 'administrator')->exists()) {
            $roleModel::unguard();
            $role = $roleModel::query()->create([
                'name' => trans('admin.super_administrator'),
                'slug' => 'administrator',
                'permissions' => ['*'],
            ]);

            // 给用户设置超管角色
            $user = $userModel::find(1);
            $user->roles()->save($role);
        }
    }
}
