<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'folio',
        'customer_name',
        'customer_phone',
        'customer_email',
        'service_type',
        'table_id',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'qr_token',
        'qr_used',
        'qr_used_at',
        'preferred_time',
        'notes',
        'card_last_four',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'qr_used' => 'boolean',
        'qr_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
