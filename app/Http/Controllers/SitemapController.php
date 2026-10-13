<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Support\CanonicalUrl;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect();
        $seen = [];

        $add = static function (string $location, $lastModified = null) use ($urls, &$seen): void {
            if (isset($seen[$location])) {
                return;
            }

            $seen[$location] = true;
            $urls->push([
                'location' => $location,
                'last_modified' => $lastModified,
            ]);
        };

        foreach (['/', '/magazin', '/jurnal', '/despre-noi', '/contact', '/custom-orders'] as $path) {
            $add(CanonicalUrl::path($path));
        }

        Category::query()
            ->whereHas('products', fn ($query) => $query->where('status', 'published'))
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->each(fn (Category $category) => $add(
                CanonicalUrl::path('/magazin/categorie/'.$category->slug),
                $category->updated_at,
            ));

        Product::query()
            ->where('status', 'published')
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->each(fn (Product $product) => $add(
                CanonicalUrl::path('/magazin/produs/'.$product->slug),
                $product->updated_at,
            ));

        Post::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->each(fn (Post $post) => $add(
                CanonicalUrl::path('/jurnal/'.$post->slug),
                $post->updated_at,
            ));

        $allowed = array_values(array_diff(
            (array) config('seo.sitemap_cms_slugs', []),
            (array) config('seo.noindex_cms_slugs', []),
        ));

        if ($allowed !== []) {
            Page::query()
                ->whereIn('slug', $allowed)
                ->select(['id', 'slug', 'updated_at'])
                ->orderBy('id')
                ->each(fn (Page $page) => $add(
                    CanonicalUrl::path('/info/'.$page->slug),
                    $page->updated_at,
                ));
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
