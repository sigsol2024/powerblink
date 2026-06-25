<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Resolves public media URLs with a guaranteed on-disk placeholder (BestWestern-style demo assets).
 */
final class PlaceholderMedia
{
    public const FALLBACK_RELATIVE = 'asset/images/powerblink/placeholder.svg';

    public static function fallbackUrl(): string
    {
        return asset(self::FALLBACK_RELATIVE);
    }

    /**
     * Return URL for a public-site image path, or the lorem placeholder if the file is missing.
     *
     * @param  string|null  $relativePath  e.g. `asset/images/powerblink/foo.jpg` (under `public/`)
     */
    public static function url(?string $relativePath): string
    {
        if ($relativePath === null || trim($relativePath) === '') {
            return self::fallbackUrl();
        }

        $relativePath = ltrim($relativePath, '/');

        if (preg_match('#^https?://#i', $relativePath) === 1) {
            return $relativePath;
        }

        if (self::publicFileExists($relativePath)) {
            if (str_starts_with($relativePath, 'storage/')) {
                $publicPath = public_path($relativePath);
                if (! File::exists($publicPath)) {
                    $diskPath = ltrim(substr($relativePath, strlen('storage/')), '/');

                    return route('media.storage.show', ['path' => $diskPath], false);
                }
            }

            return asset($relativePath);
        }

        return self::fallbackUrl();
    }

    public static function publicFileExists(string $relativePath): bool
    {
        if (str_starts_with($relativePath, 'storage/')) {
            $diskPath = substr($relativePath, strlen('storage/'));

            return Storage::disk('public')->exists($diskPath);
        }

        return File::exists(public_path($relativePath));
    }
}
