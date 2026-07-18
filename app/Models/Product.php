<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Product extends Model
{
    use HasFactory, HasTranslations, HasSEO;

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

    public function getDynamicSEOData(): SEOData
    {
        if (!$this->exists) {
            return new SEOData(
                title: $this->name ?? 'New Product',
                image: asset('/img/logo.png'),
            );
        }

        $featuredImage = $this->images?->where('is_featured', true)->first()
                         ?? $this->images?->first();

        $imagePath = $featuredImage
                     ? asset('storage/' . $featuredImage->image_path)
                     : asset('/img/logo.png');

        return new SEOData(
            title: $this->name,
            description: strip_tags($this->description),
            image: $imagePath,
        );
    }
}