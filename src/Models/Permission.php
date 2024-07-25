<?php

namespace Elegant\Utils\Authorization\Models;

use Elegant\Utils\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;
    use DefaultDatetimeFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'menu_id',
        'name',
        'method',
        'uri',
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        $userModel = config('elegant-utils.authorization.users.model');
        $table = config('elegant-utils.authorization.user_permission_relational.table');
        $permission_id = config('elegant-utils.authorization.user_permission_relational.permission_id');
        $user_id = config('elegant-utils.authorization.user_permission_relational.user_id');

        return $this->belongsToMany($userModel, $table, $permission_id, $user_id)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('elegant-utils.authorization.roles.model');
        $table = config('elegant-utils.authorization.role_permission_relational.table');
        $permission_id = config('elegant-utils.authorization.role_permission_relational.permission_id');
        $role_id = config('elegant-utils.authorization.role_permission_relational.role_id');

        return $this->belongsToMany($roleModel, $table, $permission_id, $role_id)->withTimestamps();
    }

    public function menu()
    {
        $menuModel = config('elegant-utils.admin.database.menus_model');

        return $this->belongsTo($menuModel, 'menu_id');
    }

    public function setMethodAttribute($value)
    {
        $this->attributes['method'] = implode(',', $value);
    }

    // 修改器：从数据库中获取 tags 字段并转换为数组
    public function getMethodAttribute($value)
    {
        return explode(',', $value);
    }

    public static function getOptions()
    {
        $permissionModel = new static();

        $permissions = $permissionModel::query()
            ->with(['menu'])
            ->get()
            ->reduce(function ($result, $permission) {
                if (!empty($permission['menu'])) {
                    $result[$permission['menu']['title']][$permission['id']] = $permission['name'];
                } else {
                    $result[$permission['id']] = $permission['name'];
                }
                return $result;
            });

        $data = [];

        foreach ($permissions as $key => $permission) {
            if (is_array($permission) && count($permission) === 1) {
                $data[array_key_first($permission)] = $permission[array_key_first($permission)];
            } else {
                $data[$key] = $permission;
            }
        }

       return $data;
    }
}
