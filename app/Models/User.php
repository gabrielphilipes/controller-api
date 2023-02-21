<?php

namespace App\Models;

use App\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new BusinessScope);
    }

    /**
     * @var array<string, array<string>>
     */
    private const PERMISSIONS = [
        '*' => ['*'],
        'users' => [
            'create',
            'read',
            'update',
            'delete',
        ],
        'business' => [
            'create',
            'read',
            'update',
            'delete',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'name',
        'email',
        'status',
        'password',
        'permissions',
        'timezone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        if (empty($this->permissions)) {
            return false;
        }

        return in_array('*', $this->permissions);
    }

    /**
     * @return array<string>
     */
    public static function getAllPermissions(): array
    {
        foreach (self::PERMISSIONS as $permissionKey => $permissions) {
            foreach ($permissions as $permission) {
                $listPermissions[] = $permissionKey . '_' . $permission;
            }
        }

        if (empty($listPermissions)) {
            return [];
        }

        return $listPermissions;
    }

    /**
     * @return HasOne
     */
    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'business_id');
    }
}
