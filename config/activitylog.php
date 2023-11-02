<?php

use App\Models\ActivityLog;

return [

    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     */
    'delete_records_older_than_days' => env('ACTIVITY_LOGGER_DELETE_DAYS_BEFORE', 365),

    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     */
    'default_log_name' => env('ACTIVITY_LOGGER_DEFAULT_LOG_NAME', 'default'),

    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the current Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * This model will be used to log activity.
     * It should implement the Spatie\Activitylog\Contracts\ActivityLog interface
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => ActivityLog::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the ActivityLog model shipped with this package.
     */
    'table_name' => 'activity_log',

    /*
     * This is the database connection that will be used by the migration and
     * the ActivityLog model shipped with this package. In case it's not set
     * Laravel's database.default will be used instead.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),
];
