<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportEmployeesJson extends Command
{
    protected $signature   = 'employees:export-json';
    protected $description = 'Export all employee data to a JSON file';

    public function handle(): void
    {
        $this->info('Fetching employees...');

        $employees = Employee::with(['manager', 'position'])
            ->latest()
            ->get()
            ->map(fn($employee) => [
                'id'                     => $employee->id,
                'name'                   => $employee->name,
                'email'                  => $employee->email,
                'salary'                 => (float) $employee->salary,
                'is_founder'             => $employee->is_founder,
                'manager'                => $employee->manager?->name,
                'position'               => $employee->position?->title,
                'last_salary_changed_at' => $employee->last_salary_changed_at
                                            ? \Carbon\Carbon::parse($employee->last_salary_changed_at)->toDateTimeString()
                                            : null,
                'created_at'             => \Carbon\Carbon::parse($employee->created_at)->toDateTimeString(),
            ]);

        if ($employees->isEmpty()) {
            $this->warn('No employees found.');
            return;
        }

        $filename   = 'employees_' . now()->format('Y-m-d_H-i-s') . '.json';
        $outputPath = storage_path('app/' . $filename);

        File::put($outputPath, json_encode($employees, JSON_PRETTY_PRINT));

        $this->info("Exported {$employees->count()} employee(s) to:");
        $this->line($outputPath);
    }
}