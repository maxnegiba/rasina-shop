<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class ImportMtdArtCatalogV2 extends Command
{
    protected $signature = 'mtd:import-catalog-v2
        {source=storage/app/imports/mtd-art-v2 : Directorul catalogului v2}
        {--publish-products : Publică produsele cu preț și stoc pozitiv}
        {--publish-posts : Publică articolele imediat}
        {--replace-images : Înlocuiește galeria DB pentru produsele importate}
        {--reset-stock : Resetează stocul produselor existente la valoarea din catalog}
        {--image-quality=82 : Calitatea WebP, între 60 și 95}
        {--max-image-size=1800 : Latura maximă a imaginii în pixeli}
        {--no-backup : Nu creează backup JSON înainte de import}
        {--dry-run : Validează catalogul și conversia WebP fără a scrie date}';

    protected $description = 'Importă catalogul complet MTD ART v2: 4 categorii, RO/EN, SEO, WebP, alt text, galerie ordonată și articole asociate.';

    private ?string $stageRoot = null;
    private array $newPublicFiles = [];
    private array $overwrittenBackups = [];
    private array $warnings = [];

    public function handle(): int
    {
        try {
            $root = $this->absolutePath((string) $this->argument('source'));
            $catalog = $this->readJson($root . '/Catalog_Produse_MTD_ART_v2.json');
            $articleFile = $this->readJson($root . '/Articole_MTD_ART_v2.json');
            $categories = (array) ($catalog['categories'] ?? []);
            $products = (array) ($catalog['products'] ?? []);
            $articles = (array) ($articleFile['articles'] ?? []);

            $this->validateCatalog($root, $catalog, $categories, $products, $articles);
            $staged = $this->stageImages($root, $products);
            $this->printPlan($categories, $products, $articles, $staged);

            if ($this->option('dry-run')) {
                $this->newLine();
                $this->info('Dry-run finalizat: JSON-ul, toate imaginile și conversia WebP au fost validate.');
                $this->info('Nu au fost modificate baza de date sau storage/app/public.');
                $this->printWarnings();
                return self::SUCCESS;
            }

            $this->validateDatabaseSchema();

            if (! $this->option('no-backup')) {
                $this->info('Backup înainte de import: ' . $this->createBackup($products, $articles));
            }

            $result = DB::transaction(function () use ($categories, $products, $articles, $staged): array {
                $categoryMap = $this->importCategories($categories);
                $postResult = $this->importPosts($articles, $staged);
                $productResult = $this->importProducts($products, $staged, $categoryMap, $postResult['by_slug']);

                return [
                    'categories' => count($categoryMap),
                    'posts_created' => $postResult['created'],
                    'posts_updated' => $postResult['updated'],
                    'products_created' => $productResult['created'],
                    'products_updated' => $productResult['updated'],
                    'images' => $productResult['images'],
                    'links' => $productResult['links'],
                ];
            }, 3);

            $this->newLine();
            $this->info('Importul complet a fost finalizat cu succes într-o singură tranzacție DB.');
            $this->table(['Element', 'Rezultat'], [
                ['Categorii sincronizate', $result['categories']],
                ['Produse create', $result['products_created']],
                ['Produse actualizate', $result['products_updated']],
                ['Imagini WebP instalate', $result['images']],
                ['Articole create', $result['posts_created']],
                ['Articole actualizate', $result['posts_updated']],
                ['Legături produs–articol', $result['links']],
            ]);
            $this->printWarnings();

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->rollbackPublicFiles();
            $this->newLine();
            $this->error('Import eșuat. Tranzacția DB a fost anulată: ' . $exception->getMessage());
            if ((bool) config('app.debug')) {
                $this->line($exception->getTraceAsString());
            }
            return self::FAILURE;
        } finally {
            $this->cleanupStage();
        }
    }

    private function absolutePath(string $path): string
    {
        if (str_starts_with($path, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $path)) {
            return rtrim($path, '/');
        }

        return rtrim(base_path($path), '/');
    }

    private function readJson(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("Fișier obligatoriu lipsă: {$path}");
        }

        $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($decoded)) {
            throw new RuntimeException("JSON invalid: {$path}");
        }

        return $decoded;
    }

    private function validateCatalog(string $root, array $catalog, array $categories, array $products, array $articles): void
    {
        if ((int) ($catalog['schema_version'] ?? 0) !== 2) {
            throw new RuntimeException('Catalogul trebuie să folosească schema_version=2.');
        }
        if (count($categories) !== 4) {
            throw new RuntimeException('Catalogul trebuie să conțină exact cele 4 categorii MTD ART.');
        }
        if ($products === []) {
            throw new RuntimeException('Catalogul nu conține produse.');
        }

        $categorySlugs = [];
        foreach ($categories as $category) {
            $slug = trim((string) ($category['slug'] ?? ''));
            if ($slug === '' || ! isset($category['name']['ro'], $category['name']['en'])) {
                throw new RuntimeException('Categorie incompletă în JSON.');
            }
            $categorySlugs[$slug] = true;
        }

        $productSlugs = [];
        foreach ($products as $index => $product) {
            foreach (['id', 'slug', 'category_slug', 'name', 'description_html', 'price_ron', 'image_folder', 'images', 'seo'] as $field) {
                if (! array_key_exists($field, $product)) {
                    throw new RuntimeException("Produsul #{$index} nu are câmpul {$field}.");
                }
            }
            $slug = trim((string) $product['slug']);
            if ($slug === '' || isset($productSlugs[$slug])) {
                throw new RuntimeException("Slug de produs gol sau duplicat: {$slug}");
            }
            $productSlugs[$slug] = true;
            if (! isset($categorySlugs[(string) $product['category_slug']])) {
                throw new RuntimeException("Categorie necunoscută pentru {$slug}: {$product['category_slug']}");
            }
            if (! isset($product['name']['ro'], $product['name']['en'], $product['description_html']['ro'], $product['description_html']['en'])) {
                throw new RuntimeException("Traduceri RO/EN incomplete pentru {$slug}.");
            }
            if (! is_numeric($product['price_ron']) || (float) $product['price_ron'] <= 0) {
                throw new RuntimeException("Preț invalid pentru {$slug}.");
            }
            if (! isset($product['seo']['ro']['title'], $product['seo']['ro']['description'], $product['seo']['en']['title'], $product['seo']['en']['description'])) {
                throw new RuntimeException("SEO RO/EN incomplet pentru {$slug}.");
            }

            $featuredCount = 0;
            $sortOrders = [];
            foreach ((array) $product['images'] as $image) {
                foreach (['file', 'featured', 'sort_order', 'alt'] as $field) {
                    if (! array_key_exists($field, $image)) {
                        throw new RuntimeException("Imagine incompletă pentru {$slug}: lipsește {$field}.");
                    }
                }
                $source = $root . '/' . trim((string) $product['image_folder'], '/') . '/' . basename((string) $image['file']);
                if (! is_file($source)) {
                    throw new RuntimeException("Imagine lipsă pentru {$slug}: {$source}");
                }
                if (! isset($image['alt']['ro'], $image['alt']['en'])) {
                    throw new RuntimeException("Alt text RO/EN lipsă pentru {$slug}.");
                }
                $featuredCount += (bool) $image['featured'] ? 1 : 0;
                $order = (int) $image['sort_order'];
                if (isset($sortOrders[$order])) {
                    throw new RuntimeException("Ordine de galerie duplicată pentru {$slug}: {$order}");
                }
                $sortOrders[$order] = true;
            }
            if ($featuredCount !== 1) {
                throw new RuntimeException("{$slug} trebuie să aibă exact o imagine Featured.");
            }
        }

        $articleSlugs = [];
        foreach ($articles as $article) {
            $slug = trim((string) ($article['slug'] ?? ''));
            if ($slug === '' || isset($articleSlugs[$slug])) {
                throw new RuntimeException("Slug de articol gol sau duplicat: {$slug}");
            }
            $articleSlugs[$slug] = true;
            if (! isset($article['title']['ro'], $article['title']['en'], $article['content_html']['ro'], $article['content_html']['en'])) {
                throw new RuntimeException("Traduceri RO/EN incomplete pentru articolul {$slug}.");
            }
            $productSlug = (string) ($article['product_slug'] ?? '');
            if (! isset($productSlugs[$productSlug])) {
                throw new RuntimeException("Articolul {$slug} indică un produs inexistent: {$productSlug}");
            }
        }
    }

    private function printPlan(array $categories, array $products, array $articles, array $staged): void
    {
        $counts = [];
        foreach ($products as $product) {
            $category = (string) $product['category_slug'];
            $counts[$category] = ($counts[$category] ?? 0) + 1;
        }

        $this->table(['Element', 'Număr'], [
            ['Categorii', count($categories)],
            ['Produse', count($products)],
            ['Cruci', $counts['cruci'] ?? 0],
            ['Baticuri', $counts['baticuri'] ?? 0],
            ['Butoni', $counts['butoni'] ?? 0],
            ['Obiecte de decor', $counts['obiecte-de-decor'] ?? 0],
            ['Articole RO/EN', count($articles)],
            ['Imagini validate și convertite în WebP', array_sum(array_map('count', $staged))],
        ]);
    }

    private function stageImages(string $root, array $products): array
    {
        $quality = (int) $this->option('image-quality');
        $maxSize = (int) $this->option('max-image-size');
        if ($quality < 60 || $quality > 95) {
            throw new RuntimeException('--image-quality trebuie să fie între 60 și 95.');
        }
        if ($maxSize < 600 || $maxSize > 4000) {
            throw new RuntimeException('--max-image-size trebuie să fie între 600 și 4000.');
        }

        $this->stageRoot = storage_path('app/mtd-import-stage/' . Str::uuid());
        if (! mkdir($this->stageRoot, 0775, true) && ! is_dir($this->stageRoot)) {
            throw new RuntimeException('Nu pot crea directorul temporar pentru imagini.');
        }

        $staged = [];
        foreach ($products as $product) {
            $slug = (string) $product['slug'];
            foreach ((array) $product['images'] as $image) {
                $source = $root . '/' . trim((string) $product['image_folder'], '/') . '/' . basename((string) $image['file']);
                $base = pathinfo((string) $image['file'], PATHINFO_FILENAME);
                $filename = $base . '.webp';
                $stagePath = $this->stageRoot . '/webp/' . $product['category_slug'] . '/' . $slug . '/' . $filename;
                $destination = 'products/' . $product['category_slug'] . '/' . $slug . '/' . $filename;
                $this->convertToWebp($source, $stagePath, $quality, $maxSize);
                if (! is_file($stagePath) || filesize($stagePath) === 0) {
                    throw new RuntimeException("Conversia WebP a eșuat pentru {$source}");
                }
                $staged[$slug][] = [
                    'staged_path' => $stagePath,
                    'destination' => $destination,
                    'featured' => (bool) $image['featured'],
                    'sort_order' => (int) $image['sort_order'],
                    'alt_text' => (array) $image['alt'],
                ];
            }
        }

        return $staged;
    }

    private function convertToWebp(string $source, string $destination, int $quality, int $maxSize): void
    {
        $directory = dirname($destination);
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException("Nu pot crea directorul {$directory}");
        }

        if (class_exists(\Imagick::class)) {
            $image = new \Imagick($source);
            if (method_exists($image, 'autoOrient')) {
                $image->autoOrient();
            }
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();
            if (max($width, $height) > $maxSize) {
                $image->thumbnailImage($maxSize, $maxSize, true, true);
            }
            $image->stripImage();
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($quality);
            $image->writeImage($destination);
            $image->clear();
            $image->destroy();
            return;
        }

        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            throw new RuntimeException('Conversia WebP necesită extensia PHP Imagick sau GD cu suport WebP.');
        }

        $binary = file_get_contents($source);
        if ($binary === false) {
            throw new RuntimeException("Nu pot citi imaginea {$source}");
        }
        $sourceImage = imagecreatefromstring($binary);
        if ($sourceImage === false) {
            throw new RuntimeException("Format de imagine neacceptat: {$source}");
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        $scale = min(1, $maxSize / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $newWidth, $newHeight, $transparent);
        imagecopyresampled($target, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (! imagewebp($target, $destination, $quality)) {
            imagedestroy($sourceImage);
            imagedestroy($target);
            throw new RuntimeException("Nu pot scrie imaginea WebP: {$destination}");
        }
        imagedestroy($sourceImage);
        imagedestroy($target);
    }

    private function validateDatabaseSchema(): void
    {
        $required = [
            'products' => ['related_post_id', 'seo_translations'],
            'posts' => ['seo_translations'],
            'product_images' => ['sort_order', 'alt_text'],
        ];
        foreach ($required as $table => $columns) {
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    throw new RuntimeException("Schema incompletă: lipsește {$table}.{$column}. Rulează php artisan migrate --force.");
                }
            }
        }
    }

    private function importCategories(array $categories): array
    {
        $map = [];
        foreach ($categories as $data) {
            $category = Category::query()->firstOrNew(['slug' => (string) $data['slug']]);
            $category->setTranslation('name', 'ro', (string) $data['name']['ro']);
            $category->setTranslation('name', 'en', (string) $data['name']['en']);
            $category->setTranslation('description', 'ro', (string) $data['description']['ro']);
            $category->setTranslation('description', 'en', (string) $data['description']['en']);
            $category->parent_id = null;
            $category->save();
            $map[(string) $data['slug']] = $category;
        }

        return $map;
    }

    private function importPosts(array $articles, array $staged): array
    {
        $created = 0;
        $updated = 0;
        $bySlug = [];
        $nextSort = ((int) Post::query()->max('sort_order')) + 1;

        foreach ($articles as $article) {
            $slug = (string) $article['slug'];
            $post = Post::query()->where('slug', $slug)->first();
            $wasExisting = $post !== null;
            $post ??= new Post(['slug' => $slug]);

            $productSlug = (string) $article['product_slug'];
            $featured = collect($staged[$productSlug] ?? [])->firstWhere('featured', true);
            $post->slug = $slug;
            $post->author = (string) ($article['seo']['author'] ?? 'MTD ART');
            $post->featured_image = $featured['destination'] ?? $post->featured_image;
            $post->seo_translations = [
                'ro' => (array) $article['seo']['ro'],
                'en' => (array) $article['seo']['en'],
                'author' => (string) ($article['seo']['author'] ?? 'MTD ART'),
                'robots' => (string) ($article['seo']['robots'] ?? 'index, follow'),
            ];
            $post->setTranslation('title', 'ro', (string) $article['title']['ro']);
            $post->setTranslation('title', 'en', (string) $article['title']['en']);
            $post->setTranslation('content', 'ro', (string) $article['content_html']['ro']);
            $post->setTranslation('content', 'en', (string) $article['content_html']['en']);
            $post->setTranslation('seo_meta_description', 'ro', (string) $article['seo']['ro']['description']);
            $post->setTranslation('seo_meta_description', 'en', (string) $article['seo']['en']['description']);

            if (! $wasExisting) {
                $post->sort_order = (int) ($article['sort_order'] ?? $nextSort++);
                $post->published_at = $this->option('publish-posts') ? now() : null;
            } elseif ($this->option('publish-posts') && $post->published_at === null) {
                $post->published_at = now();
            }
            $post->save();
            $this->syncSeo($post, (array) $article['seo']);

            $bySlug[$slug] = $post;
            $wasExisting ? $updated++ : $created++;
        }

        return compact('created', 'updated', 'bySlug');
    }

    private function importProducts(array $products, array $staged, array $categoryMap, array $postBySlug): array
    {
        $created = 0;
        $updated = 0;
        $images = 0;
        $links = 0;

        foreach ($products as $data) {
            $slug = (string) $data['slug'];
            $product = Product::query()->where('slug', $slug)->first();
            $wasExisting = $product !== null;
            $product ??= new Product(['slug' => $slug]);

            $stock = $wasExisting && ! $this->option('reset-stock')
                ? (int) $product->stock
                : max(0, (int) ($data['stock'] ?? 1));
            $status = $wasExisting ? (string) $product->status : 'draft';
            if ($this->option('publish-products')) {
                $status = ((float) $data['price_ron'] > 0 && $stock > 0) ? 'published' : 'draft';
            }

            $relatedSlug = (string) ($data['related_article_slug'] ?? '');
            $relatedPost = $relatedSlug !== '' ? ($postBySlug[$relatedSlug] ?? null) : null;

            $product->category_id = $categoryMap[(string) $data['category_slug']]->getKey();
            $product->related_post_id = $relatedPost?->getKey();
            $product->slug = $slug;
            $product->price = round((float) $data['price_ron'], 2);
            $product->stock = $stock;
            $product->is_custom = (bool) ($data['is_custom'] ?? true);
            $product->status = $status;
            $product->seo_translations = [
                'ro' => (array) $data['seo']['ro'],
                'en' => (array) $data['seo']['en'],
                'author' => (string) ($data['seo']['author'] ?? 'MTD ART'),
                'robots' => (string) ($data['seo']['robots'] ?? 'index, follow'),
            ];
            $product->setTranslation('name', 'ro', (string) $data['name']['ro']);
            $product->setTranslation('name', 'en', (string) $data['name']['en']);
            $product->setTranslation('description', 'ro', (string) $data['description_html']['ro']);
            $product->setTranslation('description', 'en', (string) $data['description_html']['en']);
            $product->save();

            if ($relatedPost !== null) {
                $links++;
            }

            if ($this->option('replace-images')) {
                $product->images()->delete();
            } else {
                $product->images()->update(['is_featured' => false]);
            }

            $featuredPath = null;
            foreach ($staged[$slug] as $image) {
                $this->installPublicFile((string) $image['staged_path'], (string) $image['destination']);
                $product->images()->updateOrCreate(
                    ['image_path' => (string) $image['destination']],
                    [
                        'is_featured' => (bool) $image['featured'],
                        'sort_order' => (int) $image['sort_order'],
                        'alt_text' => (array) $image['alt_text'],
                    ]
                );
                if ($image['featured']) {
                    $featuredPath = (string) $image['destination'];
                }
                $images++;
            }

            if ($featuredPath !== null && Schema::hasColumn($product->getTable(), 'image')) {
                $product->forceFill(['image' => $featuredPath])->save();
            }
            $this->syncSeo($product, (array) $data['seo']);

            $wasExisting ? $updated++ : $created++;
            $this->line(($wasExisting ? 'actualizat' : 'creat') . ": {$slug}");
        }

        return compact('created', 'updated', 'images', 'links');
    }

    private function installPublicFile(string $source, string $destination): void
    {
        $disk = Storage::disk('public');
        if ($disk->exists($destination)) {
            if (! isset($this->overwrittenBackups[$destination])) {
                $backup = $this->stageRoot . '/rollback/' . sha1($destination) . '.bin';
                $directory = dirname($backup);
                if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
                    throw new RuntimeException('Nu pot crea backup temporar pentru imaginile existente.');
                }
                file_put_contents($backup, (string) $disk->get($destination));
                $this->overwrittenBackups[$destination] = $backup;
            }
        } else {
            $this->newPublicFiles[$destination] = true;
        }

        $binary = file_get_contents($source);
        if ($binary === false || ! $disk->put($destination, $binary)) {
            throw new RuntimeException("Nu pot instala imaginea în storage public: {$destination}");
        }
    }

    private function rollbackPublicFiles(): void
    {
        $disk = Storage::disk('public');
        foreach (array_keys($this->newPublicFiles) as $path) {
            $disk->delete($path);
        }
        foreach ($this->overwrittenBackups as $path => $backup) {
            if (is_file($backup)) {
                $disk->put($path, (string) file_get_contents($backup));
            }
        }
    }

    private function syncSeo(Model $model, array $seo): void
    {
        if (! method_exists($model, 'seo')) {
            return;
        }

        $romanian = (array) ($seo['ro'] ?? []);
        $model->seo()->updateOrCreate([], [
            'title' => Str::limit(trim((string) ($romanian['title'] ?? '')), 60, ''),
            'description' => Str::limit(trim(strip_tags((string) ($romanian['description'] ?? ''))), 160, ''),
            'author' => trim((string) ($seo['author'] ?? 'MTD ART')) ?: 'MTD ART',
            'robots' => strtolower(trim((string) ($seo['robots'] ?? 'index, follow'))),
        ]);
    }

    private function createBackup(array $products, array $articles): string
    {
        $productSlugs = array_values(array_filter(array_column($products, 'slug')));
        $articleSlugs = array_values(array_filter(array_column($articles, 'slug')));
        $payload = [
            'created_at' => now()->toIso8601String(),
            'products' => Product::query()->whereIn('slug', $productSlugs)->with(['images', 'seo'])->get()->toArray(),
            'posts' => Post::query()->whereIn('slug', $articleSlugs)->with('seo')->get()->toArray(),
        ];
        $path = 'import-backups/mtd-art-v2-' . now()->format('Ymd-His') . '.json';
        Storage::disk('local')->put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        return storage_path('app/' . $path);
    }

    private function printWarnings(): void
    {
        if ($this->warnings === []) {
            return;
        }
        $this->newLine();
        $this->warn('Avertismente:');
        foreach (array_unique($this->warnings) as $warning) {
            $this->line(' - ' . $warning);
        }
    }

    private function cleanupStage(): void
    {
        if ($this->stageRoot === null || ! is_dir($this->stageRoot)) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->stageRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }
        @rmdir($this->stageRoot);
    }
}
