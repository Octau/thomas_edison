<?php

namespace App\Http\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

class ScopeHelper
{
    public static function dateParse(string $date): Carbon
    {
        return Date::parse($date)->setTimezone(config('app.timezone'));
    }
}
