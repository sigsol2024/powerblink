<?php

namespace App\Http\Middleware;

use App\Models\SiteTrafficEvent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackPublicTraffic
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        $routeName = $request->route()?->getName();

        try {
            SiteTrafficEvent::query()->create([
                'path' => $request->path(),
                'route_name' => $routeName,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'referrer_host' => parse_url((string) $request->headers->get('referer'), PHP_URL_HOST) ?: null,
                'referrer_url' => $request->headers->get('referer'),
                'user_agent' => $request->userAgent(),
                'ip_hash' => $this->hashIp((string) $request->ip()),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'viewed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Public traffic tracking failed.', [
                'route' => $routeName,
                'path' => $request->path(),
                'error' => $exception->getMessage(),
            ]);
        }

        return $response;
    }

    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        $routeName = (string) ($request->route()?->getName() ?? '');
        if ($routeName === '') {
            return false;
        }

        $excludedRoutePrefixes = [
            'admin.',
            'dashboard.',
            'login',
            'register',
            'verification.',
            'password.',
            'profile.',
            'bootstrap.admin',
        ];

        foreach ($excludedRoutePrefixes as $prefix) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix)) {
                return false;
            }
        }

        if ($request->is('admin*', 'dashboard*', 'login', 'register', 'forgot-password', 'reset-password*', 'confirm-password', 'verify-email*', 'storage/*', 'up')) {
            return false;
        }

        return true;
    }

    protected function hashIp(string $ip): ?string
    {
        if ($ip === '') {
            return null;
        }

        return hash('sha256', $ip.'|'.config('app.key'));
    }
}
