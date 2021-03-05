<?php

namespace Encore\Authorize\Models;

use Encore\Admin\Models\Administrator as BaseModel;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('admins.authorize.roles_model');
        $table = config('admins.authorize.role_users_table') ?: 'admin_role_users';

        return $this->belongsToMany($roleModel, $table, 'user_id', 'role_id')->withTimestamps();
    }

//    public function setPermissionsAttribute($value)
//    {
//        return $this->attributes['permissions'] = json_encode(array_values($value), JSON_UNESCAPED_UNICODE);
//    }
//
//    public function getPermissionsAttribute($value)
//    {
//        return array_values(json_decode($value, true) ?: []);
//    }

    /**
     * @return bool
     */
    public function isAdministrator():bool
    {
        return $this->roles->where('slug', 'administrator')->isNotEmpty();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function allPermissions()
    {
        return $this->roles()->pluck('permissions')->flatten()->merge($this->permissions);
    }

    /**
     * @param integer $menu menu id
     * @return bool
     */
    public function canMenu($menu)
    {
        if ($this->isAdministrator() || isset($menu['children']) || url()->isValidUrl($menu['uri'])) {
            return true;
        }

        foreach ($this->allPermissions() as $permissions) {
            if ($permissions === '*' || in_array('GET=>' . $menu['uri'], explode('&&', $permissions))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Route $route
     *
     * @return bool
     */
    public function canRoute(Route $route)
    {
        if ($this->isAdministrator()) {
            return true;
        }

        $uri = set_route_url(admin_restore_path($route->uri()));

        foreach ($this->allPermissions() as $permissions) {
            if ($permissions === '*' || in_array($route->methods[0] . '=>' . $uri, explode('&&', $permissions))) {
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
            }
        });
    }
}
