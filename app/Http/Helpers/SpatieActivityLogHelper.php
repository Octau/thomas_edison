<?php

namespace App\Http\Helpers;

use App\Models\ActivityLog;
use Closure;
use Spatie\Activitylog\Facades\LogBatch;
use Throwable;

class SpatieActivityLogHelper
{
    public const ACTIVITY_DEFAULT_LOG_NAME = 'activitylog.default_log_name';

    public static function logWithinBatch(string $batchLogName, string $batchActionMethod, Closure $closure): mixed
    {
        $defaultLogName = config(self::ACTIVITY_DEFAULT_LOG_NAME);

        try {
            config([self::ACTIVITY_DEFAULT_LOG_NAME => $batchLogName]);
            LogBatch::startBatch();

            $result = $closure();
            $batchUuid = LogBatch::getUuid();

            LogBatch::endBatch();
            config([self::ACTIVITY_DEFAULT_LOG_NAME => $defaultLogName]);

            ActivityLog::forBatch($batchUuid)->update(['description' => $batchActionMethod]);
        } catch (Throwable $throwable) {
            if (LogBatch::isOpen()) {
                LogBatch::endBatch();
            }
            if (config(self::ACTIVITY_DEFAULT_LOG_NAME) !== $defaultLogName) {
                config([self::ACTIVITY_DEFAULT_LOG_NAME => $defaultLogName]);
            }
            throw $throwable;
        }

        return $result;
    }
}
