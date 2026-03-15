<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveLogFiles extends Command
{
    protected $signature   = 'logs:remove-files';
    protected $description = 'Remove all log files from storage/logs';

    public function handle(): void
    {
        $files = File::glob(storage_path('logs/*.log'));

        if (empty($files)) {
            $this->info('No log files found.');
            return;
        }

        foreach ($files as $file) {
            File::delete($file);
            $this->line('Deleted: ' . basename($file));
        }

        $this->info(count($files) . ' log file(s) removed successfully.');
    }
}