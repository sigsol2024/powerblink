<?php

namespace App\Support;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Media library listing scoped by uploader (WordPress-style isolation).
 * Admins see legacy rows (no uploader) and uploads from any admin; vendors only see their own.
 */
final class MediaLibraryCatalog
{
    public static function mediaDir(): string
    {
        return public_path('asset/images/powerblink');
    }

    public static function mediaPathPrefix(): string
    {
        return 'asset/images/powerblink/';
    }

    public static function syncDiscoveredFiles(): void
    {
        $dir = self::mediaDir();
        if (! is_dir($dir)) {
            return;
        }

        foreach (File::files($dir) as $file) {
            $name = $file->getFilename();
            Media::query()->firstOrCreate(
                ['file_path' => self::mediaPathPrefix().$name],
                [
                    'filename' => $name,
                    'original_name' => $name,
                    'file_type' => $file->getExtension(),
                    'file_size' => (int) $file->getSize(),
                ]
            );
        }
    }

    /**
     * @return Builder<Media>
     */
    public static function visibleFor(?User $user): Builder
    {
        self::syncDiscoveredFiles();

        $q = Media::query()->latest();

        if (! $user) {
            return $q->whereRaw('1 = 0');
        }

        if ($user->can('media.manage') && $user->isStaff()) {
            return $q->where(function (Builder $w): void {
                $w->whereNull('uploaded_by')
                    ->orWhereHas('uploader', function (Builder $sub): void {
                        $sub->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->whereIn('name', ['admin', 'editor']));
                    });
            });
        }

        return $q->where('uploaded_by', $user->id);
    }

    /**
     * @return array<int, array{id: int, name: string, path: string, url: string, size: int, modified_at: int}>
     */
    public static function toListItems(Builder $query): array
    {
        return $query
            ->get()
            ->map(static function (Media $item): array {
                return [
                    'id' => (int) $item->id,
                    'name' => $item->filename,
                    'path' => $item->file_path,
                    'url' => asset($item->file_path),
                    'size' => (int) $item->file_size,
                    'modified_at' => $item->updated_at?->timestamp ?? $item->created_at?->timestamp ?? time(),
                ];
            })
            ->all();
    }

    public static function isPublicMediaPath(string $path): bool
    {
        $p = str_replace('\\', '/', trim($path));
        $p = ltrim($p, '/');

        return str_starts_with($p, 'asset/images/powerblink/')
            && ! str_contains($p, '..');
    }

    public static function filterItemsByQuery(array $items, string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return $items;
        }

        return array_values(array_filter($items, static function (array $item) use ($query): bool {
            return Str::contains(Str::lower($item['name']), Str::lower($query));
        }));
    }
}
