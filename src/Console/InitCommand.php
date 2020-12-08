<?php

namespace Encore\Authorize\Console;

use Encore\Admin\Models\Menu;
use Illuminate\Console\Command;
use Encore\Authorize\Models\User;
use Encore\Authorize\Models\Role;

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

        // 如果不存在超管角色，创建一个
        if (!Role::query()->where('slug', 'administrator')->exists()) {
            Role::unguard();
            $role = Role::query()->create([
                'name' => trans('admin.super_administrator'),
                'slug' => 'administrator',
                'permissions' => ['*'],
            ]);

            // 给用户设置超管角色
            $user = User::find(1);
            $user->roles()->save($role);
        }
    }
}
