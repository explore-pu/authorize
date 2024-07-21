<?php

namespace Elegant\Utils\Authorization\Models;

use Elegant\Utils\Traits\DefaultDatetimeFormat;
use Elegant\Utils\Traits\ModelTree;
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
        'permissions'  => 'json'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('elegant-utils.admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('elegant-utils.authorization.roles_table') ?: 'admin_roles');

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        $roleModel = config('elegant-utils.authorization.roles_model');
        $table = config('elegant-utils.authorization.role_users_table') ?: 'admin_role_users';

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
