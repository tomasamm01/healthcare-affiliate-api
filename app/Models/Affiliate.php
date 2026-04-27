<?php

namespace App\Models;

use App\Enums\AffiliateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'dni',
        'status',
        'plan_id',
        'holder_id',
    ];

    protected $casts = [
        'status' => AffiliateStatus::class,
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function dependents(): HasMany
    {
        return $this->hasMany(Affiliate::class, 'holder_id');
    }

    public function holder(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class, 'holder_id');
    }

    public function isHolder(): bool
    {
        return is_null($this->holder_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', AffiliateStatus::ACTIVE);
    }

    public function scopeHolders($query)
    {
        return $query->whereNull('holder_id');
    }

    public function scopeDependents($query)
    {
        return $query->whereNotNull('holder_id');
    }
}
