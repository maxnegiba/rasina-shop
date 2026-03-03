<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations;

    // Aici am adăugat 'image' la final ca să îi dăm voie să îl salveze!
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 
        'price', 'stock', 'is_custom', 'status', 'image' 
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'is_custom' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Această relație o vom folosi mai târziu dacă vrei să faci o galerie completă cu unghiuri diferite
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}