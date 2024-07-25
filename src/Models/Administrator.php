<?php

namespace Elegant\Utils\Authorization\Models;

use Elegant\Utils\Models\Administrator as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Routing\Route;

/**
 * @method static find(int $int)
 */
class Administrator extends BaseModel
{
    /**
     * Current user roles
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('elegant-utils.authorization.roles.model');
        $table = config('elegant-utils.authorization.user_role_relational.table');
        $user_id = config('elegant-utils.authorization.user_role_relational.user_id');
        $role_id = config('elegant-utils.authorization.user_role_relational.role_id');

        return $this->belongsToMany($roleModel, $table, $user_id, $role_id)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $permissionModel = config('elegant-utils.authorization.permissions.model');
        $table = config('elegant-utils.authorization.user_permission_relational.table');
        $user_id = config('elegant-utils.authorization.user_permission_relational.user_id');
        $permission_id = config('elegant-utils.authorization.user_permission_relational.permission_id');

        return $this->belongsToMany($permissionModel, $table, $user_id, $permission_id)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function menus(): BelongsToMany
    {
        $menuModel = config('elegant-utils.admin.database.menus_model');
        $table = config('elegant-utils.authorization.user_menu_relational.table');
        $user_id = config('elegant-utils.authorization.user_menu_relational.user_id');
        $menu_id = config('elegant-utils.authorization.user_menu_relational.menu_id');

        return $this->belongsToMany($menuModel, $table, $user_id, $menu_id)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function roleMenus()
    {
        return $this->roles()->with('menus');
    }

    /**
     * @return array
     */
    public function allMenus()
    {
        return array_merge(call_user_func_array('array_merge', $this->roleMenus->pluck('menus')->toArray()), $this->menus->toArray());
    }

    public function rolePermissions()
    {
        return $this->roles()->with('permissions');
    }

    public function allPermissions()
    {
        return array_merge(call_user_func_array('array_merge', $this->rolePermissions->pluck('permissions')->toArray()), $this->permissions->toArray());
    }

    /**
     * Determine whether it is an administrator
     *
     * @return bool
     */
    public function isAdministrator():bool
    {
        return $this->roles->where('slug', 'administrator')->isNotEmpty();
    }

    /**
     * Determine whether there is menu permission
     *
     * @param $menu
     * @return bool
     */
    public function canSeeMenu($menu): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if (in_array($menu['id'], array_column($this->allMenus(), 'id'))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether there is routing permission
     *
     * @param Route $route
     * @return bool
     */
    public function canAccessRoute(Route $route): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        foreach ($this->allPermissions() as $permissions) {
            if ($permissions['uri'] === '*') {
                return true;
            }

            $uris = preg_split('/\r\n|\r|\n/', $permissions['uri'], -1, PREG_SPLIT_NO_EMPTY);
            array_walk($uris, function (&$uri) {
                if ($uri !== '/') {
                    $uri = ltrim($uri, '/');
                }
            });

            if (!empty(array_intersect($permissions['method'], $route->methods)) && in_array($route->uri(), $uris)) {
                return true;
            }
        }

        return false;
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
                $model->permissions()->detach();
                $model->menus()->detach();
            }
        });
    }
}
