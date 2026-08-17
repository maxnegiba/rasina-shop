<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Statikbe\CookieConsent\CookieConsentMiddleware;
use Tests\TestCase;

class HomePageProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_available_products_by_creation_date(): void
    {
        $this->withoutMiddleware(CookieConsentMiddleware::class);

        $category = Category::create([
            'name' => ['ro' => 'Cruci'],
            'slug' => 'cruci-test',
        ]);

        $olderProduct = Product::forceCreate([
            'category_id' => $category->id,
            'name' => ['ro' => 'Cruce veche'],
            'slug' => 'cruce-veche',
            'price' => 100,
            'stock' => 1,
            'status' => 'published',
            'created_at' => now()->subDay(),
        ]);
        $newerProduct = Product::forceCreate([
            'category_id' => $category->id,
            'name' => ['ro' => 'Cruce nouă'],
            'slug' => 'cruce-noua',
            'price' => 120,
            'stock' => 1,
            'status' => 'published',
            'created_at' => now(),
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('latestProducts', function ($products) use ($newerProduct, $olderProduct): bool {
                return $products->pluck('id')->all() === [$newerProduct->id, $olderProduct->id];
            });
    }
}
