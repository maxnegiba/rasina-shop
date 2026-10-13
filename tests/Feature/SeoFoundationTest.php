<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SeoFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_emit_exactly_one_www_canonical(): void
    {
        $this->withoutVite();

        foreach (['/', '/contact', '/custom-orders', '/magazin'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            preg_match_all('/<link\s+rel="canonical"[^>]*>/i', $html, $tags);

            $this->assertCount(1, $tags[0], $path);
            $this->assertStringContainsString(
                'href="https://www.mtdart.ro'.$path.'"',
                $tags[0][0],
            );
        }
    }

    public function test_canonical_ignores_tracking_parameters_but_keeps_real_pagination(): void
    {
        $this->withoutVite();

        $html = $this->get('/magazin?page=2&utm_source=test&fbclid=abc')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(
            '<link rel="canonical" href="https://www.mtdart.ro/magazin?page=2">',
            $html,
        );
        $this->assertStringNotContainsString('href="https://www.mtdart.ro/magazin?page=2&utm_', $html);
    }

    public function test_sitemap_exposes_only_public_canonical_urls(): void
    {
        $category = Category::create([
            'name' => ['ro' => 'Cruci'],
            'slug' => 'cruci-test',
        ]);

        foreach ([
            ['produs-public', 'published', 0],
            ['produs-ciorna', 'draft', 1],
        ] as [$slug, $status, $stock]) {
            Product::create([
                'category_id' => $category->id,
                'name' => ['ro' => $slug],
                'slug' => $slug,
                'price' => 100,
                'stock' => $stock,
                'status' => $status,
            ]);
        }

        Post::create([
            'slug' => 'articol-public',
            'title' => ['ro' => 'Articol public'],
            'content' => ['ro' => 'Text public'],
            'published_at' => now()->subDay(),
        ]);
        Post::create([
            'slug' => 'articol-viitor',
            'title' => ['ro' => 'Articol viitor'],
            'content' => ['ro' => 'Text viitor'],
            'published_at' => now()->addDay(),
        ]);
        Page::create([
            'slug' => 'pagina-fara-prioritate',
            'title' => ['ro' => 'Fără prioritate'],
            'content' => ['ro' => 'Text'],
        ]);

        $response = $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = $response->getContent();
        $this->assertNotFalse(simplexml_load_string($xml));
        $this->assertStringContainsString('https://www.mtdart.ro/custom-orders', $xml);
        $this->assertStringContainsString('https://www.mtdart.ro/magazin/produs/produs-public', $xml);
        $this->assertStringContainsString('https://www.mtdart.ro/jurnal/articol-public', $xml);
        $this->assertStringNotContainsString('produs-ciorna', $xml);
        $this->assertStringNotContainsString('articol-viitor', $xml);
        $this->assertStringNotContainsString('pagina-fara-prioritate', $xml);
        $this->assertStringNotContainsString('https://mtdart.ro/', $xml);

        $locations = (array) simplexml_load_string($xml);
        $all = [];
        foreach (simplexml_load_string($xml)->url as $url) {
            $all[] = (string) $url->loc;
        }

        $this->assertCount(count(array_unique($all)), $all);
        $this->assertNotContains('https://www.mtdart.ro/checkout', $all);
    }
}
