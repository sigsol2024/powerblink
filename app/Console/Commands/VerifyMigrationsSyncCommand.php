<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VerifyMigrationsSyncCommand extends Command
{
    protected $signature = 'db:verify-migrations-sync';

    protected $description = 'Verify migration files on disk match rows in the migrations table';

    public function handle(): int
    {
        $diskFiles = collect(File::glob(database_path('migrations/*.php')))
            ->map(fn (string $path): string => pathinfo($path, PATHINFO_FILENAME))
            ->sort()
            ->values();

        $dbRows = DB::table('migrations')->orderBy('migration')->pluck('migration');

        if ($diskFiles->count() !== $dbRows->count()) {
            $this->error('Migration count mismatch: disk='.$diskFiles->count().', database='.$dbRows->count());

            return self::FAILURE;
        }

        $missingOnDisk = $dbRows->diff($diskFiles);
        $missingInDb = $diskFiles->diff($dbRows);

        if ($missingOnDisk->isNotEmpty()) {
            $this->error('Rows in database without migration files:');
            $missingOnDisk->each(fn (string $m) => $this->line('  - '.$m));

            return self::FAILURE;
        }

        if ($missingInDb->isNotEmpty()) {
            $this->error('Migration files without database rows:');
            $missingInDb->each(fn (string $m) => $this->line('  - '.$m));

            return self::FAILURE;
        }

        $this->info('Migrations table is in sync with '.(string) $diskFiles->count().' files on disk.');

        return self::SUCCESS;
    }
}
