<?php

namespace App\Models\Concerns;

use App\Services\SafeHtmlSanitizer;
use Illuminate\Database\Eloquent\Model;

trait SanitizesRichHtml
{
    public static function bootSanitizesRichHtml(): void
    {
        static::saving(fn (Model $model) => self::sanitizeRichHtml($model));
        static::retrieved(fn (Model $model) => self::sanitizeRichHtml($model));
    }

    private static function sanitizeRichHtml(Model $model): void
    {
        if (! method_exists($model, 'getTranslations') || ! method_exists($model, 'setTranslations')) {
            return;
        }

        $translations = $model->getTranslations('content');
        $sanitizer = app(SafeHtmlSanitizer::class);

        foreach ($translations as $locale => $html) {
            $translations[$locale] = $sanitizer->sanitize((string) $html);
        }

        $model->setTranslations('content', $translations);
    }
}
