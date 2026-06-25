<?php

namespace Tests\Unit;

use App\Console\Commands\ExportPowerblinkSqlCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MysqlSchemaExportTest extends TestCase
{
    public function test_sqlite_to_mysql_schema_uses_varchar_for_indexed_strings(): void
    {
        $sqlitePath = database_path('schema_export_test.sqlite');
        if (is_file($sqlitePath)) {
            File::delete($sqlitePath);
        }

        Config::set('database.default', 'schema_export_test');
        Config::set('database.connections.schema_export_test', [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('schema_export_test');
        DB::reconnect('schema_export_test');

        Artisan::call('migrate', ['--force' => true]);

        $command = app(ExportPowerblinkSqlCommand::class);
        $method = new \ReflectionMethod($command, 'buildMysqlSchemaFromSqlite');
        $method->setAccessible(true);
        $schema = $method->invoke($command, $sqlitePath);

        $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS=0;', $schema);
        $this->assertStringContainsString('`reference` VARCHAR(255) NOT NULL', $schema);
        $this->assertStringContainsString('UNIQUE KEY `academy_payments_reference_unique` (`reference`)', $schema);
        $this->assertStringNotContainsString('`reference` TEXT NOT NULL', $schema);

        File::delete($sqlitePath);
    }
}
