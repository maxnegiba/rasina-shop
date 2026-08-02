<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, HasTranslations, HasSEO;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'seo_translations',
        'featured_image',
        'seo_meta_description',
        'published_at',
        'author',
        'sort_order',
    ];

    public $translatable = ['title', 'content', 'seo_meta_description'];

    protected $casts = [
        'published_at' => 'datetime',
        'seo_translations' => 'array',
    ];

    public function relatedProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'related_post_id');
    }

    public function getDynamicSEOData(): SEOData
    {
        if (! $this->exists) {
            return new SEOData();
        }

        $locale = app()->getLocale();
        $translations = is_array($this->seo_translations) ? $this->seo_translations : [];
        $localizedSeo = $translations[$locale] ?? $translations['ro'] ?? [];

        $imagePath = $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : asset('/img/logo.png');

        return new SEOData(
            title: $localizedSeo['title'] ?? $this->title,
            description: $localizedSeo['description']
                ?? ($this->seo_meta_description ?: strip_tags(mb_substr((string) $this->content, 0, 160))),
            author: $translations['author'] ?? ($this->author ?: 'MTD ART'),
            image: $imagePath,
            robots: $translations['robots'] ?? 'index, follow',
        );
    }
}
