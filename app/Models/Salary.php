<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'from_date',
        'to_date',
        'salary_month',
        'payment_date',
        'basic_salary',
        'bonus',
        'deductions',
        'net_salary',
        'payment_method',
        'account_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'from_date'    => 'date',
        'to_date'      => 'date',
        'payment_date' => 'date',
        'basic_salary' => 'decimal:2',
        'bonus'        => 'decimal:2',
        'deductions'   => 'decimal:2',
        'net_salary'   => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
