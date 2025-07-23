<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Purchase extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'float',
        'tax' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    protected function taxPercentage(): Attribute
    {
        return new Attribute(
            get: fn () => number_format(($this->tax / $this->amount) * 100,2),
        );
    }

    protected $appends = ['tax_percentage'];
}
