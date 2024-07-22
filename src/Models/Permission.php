<?php

namespace Elegant\Utils\Authorization\Models;

use Educate\Core\Traits\DefaultDatetimeFormat;
use Educate\Core\Traits\ForceTruncate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ForceTruncate;
    use DefaultDatetimeFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'as',
        'method',
        'uri',
    ];

    /**
     * @return BelongsToMany
     */
    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'worker_permissions', 'permission_id', 'worker_id')->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')->withTimestamps();
    }
}
