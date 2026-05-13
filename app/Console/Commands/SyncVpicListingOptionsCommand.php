<?php

namespace App\Console\Commands;

use App\Services\Vpic\VpicListingCatalogSyncService;
use Illuminate\Console\Command;

/**
 * Sync vPIC makes/models. Only Make_IDs listed in config/vpic.php `allowed_make_ids` (or VPIC_ALLOWED_MAKE_IDS) are imported.
 */
class SyncVpicListingOptionsCommand extends Command
{
    protected $signature = 'listing-options:sync-vpic
                            {--dry-run : Compute changes without writing to the database}
                            {--makes-only : Sync makes from GetAllMakes only}
                            {--models-only : Sync models for existing vPIC makes only}
                            {--no-models : Alias for --makes-only}';

    protected $description = 'Sync NHTSA vPIC makes/models into listing_options (curated allowed_make_ids only; no deletes; manual rows preserved)';

    public function handle(VpicListingCatalogSyncService $sync): int
    {
        if ($this->option('makes-only') && $this->option('models-only')) {
            $this->error('Choose at most one of --makes-only and --models-only.');

            return self::FAILURE;
        }
        if ($this->option('no-models') && $this->option('models-only')) {
            $this->error('Do not combine --no-models with --models-only.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $makesOnly = (bool) $this->option('makes-only') || (bool) $this->option('no-models');
        $modelsOnly = (bool) $this->option('models-only');

        if ($dryRun) {
            $this->warn('Dry run: no database writes.');
        }

        if ($modelsOnly) {
            $stats = $sync->syncModels($dryRun);
            $this->printStats('Models', $stats);

            return isset($stats['error']) ? self::FAILURE : self::SUCCESS;
        }

        if ($makesOnly) {
            $stats = $sync->syncMakes($dryRun);
            $this->printStats('Makes', $stats);

            return isset($stats['error']) ? self::FAILURE : self::SUCCESS;
        }

        $makeStats = $sync->syncMakes($dryRun);
        $this->printStats('Makes', $makeStats);
        if (isset($makeStats['error'])) {
            return self::FAILURE;
        }

        $modelStats = $sync->syncModels($dryRun);
        $this->printStats('Models', $modelStats);

        return isset($modelStats['error']) ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array<string, int|string>  $stats
     */
    private function printStats(string $label, array $stats): void
    {
        if (isset($stats['error'])) {
            $this->error($label.': '.$stats['error']);

            return;
        }
        $this->info($label.' sync finished.');
        foreach ($stats as $k => $v) {
            $this->line('  '.str_replace('_', ' ', (string) $k).': '.$v);
        }
    }
}
