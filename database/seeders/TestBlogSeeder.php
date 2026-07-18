<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;

class TestBlogSeeder extends Seeder
{
    public function run(): void
    {
        Post::create([
            'slug' => 'test-post',
            'title' => 'Test Blog Post',
            'content' => '<p>This is a test blog post.</p>',
            'author' => 'Test Author',
            'published_at' => now(),
            // No featured_image to test fallback
        ]);

        Post::create([
            'slug' => 'test-post-with-image',
            'title' => 'Test Blog Post with Image',
            'content' => '<p>This is a test blog post with an image.</p>',
            'author' => 'Another Author',
            'published_at' => now(),
            'featured_image' => 'blog/test.jpg'
        ]);
    }
}
