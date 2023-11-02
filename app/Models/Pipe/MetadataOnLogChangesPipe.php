<?php

namespace App\Models\Pipe;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Contracts\LoggablePipe;
use Spatie\Activitylog\EventLogBag;

class MetadataOnLogChangesPipe implements LoggablePipe
{
    public function handle(EventLogBag $event, Closure $next): EventLogBag
    {
        $event->changes['metadata'] = [
            'url' => $this->url(),
            'ip_address' => $this->ipAddress(),
            'user_agent' => $this->userAgent(),
        ];

        return $next($event);
    }

    private function ipAddress(): string|null
    {
        return Request::ip();
    }

    private function url(): string
    {
        return App::runningInConsole() ? 'console' : Request::fullUrl();
    }

    private function userAgent(): array|string|null
    {
        return Request::header('User-Agent');
    }
}
