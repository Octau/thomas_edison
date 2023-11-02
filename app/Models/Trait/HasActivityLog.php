<?php

namespace App\Models\Trait;

use App\Models\ActivityLog;
use App\Models\Common\ActivityLogDescription;
use App\Models\Pipe\MetadataOnLogChangesPipe;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasActivityLog
{
    use LogsActivity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::addLogChange(new MetadataOnLogChangesPipe);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['id', 'created_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(ActivityLog $activityLog, string $eventName): void
    {
        // set any change on activity_log's attribute
        $activityLog->description = ActivityLogDescription::eventMap($eventName);
        // NOTE: on apply logWithinBatch from SpatieActivityLogHelper, description would be replaced already
    }
}
