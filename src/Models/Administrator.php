<?php /** @noinspection PhpUndefinedFieldInspection */

namespace Encore\Authorize\Models;

use Encore\Admin\Models\Administrator as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;

/**
 * @method static find(int $int)
 */
class Administrator extends BaseModel
{
    protected $fillable = [
        'username',
        'password',
        'name',
        'avatar',
        'permissions'
    ];

    protected $casts = [
        'permissions'  => 'json'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('admins.authorize.roles_model');
        $table = config('admins.authorize.role_users_table') ?: 'admin_role_users';

        return $this->belongsToMany($roleModel, $table, 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * @return bool
     */
    public function isAdministrator():bool
    {
        return $this->roles->where('slug', 'administrator')->isNotEmpty();
    }

    /**
     * @return Collection
     */
    public function allPermissions(): Collection
    {
        return $this->roles()->pluck('permissions')->merge($this->permissions);
    }

    /**
     * @param $menu
     * @return bool
     */
//    public function canMenu($menu): bool
//    {
//        if ($this->isAdministrator() || isset($menu['children']) || url()->isValidUrl($menu['uri'])) {
//            return true;
//        }
//
//        foreach ($this->allPermissions() as $permissions) {
//            if ($permissions === '*' || in_array('GET=>' . $menu['uri'], explode('&&', $permissions))) {
//                return true;
//            }
//        }
//
//        return false;
//    }

    /**
     * @param Route $route
     *
     * @return bool
     */
    public function canRoute(Route $route): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        $uri = set_route_url(admin_restore_path($route->uri()));

        foreach ($this->getRoutePermissions() as $permissions) {
            if ($permissions === '*' || in_array($route->methods[0] . '=>' . $uri, explode('&&', $permissions))) {
                return true;
            }
        }

        return false;
    }

    protected function getMenuPermissions(): array
    {
        $menuPermissions = [];

        // 合并角色菜单权限
        foreach ($this->roles()->pluck('permissions') as $permission) {
            if (isset($permission['menus'])) {
                $menuPermissions = array_merge($menuPermissions, $permission['menus']);
            }
        }

        // 合并用户菜单权限
        if (isset($this->permissions['menus'])) {
            $menuPermissions = array_merge($menuPermissions, $this->permissions['menus']);
        }

        return $menuPermissions;
    }

    /**
     * 获取所有路由权限
     *
     * @return array
     */
    protected function getRoutePermissions(): array
    {
        $routePermissions = [];

        // 合并角色路由权限
        foreach ($this->roles()->pluck('permissions') as $permission) {
            if (isset($permission['routes'])) {
                $routePermissions = array_merge($routePermissions, $permission['routes']);
            }
        }

        // 合并用户路由权限
        if (isset($this->permissions['routes'])) {
            $routePermissions = array_merge($routePermissions, $this->permissions['routes']);
        }

        return array_unique($routePermissions);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (!method_exists($model, 'trashed') || (method_exists($model, 'trashed') && $model->trashed())) {
                $model->roles()->detach();
            }
        });
    }
}
