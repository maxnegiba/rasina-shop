<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_code',
        'quantity',
        'unit_price',
    ];

    protected $appends = ['subtotal'];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function getSubtotalAttribute(): float
    {
        return (float) $this->unit_price * (int) $this->quantity;
    }

    public function displayName(): string
    {
        return $this->product_name
            ?: (string) ($this->product?->name ?: 'Produs indisponibil');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
