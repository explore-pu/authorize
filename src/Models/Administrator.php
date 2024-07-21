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
     * Current user roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('elegant-utils.authorization.roles_model');
        $table = config('elegant-utils.authorization.role_users_table') ?: 'admin_role_users';

        return $this->belongsToMany($roleModel, $table, 'user_id', 'role_id')->withTimestamps();
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
    public function canMenu($menu): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if (in_array($menu['id'], $this->getMenuPermissions())) {
            return true;
        }

        return false;
    }

    /**
     * Get all menu permissions
     *
     * @return array
     */
    protected function getMenuPermissions(): array
    {
        $menuPermissions = [];

        // Merged role menu permissions
        foreach ($this->roles()->pluck('permissions') as $permission) {
            if (isset($permission['menus'])) {
                $menuPermissions = array_merge($menuPermissions, $permission['menus']);
            }
        }

        // Consolidate user menu permissions
        if (isset($this->permissions['menus'])) {
            $menuPermissions = array_merge($menuPermissions, $this->permissions['menus']);
        }

        return array_unique(array_filter($menuPermissions));
    }

    /**
     * Determine whether there is routing permission
     *
     * @param Route $route
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

    /**
     * Get all routing permissions
     *
     * @return array
     */
    protected function getRoutePermissions(): array
    {
        $routePermissions = [];

        // Merged role routing permissions
        foreach ($this->roles()->pluck('permissions') as $permission) {
            if (isset($permission['routes'])) {
                $routePermissions = array_merge($routePermissions, $permission['routes']);
            }
        }

        // Incorporate user routing permissions
        if (isset($this->permissions['routes'])) {
            $routePermissions = array_merge($routePermissions, $this->permissions['routes']);
        }

        return array_unique(array_filter($routePermissions));
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
