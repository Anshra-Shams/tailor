<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'member_id',
        'service_id',
        'price',
        'status',
        'order_date',
        'due_date',
        'completed_date',
        'measurements',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'order_date' => 'date',
        'due_date' => 'date',
        'completed_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
