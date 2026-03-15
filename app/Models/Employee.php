<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'salary',
        'manager_id',
        'position_id',
        'is_founder',
        'last_salary_changed_at',
    ];

    protected function casts(): array
    {
        return [
            'salary'     => 'decimal:2',
            'is_founder' => 'boolean',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function getManagerChain(): \Illuminate\Support\Collection
    {
        $chain   = collect([$this]);
        $current = $this;

        while ($current->manager_id !== null) {
            $current = $current->manager;
            $chain->prepend($current);
        }

        return $chain;
    }
}