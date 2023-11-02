<?php

namespace App\Models\Contract;

use App\Models\ActivityLog;
use Spatie\Activitylog\LogOptions;

interface ActivityLogged
{
    public function getActivitylogOptions(): LogOptions;

    public function tapActivity(ActivityLog $activityLog, string $eventName): void;
}
