<?php

namespace App\Support;

final class MediaImageUrl
{
    /**
     * Public media URL: supports local `asset/...` or `storage/...` paths and absolute http(s) URLs.
     * Missing local files resolve to {@see PlaceholderMedia::fallbackUrl()} so UI never shows a broken src.
     */
    public static function url(?string $path): string
    {
        if ($path === null || trim($path) === '') {
            return PlaceholderMedia::fallbackUrl();
        }

        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        return PlaceholderMedia::url($path);
    }

    public static function isRemote(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        return preg_match('#^https?://#i', $path) === 1;
    }
}
