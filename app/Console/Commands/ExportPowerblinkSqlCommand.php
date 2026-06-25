<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ExportPowerblinkSqlCommand extends Command
{
    protected $signature = 'db:export-powerblink-sql
                            {--fresh : Run migrate:fresh --seed before export (dev/CI only)}
                            {--sqlite : Build export from a temporary SQLite database (no local MySQL required)}
                            {--skip-dump : Skip data dump; only run schema:dump and verification}';

    protected $description = 'Export PowerBlink academy SQL dump and Laravel schema for phpMyAdmin fresh installs';

    public function handle(): int
    {
        if ($this->option('sqlite')) {
            return $this->exportFromSqlite();
        }

        if ($this->option('fresh')) {
            if (app()->environment('production')) {
                $this->error('Refusing migrate:fresh in production.');

                return self::FAILURE;
            }

            $this->warn('Running migrate:fresh --seed...');
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            $this->line(Artisan::output());
        }

        if (Artisan::call('db:verify-migrations-sync') !== 0) {
            $this->error(Artisan::output());

            return self::FAILURE;
        }

        $this->writeSchemaDump(config('database.default'));

        if ($this->option('skip-dump')) {
            $this->info('Skipped data dump (--skip-dump).');

            return self::SUCCESS;
        }

        $connection = config('database.default');
        $config = config('database.connections.'.$connection);
        if (($config['driver'] ?? '') !== 'mysql') {
            $this->warn('No MySQL connection configured. Re-run with --sqlite to generate powerblink_academy.sql without local MySQL.');

            return self::FAILURE;
        }

        return $this->exportFromMysql($config);
    }

    private function exportFromSqlite(): int
    {
        if (app()->environment('production')) {
            $this->error('Refusing SQLite export in production.');

            return self::FAILURE;
        }

        $sqlitePath = database_path('powerblink_export.sqlite');
        if (is_file($sqlitePath)) {
            File::delete($sqlitePath);
        }

        $env = $this->isolatedExportEnvironment($sqlitePath);
        if (! $this->exportCredentialsConfigured($env)) {
            return self::FAILURE;
        }

        $this->warn('Running migrate:fresh --seed on temporary SQLite database...');
        if (! $this->runIsolatedFreshSeed($sqlitePath, $env)) {
            return self::FAILURE;
        }

        Config::set('database.default', 'powerblink_export');
        Config::set('database.connections.powerblink_export', [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('powerblink_export');
        DB::reconnect('powerblink_export');

        if (Artisan::call('db:verify-migrations-sync') !== 0) {
            $this->error(Artisan::output());

            return self::FAILURE;
        }

        $schemaPath = $this->writeMysqlSchemaFromSqlite($sqlitePath);
        $this->info('Wrote '.$schemaPath);

        if ($this->option('skip-dump')) {
            $this->info('Schema written. Skipped data dump (--skip-dump).');

            return self::SUCCESS;
        }

        $header = $this->dumpHeader();
        $body = File::get($schemaPath)."\n\n";
        $body .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = collect(DB::connection('powerblink_export')->select(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        ))->pluck('name');

        foreach ($tables as $table) {
            $rows = DB::connection('powerblink_export')->table($table)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $columns = array_keys((array) $rows->first());
            $columnList = '`'.implode('`, `', $columns).'`';

            foreach ($rows as $row) {
                $values = [];
                foreach ($columns as $column) {
                    $value = $row->{$column} ?? null;
                    if ($table === 'users' && $column === 'remember_token') {
                        $value = null;
                    }
                    $values[] = $this->sqlValue($value);
                }
                $body .= 'INSERT INTO `'.$table.'` ('.$columnList.') VALUES ('.implode(', ', $values).');'."\n";
            }
            $body .= "\n";
        }

        $body .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $outputPath = database_path('powerblink_academy.sql');
        File::put($outputPath, $header.$body);
        $this->info('Wrote '.$outputPath.' (structure + seed data from SQLite export)');

        $this->removeStaleDumps();

        return self::SUCCESS;
    }

    private function runIsolatedFreshSeed(string $sqlitePath, array $env): bool
    {
        $process = new Process(
            [PHP_BINARY, base_path('artisan'), 'migrate:fresh', '--seed', '--force'],
            base_path(),
            $env
        );
        $process->setTimeout(600);
        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->error('migrate:fresh --seed failed in isolated export process.');
            if ($process->getErrorOutput() !== '') {
                $this->line($process->getErrorOutput());
            }

            return false;
        }

        return is_file($sqlitePath);
    }

    /**
     * @return array<string, string>
     */
    private function isolatedExportEnvironment(string $sqlitePath): array
    {
        $env = [];
        foreach (array_merge($_ENV, $_SERVER) as $key => $value) {
            if (! is_string($key) || ! is_scalar($value)) {
                continue;
            }
            $env[$key] = (string) $value;
        }

        $env['APP_ENV'] = 'local';
        $env['DB_CONNECTION'] = 'sqlite';
        $env['DB_DATABASE'] = $sqlitePath;
        $env['CACHE_STORE'] = 'array';
        $env['QUEUE_CONNECTION'] = 'sync';
        $env['SESSION_DRIVER'] = 'array';
        $env['APP_URL'] = (string) config('powerblink.site_url', 'https://powerblinkfc.com');

        return $env;
    }

    /**
     * @param  array<string, string>  $env
     */
    private function exportCredentialsConfigured(array $env): bool
    {
        foreach (['BOOTSTRAP_ADMIN_PASSWORD', 'DEMO_USER_PASSWORD'] as $key) {
            if (trim((string) ($env[$key] ?? '')) === '') {
                $this->error("{$key} must be set before exporting seeded SQL (password hashes are baked into the dump).");

                return false;
            }
        }

        return true;
    }

    private function writeMysqlSchemaFromSqlite(string $sqlitePath): string
    {
        $schemaDir = database_path('schema');
        if (! is_dir($schemaDir)) {
            File::makeDirectory($schemaDir, 0755, true);
        }

        $schemaPath = database_path('schema/mysql-schema.sql');
        File::put($schemaPath, $this->buildMysqlSchemaFromSqlite($sqlitePath));

        return $schemaPath;
    }

    private function buildMysqlSchemaFromSqlite(string $sqlitePath): string
    {
        $pdo = new \PDO('sqlite:'.$sqlitePath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $tables = $pdo->query(
            "SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        )->fetchAll(\PDO::FETCH_ASSOC);

        $lines = [
            'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";',
            'SET time_zone = "+00:00";',
            'SET NAMES utf8mb4;',
            '',
        ];

        foreach ($tables as $table) {
            $name = (string) $table['name'];
            $createSql = (string) ($table['sql'] ?? '');
            $createSqlTypes = $this->parseSqliteCreateColumnTypes($createSql);
            $columns = $pdo->query('PRAGMA table_info('.str_replace('"', '""', $name).')')->fetchAll(\PDO::FETCH_ASSOC);
            $foreignKeys = $pdo->query('PRAGMA foreign_key_list('.str_replace('"', '""', $name).')')->fetchAll(\PDO::FETCH_ASSOC);
            $indexes = $pdo->query('PRAGMA index_list('.str_replace('"', '""', $name).')')->fetchAll(\PDO::FETCH_ASSOC);

            $columnTypes = [];
            $primaryColumns = [];
            $indexedColumns = [];

            foreach ($columns as $column) {
                $columnName = (string) $column['name'];
                $sqliteType = strtolower($createSqlTypes[$columnName] ?? (string) $column['type']);
                $isPrimary = (int) ($column['pk'] ?? 0) > 0;

                if ($isPrimary) {
                    $primaryColumns[] = $columnName;
                    $indexedColumns[] = $columnName;
                }

                $columnTypes[$columnName] = $this->sqliteColumnTypeToMysql(
                    $columnName,
                    $sqliteType,
                    $isPrimary && (int) $column['pk'] === 1,
                );
            }

            foreach ($indexes as $index) {
                if (($index['origin'] ?? '') === 'pk' || (string) ($index['name'] ?? '') === 'primary') {
                    continue;
                }

                $indexName = (string) $index['name'];
                if (str_starts_with($indexName, 'sqlite_autoindex_')) {
                    continue;
                }

                $indexColumns = $pdo->query('PRAGMA index_info('.str_replace('"', '""', $indexName).')')->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($indexColumns as $indexColumn) {
                    $indexedColumns[] = (string) $indexColumn['name'];
                }
            }

            foreach (array_unique($indexedColumns) as $indexedColumn) {
                if (($columnTypes[$indexedColumn] ?? '') === 'TEXT') {
                    $columnTypes[$indexedColumn] = $this->varcharLengthForColumn($indexedColumn);
                }
            }

            $columnDefs = [];

            foreach ($columns as $column) {
                $columnName = (string) $column['name'];
                $notNull = (int) $column['notnull'] === 1;
                $default = $column['dflt_value'];
                $isPrimary = (int) ($column['pk'] ?? 0) > 0;
                $isAutoIncrementPrimary = $isPrimary && (int) $column['pk'] === 1 && $columnName === 'id';

                $mysqlType = $columnTypes[$columnName];
                $definition = '`'.$columnName.'` '.$mysqlType;

                if ($notNull && ! $isAutoIncrementPrimary) {
                    $definition .= ' NOT NULL';
                }

                if ($default !== null && ! $isAutoIncrementPrimary) {
                    $definition .= ' DEFAULT '.$this->mysqlDefaultValue($default);
                }

                $columnDefs[] = $definition;
            }

            if ($primaryColumns !== []) {
                $columnDefs[] = 'PRIMARY KEY (`'.implode('`, `', $primaryColumns).'`)';
            }

            foreach ($foreignKeys as $foreignKey) {
                $onDelete = strtoupper(str_replace(' ', ' ', (string) ($foreignKey['on_delete'] ?? 'NO ACTION')));
                $onUpdate = strtoupper(str_replace(' ', ' ', (string) ($foreignKey['on_update'] ?? 'NO ACTION')));
                $columnDefs[] = sprintf(
                    'CONSTRAINT `%s_%s_foreign` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s ON UPDATE %s',
                    $name,
                    (string) $foreignKey['from'],
                    (string) $foreignKey['from'],
                    (string) $foreignKey['table'],
                    (string) $foreignKey['to'],
                    $onDelete === 'NO ACTION' ? 'RESTRICT' : $onDelete,
                    $onUpdate === 'NO ACTION' ? 'RESTRICT' : $onUpdate
                );
            }

            foreach ($indexes as $index) {
                if (($index['origin'] ?? '') === 'pk' || (string) ($index['name'] ?? '') === 'primary') {
                    continue;
                }

                $indexName = (string) $index['name'];
                if (str_starts_with($indexName, 'sqlite_autoindex_')) {
                    continue;
                }

                $indexColumns = $pdo->query('PRAGMA index_info('.str_replace('"', '""', $indexName).')')->fetchAll(\PDO::FETCH_ASSOC);
                if ($indexColumns === []) {
                    continue;
                }

                $cols = array_map(static fn (array $col): string => '`'.((string) $col['name']).'`', $indexColumns);
                $unique = (int) ($index['unique'] ?? 0) === 1 ? 'UNIQUE ' : '';
                $columnDefs[] = $unique.'KEY `'.$indexName.'` ('.implode(', ', $cols).')';
            }

            $lines[] = 'DROP TABLE IF EXISTS `'.$name.'`;';
            $lines[] = 'CREATE TABLE `'.$name.'` (';
            $lines[] = '  '.implode(",\n  ", $columnDefs);
            $lines[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
            $lines[] = '';
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * @return array<string, string>
     */
    private function parseSqliteCreateColumnTypes(string $createSql): array
    {
        if ($createSql === '' || ! preg_match('/\((.*)\)\s*$/s', $createSql, $matches)) {
            return [];
        }

        $types = [];
        foreach (preg_split('/,\s*(?=(?:[^"]*"[^"]*")*[^"]*$)/', $matches[1]) as $part) {
            $part = trim($part);
            if ($part === '' || str_starts_with(strtoupper($part), 'PRIMARY KEY')
                || str_starts_with(strtoupper($part), 'FOREIGN KEY')
                || str_starts_with(strtoupper($part), 'UNIQUE')
                || str_starts_with(strtoupper($part), 'CONSTRAINT')) {
                continue;
            }

            if (preg_match('/"(?<name>[^"]+)"\s+(?<type>[a-zA-Z0-9_()]+)/', $part, $columnMatch)) {
                $types[$columnMatch['name']] = strtolower($columnMatch['type']);
            }
        }

        return $types;
    }

    private function sqliteColumnTypeToMysql(string $columnName, string $sqliteType, bool $isAutoIncrementPrimary): string
    {
        $sqliteType = strtolower(trim($sqliteType));

        if ($isAutoIncrementPrimary && str_contains($sqliteType, 'int')) {
            return 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT';
        }

        if (preg_match('/varchar\((\d+)\)/', $sqliteType, $matches)) {
            return 'VARCHAR('.$matches[1].')';
        }

        if ($sqliteType === 'varchar' || $sqliteType === 'string') {
            return $this->varcharLengthForColumn($columnName);
        }

        if (preg_match('/char\((\d+)\)/', $sqliteType, $matches)) {
            return 'CHAR('.$matches[1].')';
        }

        return match (true) {
            str_contains($sqliteType, 'tinyint') => 'TINYINT(1)',
            str_contains($sqliteType, 'int') => 'BIGINT UNSIGNED',
            str_contains($sqliteType, 'datetime') => 'DATETIME',
            str_contains($sqliteType, 'timestamp') => 'TIMESTAMP',
            str_contains($sqliteType, 'date') => 'DATE',
            $sqliteType === 'json' => 'JSON',
            str_contains($sqliteType, 'text') => 'TEXT',
            str_contains($sqliteType, 'blob') => 'BLOB',
            str_contains($sqliteType, 'numeric'),
            str_contains($sqliteType, 'decimal'),
            str_contains($sqliteType, 'real') => 'DECIMAL(15, 2)',
            default => $this->varcharLengthForColumn($columnName),
        };
    }

    private function varcharLengthForColumn(string $columnName): string
    {
        return match ($columnName) {
            'remember_token' => 'VARCHAR(100)',
            'currency' => 'VARCHAR(3)',
            default => 'VARCHAR(255)',
        };
    }

    private function mysqlDefaultValue(mixed $default): string
    {
        if ($default === null) {
            return 'NULL';
        }

        $value = (string) $default;
        if ($value === 'NULL') {
            return 'NULL';
        }

        if (preg_match("/^'(.*)'$/s", $value, $matches)) {
            return "'".str_replace("'", "''", $matches[1])."'";
        }

        if (is_numeric($value)) {
            return $value;
        }

        if (in_array(strtoupper($value), ['CURRENT_TIMESTAMP', 'CURRENT_DATE', 'CURRENT_TIME'], true)) {
            return strtoupper($value);
        }

        return "'".str_replace("'", "''", $value)."'";
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function exportFromMysql(array $config): int
    {
        $database = (string) ($config['database'] ?? '');
        $host = (string) ($config['host'] ?? '127.0.0.1');
        $port = (string) ($config['port'] ?? '3306');
        $username = (string) ($config['username'] ?? 'root');
        $password = (string) ($config['password'] ?? '');

        $outputPath = database_path('powerblink_academy.sql');

        $args = [
            'mysqldump',
            '--host='.$host,
            '--port='.$port,
            '--user='.$username,
            '--single-transaction',
            '--routines',
            '--triggers',
            $database,
        ];

        $process = new Process($args, null, $password !== '' ? ['MYSQL_PWD' => $password] : null);
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('mysqldump failed: '.$process->getErrorOutput());
            $this->warn('Tip: run `php artisan db:export-powerblink-sql --fresh --sqlite` when MySQL is not available.');

            return self::FAILURE;
        }

        File::put($outputPath, $this->dumpHeader().$process->getOutput());
        $this->info('Wrote '.$outputPath);

        $this->removeStaleDumps();

        return self::SUCCESS;
    }

    private function writeSchemaDump(string $connection): void
    {
        $schemaDir = database_path('schema');
        if (! is_dir($schemaDir)) {
            File::makeDirectory($schemaDir, 0755, true);
        }

        Artisan::call('schema:dump', ['--database' => $connection]);
        $this->line(Artisan::output());
        $this->info('Wrote '.database_path('schema/mysql-schema.sql'));
    }

    private function dumpHeader(): string
    {
        $commit = trim((string) shell_exec('git rev-parse --short HEAD 2>NUL') ?: 'unknown');

        return sprintf(
            "-- PowerBlink FC academy dump\n-- Generated: %s\n-- Git commit: %s\n-- Seeders: RolesSeeder, AcademyPermissionsSeeder, PowerblinkSiteSettingsSeeder, CmsPagesSeeder, PowerblinkDemoSeeder, MediaSeeder\n\n",
            now()->toIso8601String(),
            $commit
        );
    }

    private function removeStaleDumps(): void
    {
        foreach (['myauwern_torque.sql', 'myauto_torque_db.sql'] as $stale) {
            $path = database_path($stale);
            if (is_file($path)) {
                File::delete($path);
                $this->info('Removed stale dump: '.$stale);
            }
        }
    }

    private function sqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return "'".str_replace(["\\", "'"], ["\\\\", "''"], (string) $value)."'";
    }
}
