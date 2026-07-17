<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'payment_status', 
        'shipping_status', 'customer_details', 'stripe_transaction_id',
        'proforma_number'
    ];

    protected $casts = [
        'customer_details' => 'array', // Transformă automat JSON-ul din DB în array PHP
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generează un proforma_number secvențial unic.
     */
    public static function generateProformaNumber(): string
    {
        return DB::transaction(function () {
            // Find the highest existing proforma number
            $lastProforma = static::whereNotNull('proforma_number')
                ->orderByRaw('CAST(SUBSTR(proforma_number, 10) AS INTEGER) DESC')
                ->first();

            $nextNumber = 1;

            if ($lastProforma && preg_match('/^PROFORMA-(\d+)$/', $lastProforma->proforma_number, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }

            return 'PROFORMA-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
