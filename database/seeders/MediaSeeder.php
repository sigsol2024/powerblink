<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Support\MediaLibraryCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $dir = MediaLibraryCatalog::mediaDir();
        if (! is_dir($dir)) {
            return;
        }

        $prefix = MediaLibraryCatalog::mediaPathPrefix();

        foreach (File::files($dir) as $file) {
            $name = $file->getFilename();
            Media::query()->updateOrCreate(
                ['file_path' => $prefix.$name],
                [
                    'filename' => $name,
                    'original_name' => $name,
                    'file_type' => strtolower($file->getExtension()),
                    'file_size' => (int) $file->getSize(),
                ]
            );
        }
    }
}
