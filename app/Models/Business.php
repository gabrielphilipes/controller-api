<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Business not extends ControllerModel
 * because the ControllerModel is responsible for linking
 * the other models to this one.
 */
class Business extends Model
{
    use HasFactory;

    /** @var string */
    protected $table = 'business';
}
