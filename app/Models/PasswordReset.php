<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PasswordReset extends Model
{
    use HasFactory;

    /** @var string  */
    protected $primaryKey = 'email';

    /** @var string  */
    protected $keyType = 'string';

    /** @var string[] $fillable */
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    const UPDATED_AT = null;

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
