<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $casts = [
        'unit_price' => 'float',
        'total_price' => 'float',
    ];
    protected $guarded = [];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function scopePurchasedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('purchased_at', '=', $date);
    }
}
