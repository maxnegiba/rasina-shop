<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations, HasSEO;

    protected $fillable = [
        'product_code',
        'product_type',
        'category_id',
        'related_post_id',
        'name',
        'slug',
        'description',
        'seo_translations',
        'price',
        'stock',
        'is_custom',
        'status',
        'image',
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'is_custom' => 'boolean',
        'price' => 'decimal:2',
        'seo_translations' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function relatedPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'related_post_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function displayPrice(): string
    {
        if ($this->price === null) {
            return 'Preț la cerere';
        }

        return number_format((float) $this->price, 0, ',', '.') . ' RON';
    }

    public function isPurchasable(): bool
    {
        return $this->price !== null
            && (float) $this->price > 0
            && (int) $this->stock > 0;
    }

    public function isSold(): bool
    {
        return (int) $this->stock <= 0;
    }

    public function getDynamicSEOData(): SEOData
    {
        if (! $this->exists) {
            return new SEOData();
        }

        $locale = app()->getLocale();
        $translations = is_array($this->seo_translations) ? $this->seo_translations : [];
        $localizedSeo = $translations[$locale] ?? $translations['ro'] ?? [];

        $featuredImage = $this->images->firstWhere('is_featured', true)
            ?? $this->images->first();

        $imagePath = $featuredImage
            ? asset('storage/' . $featuredImage->image_path)
            : asset('/img/logo.png');

        return new SEOData(
            title: $localizedSeo['title'] ?? $this->name,
            description: $localizedSeo['description'] ?? strip_tags((string) $this->description),
            author: $translations['author'] ?? 'MTD ART',
            image: $imagePath,
            robots: $translations['robots'] ?? 'index, follow',
        );
    }
}
