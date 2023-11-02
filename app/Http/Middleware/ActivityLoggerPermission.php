<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLoggerPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('activitylog.enabled')) {
            if (! $request->user() instanceof User) {
                activity()->disableLogging();
                try {
                    return $next($request);
                } finally {
                    activity()->enableLogging();
                }
            }
        }

        return $next($request);
    }
}
