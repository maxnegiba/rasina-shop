<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'payment_status', 
        'shipping_status', 'customer_details', 'stripe_transaction_id',
        'invoice_series', 'invoice_number'
    ];

    protected $casts = [
        'customer_details' => 'array', // Transformă automat JSON-ul din DB în array PHP
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
