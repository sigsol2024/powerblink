<?php

namespace App\Http\Middleware;

use App\Models\SiteTrafficEvent;
use App\Models\Vehicle;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackPublicTraffic
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        $routeName = $request->route()?->getName();
        $vehicleMeta = $this->resolveVehicleMetadata($request, $routeName);

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
                'vehicle_id' => $vehicleMeta['vehicle_id'],
                'vehicle_slug' => $vehicleMeta['vehicle_slug'],
                'viewed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            // Fail-open: analytics must never break public page delivery.
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
            'favorites.',
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

    /**
     * @return array{vehicle_id:int|null,vehicle_slug:string|null}
     */
    protected function resolveVehicleMetadata(Request $request, ?string $routeName): array
    {
        if ($routeName !== 'inventory.show' && $routeName !== 'product.show') {
            return ['vehicle_id' => null, 'vehicle_slug' => null];
        }

        $slug = $request->route('slug');
        if (! is_string($slug) || trim($slug) === '') {
            return ['vehicle_id' => null, 'vehicle_slug' => null];
        }

        $vehicle = Vehicle::query()
            ->select(['id', 'slug'])
            ->where('slug', $slug)
            ->first();

        return [
            'vehicle_id' => $vehicle?->id,
            'vehicle_slug' => $vehicle?->slug ?? $slug,
        ];
    }

    protected function hashIp(string $ip): ?string
    {
        if ($ip === '') {
            return null;
        }

        return hash('sha256', $ip.'|'.config('app.key'));
    }
}
