<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'action',
        'description',
        'meta',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'meta'      => 'array',
            'logged_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}