<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\SalaryChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Mail\EmployeeCreatedMail;
use App\Models\Employee;
use App\Models\EmployeeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    private function log(
        ?Employee $employee,
        string $action,
        string $description,
        array $meta = []
    ): void {
        EmployeeLog::create([
            'employee_id' => $employee?->id,
            'action'      => $action,
            'description' => $description,
            'meta'        => empty($meta) ? null : $meta,
            'logged_at'   => now(),
        ]);
    }

    private function fileLog(string $action, ?Employee $employee = null, string $extra = ''): void
    {
        $name    = $employee ? "[#{$employee->id} {$employee->name}]" : '[bulk]';
        $message = strtoupper($action) . " {$name}";

        if ($extra) {
            $message .= " — {$extra}";
        }

        Log::channel('employee')->info($message);
    }

    public function index(): JsonResponse
    {
        $employees = Employee::with('manager')->latest()->paginate(15);

        return response()->json($employees);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        if ($request->boolean('is_founder')) {
            if (Employee::where('is_founder', true)->exists()) {
                return response()->json([
                    'message' => 'A founder already exists.'
                ], 422);
            }
        }

        $employee = Employee::create($request->validated());

        $this->log($employee, 'created', "Employee {$employee->name} was created.", [
            'name'       => $employee->name,
            'email'      => $employee->email,
            'salary'     => $employee->salary,
            'manager_id' => $employee->manager_id,
        ]);

        if ($employee->manager_id) {
            $manager = Employee::findOrFail($employee->manager_id);
            Mail::to($manager->email)->send(new EmployeeCreatedMail($employee, $manager));
        }

        return response()->json($employee->load('manager', 'position'), 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json($employee->load('manager', 'position'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $data   = $request->validated();
        $before = $employee->only(['name', 'email', 'salary', 'manager_id', 'position_id']);

        $salaryChanged = isset($data['salary'])
            && (float) $data['salary'] !== (float) $employee->salary;

        $oldSalary = (float) $employee->salary;

        if ($salaryChanged) {
            $data['last_salary_changed_at'] = now();
        }

        $employee->update($data);

        $after = $employee->fresh()->only(['name', 'email', 'salary', 'manager_id', 'position_id']);

        $this->log($employee, 'updated', "Employee {$employee->name} was updated.", [
            'before' => $before,
            'after'  => $after,
        ]);

        if ($salaryChanged) {
            event(new SalaryChanged(
                $employee->fresh(),
                $oldSalary,
                (float) $data['salary']
            ));
        }

        return response()->json($employee->fresh()->load('manager', 'position'));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->log($employee, 'deleted', "Employee {$employee->name} was deleted.", [
            'name'   => $employee->name,
            'email'  => $employee->email,
            'salary' => $employee->salary,
        ]);
        $this->fileLog('deleted', $employee);

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully.']);
    }

    public function logs(Employee $employee): JsonResponse
    {
        $logs = EmployeeLog::where('employee_id', $employee->id)
            ->latest('logged_at')
            ->get();

        return response()->json([
            'employee' => $employee->name,
            'count'    => $logs->count(),
            'logs'     => $logs
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $name   = $request->query('name');
        $salary = $request->query('salary');

        $employees = Employee::with('manager')
            ->when($name,   fn($q) => $q->where('name', 'like', "%{$name}%"))
            ->when($salary, fn($q) => $q->where('salary', $salary))
            ->latest()
            ->get();

        return response()->json([
            'count'     => $employees->count(),
            'employees' => $employees
        ]);
    }

    public function hierarchy(Employee $employee): JsonResponse
    {
        $employee->load('manager.manager.manager');
        $chain = $employee->getManagerChain();

        return response()->json([
            'hierarchy' => $chain->pluck('name')->toArray()
        ]);
    }

    public function hierarchyWithSalary(Employee $employee): JsonResponse
    {
        $employee->load('manager.manager.manager');
        $chain  = $employee->getManagerChain();
        $result = $chain->mapWithKeys(fn($emp) => [
            $emp->name => (float) $emp->salary
        ]);

        return response()->json([
            'hierarchy' => $result
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $employees = Employee::with('manager')->latest()->get();

        $this->log(null, 'exported', 'Employee data was exported to CSV.');
        $this->fileLog('exported', null, 'CSV file downloaded');

        $callback = function () use ($employees) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'Email', 'Salary', 'Manager', 'Is Founder', 'Created At']);

            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->id,
                    $employee->name,
                    $employee->email,
                    $employee->salary,
                    $employee->manager?->name ?? 'No Manager',
                    $employee->is_founder ? 'Yes' : 'No',
                    $employee->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $csv = Reader::createFromPath($request->file('file')->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($csv->getRecords() as $index => $record) {

            if (Employee::where('email', $record['email'])->exists()) {
                $skipped++;
                continue;
            }

            if (!empty($record['is_founder']) && $record['is_founder'] == 1) {
                if (Employee::where('is_founder', true)->exists()) {
                    $errors[] = "Row {$index}: founder already exists, skipped.";
                    $skipped++;
                    continue;
                }
            }

            try {
                $employee = Employee::create([
                    'name'       => $record['name'],
                    'email'      => $record['email'],
                    'salary'     => $record['salary'],
                    'manager_id' => !empty($record['manager_id']) ? $record['manager_id'] : null,
                    'is_founder' => !empty($record['is_founder']) ? (bool) $record['is_founder'] : false,
                ]);

                $this->log($employee, 'imported', "Employee {$employee->name} was imported from CSV.");
                $imported++;

            } catch (\Exception $e) {
                $errors[] = "Row {$index}: " . $e->getMessage();
                $skipped++;
            }
        }

        $this->log(null, 'imported', "CSV import completed: {$imported} imported, {$skipped} skipped.");
        $this->fileLog('imported', null, "{$imported} records imported, {$skipped} skipped");

        return response()->json([
            'message'  => 'Import completed.',
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ]);
    }

    public function noSalaryChange(int $months): JsonResponse
    {
        $cutoff = now()->subMonths($months);

        $employees = Employee::with('manager')
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_salary_changed_at')
                      ->orWhere('last_salary_changed_at', '<=', $cutoff);
            })
            ->latest()
            ->get();

        return response()->json([
            'months'    => $months,
            'cutoff'    => $cutoff->toDateString(),
            'count'     => $employees->count(),
            'employees' => $employees
        ]);
    }
}