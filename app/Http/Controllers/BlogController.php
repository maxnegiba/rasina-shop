<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Afișează pagina principală a blogului (Jurnal de Atelier)
     */
    public function index()
    {
        $posts = Post::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', compact('posts'));
    }

    /**
     * Afișează un singur articol de blog
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // Aducem ultimele 3 articole pentru secțiunea "Alte Articole"
        $recentPosts = Post::where('id', '!=', $post->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'recentPosts'));
    }
}
