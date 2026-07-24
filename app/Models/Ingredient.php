<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ingredient extends Model
{
    protected $fillable = [
        'ingredient_category_id',
        'sku',
        'name',
        'unit',
        'unit_cost',
        'min_stock',
        'active',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:4',
        'min_stock' => 'decimal:3',
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
