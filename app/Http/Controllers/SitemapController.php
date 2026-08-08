<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['location' => route('home'), 'last_modified' => null],
            ['location' => route('shop.index'), 'last_modified' => null],
            ['location' => route('blog.index'), 'last_modified' => null],
            ['location' => route('about'), 'last_modified' => null],
            ['location' => route('contact'), 'last_modified' => null],
        ]);

        Category::query()->select(['slug', 'updated_at'])->orderBy('id')->each(
            fn (Category $category) => $urls->push([
                'location' => route('shop.category', $category->slug),
                'last_modified' => $category->updated_at,
            ])
        );

        Product::query()
            ->where('status', 'published')
            ->select(['slug', 'updated_at'])
            ->orderBy('id')
            ->each(fn (Product $product) => $urls->push([
                'location' => route('shop.show', $product->slug),
                'last_modified' => $product->updated_at,
            ]));

        Post::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->select(['slug', 'updated_at'])
            ->orderBy('id')
            ->each(fn (Post $post) => $urls->push([
                'location' => route('blog.show', $post->slug),
                'last_modified' => $post->updated_at,
            ]));

        Page::query()->select(['slug', 'updated_at'])->orderBy('id')->each(
            fn (Page $page) => $urls->push([
                'location' => route('page.show', $page->slug),
                'last_modified' => $page->updated_at,
            ])
        );

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
