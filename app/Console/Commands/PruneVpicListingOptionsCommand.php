<?php

namespace App\Console\Commands;

use App\Services\Vpic\VpicListingCatalogPruneService;
use Illuminate\Console\Command;

class PruneVpicListingOptionsCommand extends Command
{
    protected $signature = 'listing-options:prune-vpic
                            {--dry-run : Report counts without deactivating rows}';

    protected $description = 'Deactivate unused vPIC makes/models outside config/vpic.php allowed_make_ids (manual rows untouched)';

    public function handle(VpicListingCatalogPruneService $prune): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Dry run: no database writes.');
        }

        $result = $prune->prune($dryRun);
        if (isset($result['error'])) {
            $this->error($result['error']);

            return self::FAILURE;
        }

        $this->info('Prune finished.');
        $this->line('  models deactivated: '.$result['models_deactivated']);
        $this->line('  makes deactivated: '.$result['makes_deactivated']);

        return self::SUCCESS;
    }
}
