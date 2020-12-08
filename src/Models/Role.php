<?php

namespace Encore\Authorize\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use DefaultDatetimeFormat;
    use ModelTree;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'permissions'
    ];

    /**
     * @var string
     */
    protected $titleColumn = 'name';

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
}
