<?php

namespace Encore\Authorize\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use DefaultDatetimeFormat;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'permissions'
    ];

    protected $casts = [
        'permissions'  => 'array'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admins.authorize.roles_table') ?: 'admin_roles');

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        $roleModel = config('admins.authorize.roles_model');
        $table = config('admins.authorize.role_users_table') ?: 'admin_role_users';

        return $this->belongsToMany($roleModel, $table, 'role_id', 'user_id')->withTimestamps();
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
                $model->users()->detach();
            }
        });
    }
}
