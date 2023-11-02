<?php

namespace App\Models\Abstract;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;

abstract class BaseModel extends Model
{
    use ValidatingTrait;

    protected bool $throwValidatingExceptions = true;

    protected $dateFormat = 'Y-m-d\TH:i:s.uP';

}
