<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug', 'title', 'content', 'featured_image', 
        'seo_meta_description', 'published_at'
    ];

    public $translatable = ['title', 'content', 'seo_meta_description'];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}

