<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportDatabaseSql extends Command
{
    protected $signature   = 'db:export-sql';
    protected $description = 'Export the entire database to a SQL file';

    public function handle(): void
    {
        $database   = config('database.connections.mysql.database');
        $username   = config('database.connections.mysql.username');
        $password   = config('database.connections.mysql.password');
        $host       = config('database.connections.mysql.host');
        $port       = config('database.connections.mysql.port');

        $filename   = $database . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $outputPath = storage_path('app/' . $filename);

        $mysqldump  = $this->findMysqldump();

        if (!$mysqldump) {
            $this->error('mysqldump not found. Please install MySQL client tools.');
            return;
        }

        $this->info("Exporting database '{$database}' to {$filename}...");

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s > %s',
            escapeshellarg($mysqldump),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            empty($password) ? '' : '--password=' . escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($outputPath)
        );

        $returnCode = null;
        system($command, $returnCode);

        if ($returnCode !== 0) {
            $this->error('Export failed. Check your DB credentials.');
            return;
        }

        $this->info('Database exported successfully to:');
        $this->line($outputPath);
    }

    private function findMysqldump(): ?string
    {
        $locations = [
            '/opt/homebrew/Cellar/mysql-client/9.2.0/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            '/opt/homebrew/opt/mysql-client/bin/mysqldump',
            '/usr/bin/mysqldump',
            '/usr/local/opt/mysql-client/bin/mysqldump',
        ];

        foreach ($locations as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // fallback: try to find it via shell
        $result = shell_exec('which mysqldump 2>/dev/null');
        if ($result) {
            return trim($result);
        }

        return null;
    }
}