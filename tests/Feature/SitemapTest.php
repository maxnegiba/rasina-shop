<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_public_products_and_excludes_drafts(): void
    {
        $category = Category::create([
            'name' => ['ro' => 'Cruci'],
            'slug' => 'cruci-'.Str::random(8),
        ]);
        $published = Product::create([
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs public'],
            'slug' => 'public-'.Str::random(8),
            'price' => 100,
            'stock' => 1,
            'status' => 'published',
        ]);
        $draft = Product::create([
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs ciornă'],
            'slug' => 'draft-'.Str::random(8),
            'price' => 100,
            'stock' => 1,
            'status' => 'draft',
        ]);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('shop.show', $published->slug), false)
            ->assertDontSee(route('shop.show', $draft->slug), false);
    }
}
