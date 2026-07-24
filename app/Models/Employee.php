<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_role',
        'salary',
        'salary_period',
        'register_station',
        'active',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function tables(): BelongsToMany
    {
        return $this->belongsToMany(Table::class, 'employee_table');
    }

    public function getFormattedRoleAttribute(): string
    {
        return match ($this->job_role) {
            'mesero'        => '🍷 Mesero',
            'recepcionista' => '📋 Recepcionista',
            'cocinero'      => '👨‍🍳 Cocinero',
            default         => ucfirst($this->job_role),
        };
    }

    public function getMonthlySalaryAttribute(): float
    {
        return match ($this->salary_period) {
            'diario'    => (float) $this->salary * 30,
            'quincenal' => (float) $this->salary * 2,
            default     => (float) $this->salary,
        };
    }
}
