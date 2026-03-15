<?php

namespace App\Events;

use App\Models\Employee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalaryChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Employee $employee,
        public float    $oldSalary,
        public float    $newSalary,
    ) {}

    // Which channel to broadcast on
    public function broadcastOn(): array
    {
        return [
            new Channel('salary-changes'),
        ];
    }

    // Event name the frontend listens to
    public function broadcastAs(): string
    {
        return 'salary.changed';
    }

    // Data sent with the broadcast
    public function broadcastWith(): array
    {
        return [
            'employee_id'   => $this->employee->id,
            'employee_name' => $this->employee->name,
            'old_salary'    => $this->oldSalary,
            'new_salary'    => $this->newSalary,
            'changed_at'    => now()->toIso8601String(),
        ];
    }
}