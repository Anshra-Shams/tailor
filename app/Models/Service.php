<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'pricing_tiers',
        'estimated_days',
        'measurement_fields',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'pricing_tiers' => 'array',
        'measurement_fields' => 'array',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
