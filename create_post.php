<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

$post = new Post();
$post->setTranslation('title', 'ro', 'Un articol de test pentru verificare imagini');
$post->slug = Str::slug('Un articol de test pentru verificare imagini');
$post->setTranslation('content', 'ro', '<p>Conținut de test.</p>');
$post->published_at = now();
$post->featured_image = 'https://picsum.photos/800/600';
$post->save();

$category = new Category();
$category->setTranslation('name', 'ro', 'Mese Din Lemn Masiv');
$category->slug = 'mese-din-lemn-masiv';
$category->save();

$product = new Product();
$product->setTranslation('name', 'ro', 'Masă de Cafea cu Rășină Epoxidică');
$product->slug = 'masa-de-cafea-cu-rasina-epoxidica';
$product->setTranslation('description', 'ro', 'O masă unicat, realizată din lemn de nuc și rășină epoxidică.');
$product->price = 1500;
$product->stock = 1;
$product->category_id = $category->id;
$product->image = 'https://picsum.photos/800/800';
$product->save();

echo "Post, Category, and Product created\n";
