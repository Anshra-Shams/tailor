<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'gender',
        'address',
        'notes',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
