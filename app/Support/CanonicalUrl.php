<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * One source of truth for the public, indexable MTD ART URLs.
 *
 * The canonical host is never inferred from an incoming Host header.
 */
final class CanonicalUrl
{
    public static function origin(): string
    {
        return rtrim((string) config('seo.canonical_origin', 'https://www.mtdart.ro'), '/');
    }

    public static function path(string $path = '/'): string
    {
        $path = '/'.ltrim($path, '/');

        return self::origin().$path;
    }

    public static function forRequest(Request $request): string
    {
        $url = self::path($request->getPathInfo());

        // Page 2+ displays a different product/post subset and remains self-canonical.
        // All campaign and tracking parameters are intentionally discarded.
        if ($request->routeIs('shop.index', 'shop.category', 'blog.index')) {
            $page = $request->query('page');

            if (is_scalar($page)
                && preg_match('/^[1-9][0-9]*$/D', (string) $page)
                && (int) $page > 1) {
                $url .= '?page='.(int) $page;
            }
        }

        return $url;
    }

    public static function isPublicSeoRoute(Request $request): bool
    {
        return $request->routeIs(
            'home',
            'about',
            'contact',
            'custom-orders',
            'shop.index',
            'shop.category',
            'shop.show',
            'blog.index',
            'blog.show',
            'page.show',
        );
    }
}
