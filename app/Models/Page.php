<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasTranslations, HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'content',
    ];

    public $translatable = [
        'title',
        'content',
    ];

    public function getDynamicSEOData(): SEOData
    {
        if (! $this->exists) {
            return new SEOData();
        }

        return new SEOData(
            title: $this->title,
            description: Str::limit(
                preg_replace('/\s+/u', ' ', strip_tags((string) $this->content)) ?: '',
                160,
                '',
            ),
        );
    }
}
