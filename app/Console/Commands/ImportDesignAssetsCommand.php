<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportDesignAssetsCommand extends Command
{
    protected $signature = 'powerblink:import-design-assets
                            {--force : Re-download files that already exist}
                            {--sync-media : Create or update media library records for downloaded files}';

    protected $description = 'Download Powerblink design images from asset-manifest.json into public/asset/images/powerblink/';

    public function handle(): int
    {
        $manifestPath = database_path('seed-data/powerblink/asset-manifest.json');
        if (! is_file($manifestPath)) {
            $this->error('Missing manifest: '.$manifestPath);

            return self::FAILURE;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        if (! is_array($manifest) || ! isset($manifest['assets']) || ! is_array($manifest['assets'])) {
            $this->error('Invalid manifest format.');

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $syncMedia = (bool) $this->option('sync-media');
        $downloaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($manifest['assets'] as $asset) {
            $sourceUrl = (string) ($asset['source_url'] ?? '');
            $localPath = (string) ($asset['local_path'] ?? '');

            if ($sourceUrl === '' || $localPath === '') {
                $this->warn('Skipping asset with missing source_url or local_path.');
                $failed++;

                continue;
            }

            $absolutePath = public_path($localPath);
            $directory = dirname($absolutePath);

            if (! is_dir($directory) && ! File::makeDirectory($directory, 0755, true) && ! is_dir($directory)) {
                $this->error('Could not create directory: '.$directory);
                $failed++;

                continue;
            }

            if (is_file($absolutePath) && ! $force) {
                $skipped++;
                if ($syncMedia) {
                    $this->syncMediaRecord($localPath, (string) ($asset['screen'] ?? ''));
                }

                continue;
            }

            try {
                $response = Http::timeout(60)
                    ->withHeaders(['User-Agent' => 'PowerblinkFC/1.0 DesignAssetImporter'])
                    ->get($sourceUrl);

                if (! $response->successful()) {
                    $this->warn("HTTP {$response->status()} for {$localPath}");
                    $failed++;

                    continue;
                }

                $body = $response->body();
                if ($body === '') {
                    $this->warn('Empty response for '.$localPath);
                    $failed++;

                    continue;
                }

                file_put_contents($absolutePath, $body);
                $downloaded++;

                if ($syncMedia) {
                    $this->syncMediaRecord($localPath, (string) ($asset['screen'] ?? ''));
                }
            } catch (\Throwable $exception) {
                $this->warn('Failed '.$localPath.': '.$exception->getMessage());
                $failed++;
            }
        }

        $this->info("Downloaded: {$downloaded}, skipped: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function syncMediaRecord(string $localPath, string $screen): void
    {
        $absolutePath = public_path($localPath);
        if (! is_file($absolutePath)) {
            return;
        }

        try {
            $filename = basename($localPath);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            Media::query()->updateOrCreate(
                ['file_path' => $localPath],
                [
                    'filename' => $filename,
                    'original_name' => $filename,
                    'file_type' => $extension !== '' ? $extension : 'jpg',
                    'file_size' => (int) filesize($absolutePath),
                    'category' => $screen !== '' ? Str::slug($screen, '_') : 'powerblink',
                ]
            );
        } catch (\Throwable $exception) {
            $this->warn('Media sync skipped for '.$localPath.': '.$exception->getMessage());
        }
    }
}
