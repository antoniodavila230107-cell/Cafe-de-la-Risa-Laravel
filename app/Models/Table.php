<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Table extends Model
{
    protected $fillable = [
        'zone_id',
        'number',
        'capacity',
        'status',
        'reserved_until',
        'current_order_folio',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'reserved_until' => 'datetime',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}
