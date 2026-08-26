<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'member_id',
        'service_id',
        'price',
        'quantity',
        'paid_amount',
        'payment_status',
        'status',
        'order_date',
        'due_date',
        'completed_date',
        'measurements',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'paid_amount' => 'decimal:2',
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function totalAmount(): float
    {
        return round((float) $this->price * (int) $this->quantity, 2);
    }

    public function remainingDue(): float
    {
        return max(0, $this->totalAmount() - (float) $this->paid_amount);
    }

    public function refreshPaymentStatus(): void
    {
        if ((float) $this->paid_amount <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->remainingDue() <= 0) {
            $this->payment_status = 'paid';
            if (in_array($this->status, ['pending', 'in_progress'])) {
                $this->status = 'completed';
                $this->completed_date = now();
            }
        } else {
            $this->payment_status = 'partial';
        }
    }
}
