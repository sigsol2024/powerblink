<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DesignAssetIntegrityTest extends TestCase
{
    public function test_ninety_one_design_jpgs_exist_on_disk(): void
    {
        $dir = public_path('asset/images/powerblink');
        $this->assertDirectoryExists($dir);

        $jpgs = collect(File::files($dir))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'jpg')
            ->count();

        $this->assertSame(91, $jpgs);
    }

    public function test_every_asset_manifest_entry_exists_on_disk(): void
    {
        $manifestPath = database_path('seed-data/powerblink/asset-manifest.json');
        $this->assertFileExists($manifestPath);

        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        $missing = [];

        foreach ($manifest as $entry) {
            $localPath = $entry['local_path'] ?? null;
            if (! is_string($localPath) || $localPath === '') {
                continue;
            }

            $fullPath = public_path($localPath);
            if (! is_file($fullPath)) {
                $missing[] = $localPath;
            }
        }

        $this->assertSame([], $missing, 'Missing design assets: '.implode(', ', $missing));
    }
}
