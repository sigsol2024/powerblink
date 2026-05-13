<?php

namespace App\Console\Commands;

use App\Services\Vpic\VpicListingCatalogPruneService;
use Illuminate\Console\Command;

class PruneVpicListingOptionsCommand extends Command
{
    protected $signature = 'listing-options:prune-vpic
                            {--dry-run : Report counts without deleting rows}';

    protected $description = 'Delete unused vPIC makes/models outside config/vpic.php allowed_make_ids (manual rows untouched; irreversible)';

    public function handle(VpicListingCatalogPruneService $prune): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Dry run: no database writes.');
        } else {
            $this->warn('This permanently deletes unused vPIC rows outside the allowlist.');
        }

        $result = $prune->prune($dryRun);
        if (isset($result['error'])) {
            $this->error($result['error']);

            return self::FAILURE;
        }

        $this->info('Prune finished.');
        $this->line('  models deleted: '.$result['models_deleted']);
        $this->line('  makes deleted: '.$result['makes_deleted']);

        return self::SUCCESS;
    }
}
