<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('listing_options')) {
            return;
        }

        Schema::table('listing_options', function (Blueprint $table) {
            if (! Schema::hasColumn('listing_options', 'external_source')) {
                $table->string('external_source', 32)->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('listing_options', 'external_id')) {
                $table->string('external_id', 64)->nullable()->after('external_source');
            }
            if (! Schema::hasColumn('listing_options', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('external_id');
            }
            if (! Schema::hasColumn('listing_options', 'source_payload')) {
                $table->json('source_payload')->nullable()->after('last_synced_at');
            }
        });

        Schema::table('listing_options', function (Blueprint $table) {
            if (! $this->indexExists('listing_options', 'listing_options_category_external_idx')) {
                $table->index(['category_id', 'external_source', 'external_id'], 'listing_options_category_external_idx');
            }
            if (! $this->indexExists('listing_options', 'listing_options_category_parent_external_idx')) {
                $table->index(['category_id', 'parent_id', 'external_source', 'external_id'], 'listing_options_category_parent_external_idx');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('listing_options')) {
            return;
        }

        Schema::table('listing_options', function (Blueprint $table) {
            if ($this->indexExists('listing_options', 'listing_options_category_parent_external_idx')) {
                $table->dropIndex('listing_options_category_parent_external_idx');
            }
            if ($this->indexExists('listing_options', 'listing_options_category_external_idx')) {
                $table->dropIndex('listing_options_category_external_idx');
            }
        });

        Schema::table('listing_options', function (Blueprint $table) {
            if (Schema::hasColumn('listing_options', 'source_payload')) {
                $table->dropColumn('source_payload');
            }
            if (Schema::hasColumn('listing_options', 'last_synced_at')) {
                $table->dropColumn('last_synced_at');
            }
            if (Schema::hasColumn('listing_options', 'external_id')) {
                $table->dropColumn('external_id');
            }
            if (Schema::hasColumn('listing_options', 'external_source')) {
                $table->dropColumn('external_source');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();
        if ($driver === 'sqlite') {
            $rows = $connection->select("SELECT name FROM sqlite_master WHERE type = 'index' AND name = ?", [$indexName]);

            return count($rows) > 0;
        }

        $db = $connection->getDatabaseName();
        $rows = $connection->select(
            'SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$db, $table, $indexName]
        );

        return isset($rows[0]) && (int) ($rows[0]->c ?? 0) > 0;
    }
};
