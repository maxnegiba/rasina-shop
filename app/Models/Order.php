<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'payment_status', 
        'shipping_status', 'customer_details', 'stripe_transaction_id',
        'stripe_checkout_session_id', 'proforma_number', 'public_token',
        'stock_reserved_at', 'stock_released_at', 'terms_accepted_at',
        'terms_version', 'confirmation_sent_at',
    ];

    protected $casts = [
        'customer_details' => 'array', // Transformă automat JSON-ul din DB în array PHP
        'total_amount' => 'decimal:2',
        'stock_reserved_at' => 'datetime',
        'stock_released_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
        'confirmation_sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            $order->public_token ??= (string) Str::uuid();
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function proformaNumber(): string
    {
        $year = ($this->created_at ?? now())->format('Y');

        return sprintf('PROFORMA-%s-%06d', $year, $this->getKey());
    }

    public function releaseReservedStock(): void
    {
        DB::transaction(function (): void {
            /** @var self $order */
            $order = self::query()->lockForUpdate()->findOrFail($this->getKey());

            if (! $order->stock_reserved_at || $order->stock_released_at || $order->payment_status === 'paid') {
                return;
            }

            $order->load('items');

            foreach ($order->items as $item) {
                if ($item->product_id) {
                    DB::table('products')
                        ->where('id', $item->product_id)
                        ->increment('stock', $item->quantity);
                }
            }

            $order->update(['stock_released_at' => now()]);
        });
    }
}
