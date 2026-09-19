<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_category_id',
        'name',
        'opening_balance',
        'current_balance',
        'type',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }

    // Payment method derived from this account's category
    public function paymentMethod(): string
    {
        $name = strtolower(trim((string) ($this->category?->name ?? $this->name)));

        return match (true) {
            str_contains($name, 'cash')   => 'cash',
            str_contains($name, 'bank')   => 'bank_transfer',
            str_contains($name, 'online') => 'online_payment',
            default                       => 'other',
        };
    }
}
