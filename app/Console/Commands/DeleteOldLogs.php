<?php

namespace App\Console\Commands;

use App\Models\EmployeeLog;
use Illuminate\Console\Command;

class DeleteOldLogs extends Command
{
    protected $signature   = 'logs:delete-old';
    protected $description = 'Delete employee logs older than one month';

    public function handle(): void
    {
        $cutoff = now()->subMonth();

        $count = EmployeeLog::where('logged_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info('No old logs found.');
            return;
        }

        EmployeeLog::where('logged_at', '<', $cutoff)->delete();

        $this->info("Deleted {$count} log(s) older than {$cutoff->toDateString()}.");
    }
}