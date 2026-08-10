<?php

namespace App\Models;

use App\Models\Concerns\SanitizesRichHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations, HasSEO, SanitizesRichHtml;

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
