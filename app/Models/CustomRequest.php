<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'customer_name', 'customer_email', 'customer_phone',
        'dimensions_requested', 'color_preferences', 'special_message',
        'reference_image_path', 'status', 'quoted_price'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
