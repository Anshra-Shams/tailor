<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_logo',
        'phone',
        'address',
        'website_link',
        'invoice_terms',
    ];

    /**
     * Retrieve the singleton settings record.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'company_name'  => 'Meco',
            'phone'         => '0223-860680 - 0223862252',
            'address'       => 'SHOP # 5 & 6 RED CRESCENT HOSPITAL MARKET LATIFABAD # 6, HYDERABAD',
            'website_link'  => 'www.Meco.Com.Pk',
            'invoice_terms' => "Due Electricity failure delivery can be delay\nAfter one month of Deliver date we wouldn't accept any claim",
        ]);
    }

    /**
     * Get the full URL to the company logo, or null if none uploaded.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->company_logo && file_exists(public_path('uploads/logos/' . $this->company_logo))) {
            return asset('uploads/logos/' . $this->company_logo);
        }
        return null;
    }
}
