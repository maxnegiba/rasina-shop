<?php

namespace App\Models;

use App\Services\MarketingAttribution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'subtotal_amount', 'shipping_amount',
        'discount_amount', 'total_amount', 'payment_status', 'payment_method',
        'shipping_status', 'customer_details', 'stripe_transaction_id',
        'stripe_checkout_session_id', 'proforma_number', 'public_token',
        'stock_reserved_at', 'stock_released_at', 'terms_accepted_at',
        'privacy_acknowledged_at', 'terms_version', 'confirmation_sent_at',
        'confirmation_queued_at', 'confirmation_failed_at',
        'admin_notification_queued_at', 'admin_notification_sent_at',
        'admin_notification_failed_at', 'cancelled_at', 'cancellation_reason',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'marketing_attribution',
    ];

    protected $casts = [
        'customer_details' => 'array',
        'marketing_attribution' => 'array',
        'subtotal_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'stock_reserved_at' => 'datetime',
        'stock_released_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
        'privacy_acknowledged_at' => 'datetime',
        'confirmation_sent_at' => 'datetime',
        'confirmation_queued_at' => 'datetime',
        'confirmation_failed_at' => 'datetime',
        'admin_notification_queued_at' => 'datetime',
        'admin_notification_sent_at' => 'datetime',
        'admin_notification_failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            $order->public_token ??= (string) Str::uuid();

            if (! app()->bound('request')) {
                return;
            }

            $attributes = app(MarketingAttribution::class)->orderAttributes(request());

            foreach ($attributes as $key => $value) {
                if ($order->getAttribute($key) === null) {
                    $order->setAttribute($key, $value);
                }
            }
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

    public function isCashOnDelivery(): bool
    {
        return $this->payment_method === 'cash_on_delivery';
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function cancel(?string $reason = null): void
    {
        DB::transaction(function () use ($reason): void {
            /** @var self $order */
            $order = self::query()->lockForUpdate()->findOrFail($this->getKey());

            if ($order->cancelled_at) {
                return;
            }

            if ($order->shipping_status === 'delivered') {
                throw new \LogicException('O comandă deja livrată nu poate fi anulată.');
            }

            if ($order->payment_status !== 'paid') {
                $order->releaseReservedStock();
                $order->refresh();
            }

            $order->update([
                'cancelled_at' => now(),
                'cancellation_reason' => filled($reason) ? trim($reason) : null,
            ]);
        });

        $this->refresh();
    }

    public function deleteSafelyFromAdmin(): void
    {
        if ($this->payment_status === 'paid') {
            throw new \LogicException('Comenzile plătite nu pot fi șterse. Anulează comanda și gestionează rambursarea separat.');
        }

        $this->releaseReservedStock();
        $this->delete();
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
