<?php

namespace App\Models;

use App\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Model;

class ControllerModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        static::addGlobalScope(new BusinessScope);
    }
}
