<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_featured',
        'sort_order',
        'alt_text',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'alt_text' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function translatedAltText(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $values = is_array($this->alt_text) ? $this->alt_text : [];

        return (string) ($values[$locale] ?? $values['ro'] ?? '');
    }
}
