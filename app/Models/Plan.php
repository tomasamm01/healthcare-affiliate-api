<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'coverage_details',
        'active',
    ];

    protected $casts = [
        'coverage_details' => 'array',
        'active' => 'boolean',
    ];

    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
