<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Post extends Model
{
    use HasFactory, HasTranslations, HasSEO;

    protected $fillable = [
        'slug', 'title', 'content', 'featured_image', 
        'seo_meta_description', 'published_at', 'author', 'sort_order'
    ];

    public $translatable = ['title', 'content', 'seo_meta_description'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getDynamicSEOData(): SEOData
    {
        if (! $this->exists) {
            return new SEOData();
        }

        $imagePath = $this->featured_image
                     ? asset('storage/' . $this->featured_image)
                     : asset('/img/logo.png');

        return new SEOData(
            title: $this->title,
            description: $this->seo_meta_description ? strip_tags($this->seo_meta_description) : strip_tags(substr($this->content, 0, 160)),
            image: $imagePath,
        );
    }
}

