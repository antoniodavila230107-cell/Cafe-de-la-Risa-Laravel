<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IngredientCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
