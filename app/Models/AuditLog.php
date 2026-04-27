<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity',
        'entity_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function scopeForEntity($query, string $entity)
    {
        return $query->where('entity', $entity);
    }

    public function scopeForEntityId($query, int $entityId)
    {
        return $query->where('entity_id', $entityId);
    }
}
