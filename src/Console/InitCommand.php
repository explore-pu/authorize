<?php

namespace Elegant\Utils\Authorization\Console;

use Elegant\Utils\Models\Menu;
use Elegant\Utils\Authorization\Models\Administrator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        if (!Menu::query()->where('uri', 'auth_roles')->exists()) {
            // 创建菜单项
            $lastOrder = Menu::query()->max('order');
            Menu::query()->create([
                'parent_id' => 2,
                'order' => $lastOrder++,
                'title' => trans('admin.roles'),
                'icon' => 'fas fa-user',
                'uri' => 'auth_roles',
            ]);
        }

        $roleModel = config('elegant-utils.authorization.roles_model');
        // 如果不存在超管角色，创建一个
        if (!$roleModel::query()->where('slug', 'administrator')->exists()) {
            $roleModel::unguard();
            $role = $roleModel::query()->create([
                'name' => trans('admin.super_administrator'),
                'slug' => 'administrator',
            ]);

            // 给用户设置超管角色
            try{
                DB::transaction(function () use ($role) {
                    $userModel = config('elegant-utils.admin.database.users_model');
                    $user = $userModel::find(1);
                    $user->roles()->save($role);
                });
                $this->info('Initialization successful');
            } catch (\Exception $exception) {
                $this->error('initialization failed:'.$exception->getMessage());
            }
        }
    }
}
