@extends('layouts.app')

@section('seo_meta')
{!! seo($post) !!}
@endsection

@section('content')
<div class="bg-ivory pt-12 pb-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-sans tracking-widest text-dark-brown/50 uppercase mb-12" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-vintage-gold transition-colors">Acasă</a>
                </li>
                <li><span class="mx-1">/</span></li>
                <li>
                    <a href="{{ route('blog.index') }}" class="hover:text-vintage-gold transition-colors">Jurnal</a>
                </li>
                <li><span class="mx-1">/</span></li>
                <li aria-current="page" class="text-dark-brown truncate max-w-[150px] sm:max-w-[300px] font-medium">
                    {{ $post->title }}
                </li>
            </ol>
        </nav>

        <!-- Antet Articol -->
        <header class="text-center mb-16">
            <div class="inline-block px-4 py-1.5 bg-warm-beige text-[10px] font-sans tracking-[0.2em] font-medium text-vintage-gold uppercase mb-8 border border-vintage-gold/20 shadow-sm">
                Jurnal de Atelier
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-serif text-dark-brown mb-8 leading-tight">
                {{ $post->title }}
            </h1>

            <div class="flex items-center justify-center space-x-4 text-xs font-sans tracking-widest text-dark-brown/70 uppercase">
                <time datetime="{{ $post->published_at->format('Y-m-d') }}">
                    {{ $post->published_at->translatedFormat('d F Y') }}
                </time>
                <span class="text-vintage-gold">&bull;</span>
                <span class="font-medium">{{ $post->author ?? 'MTD ART' }}</span>
            </div>
        </header>

        <!-- Imagine Principală - Object Contain pentru a nu trunchia arta -->
        @if($post->featured_image)
            <div class="w-full h-auto max-h-[70vh] mx-auto flex items-center justify-center bg-warm-beige/20 p-4 sm:p-8 mb-20 relative overflow-hidden rounded-sm ring-1 ring-inset ring-black/5 shadow-sm group">
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full max-h-[65vh] object-contain group-hover:scale-[1.02] transition-transform duration-700 ease-out">
            </div>
        @else
            <!-- Placeholder elegant -->
            <div class="w-full h-auto max-h-[50vh] aspect-video bg-warm-beige/20 mb-20 relative overflow-hidden rounded-sm flex items-center justify-center border border-black/5">
                <img src="{{ asset('/img/logo.png') }}" alt="{{ $post->title }}" class="w-1/4 h-auto object-contain opacity-40">
            </div>
        @endif

        <!-- Conținut Articol (Tipografie Editorială) -->
        <article class="prose prose-stone lg:prose-lg mx-auto prose-headings:font-serif prose-headings:font-normal prose-headings:text-dark-brown prose-p:font-light prose-p:text-dark-brown/80 prose-p:leading-loose prose-a:text-vintage-gold prose-a:underline prose-a:underline-offset-4 hover:prose-a:text-dark-brown prose-img:rounded-sm prose-img:shadow-sm prose-img:border prose-img:border-black/5 prose-blockquote:border-l-vintage-gold prose-blockquote:bg-warm-beige/20 prose-blockquote:p-6 prose-blockquote:italic prose-blockquote:text-dark-brown/70">
            {!! $post->content !!}
        </article>

        <!-- Distribuire -->
        <div class="mt-20 pt-10 border-t border-black/5 flex flex-col sm:flex-row justify-between items-center gap-6">
            <div class="text-xs font-sans tracking-widest text-dark-brown/60 uppercase font-medium">
                <span>Distribuiți această poveste</span>
            </div>
            <div class="flex space-x-4">
                <button class="w-10 h-10 rounded-full border border-black/10 flex items-center justify-center text-dark-brown hover:border-vintage-gold hover:text-vintage-gold hover:shadow-md transition-all duration-300">
                    F
                </button>
                <button class="w-10 h-10 rounded-full border border-black/10 flex items-center justify-center text-dark-brown hover:border-vintage-gold hover:text-vintage-gold hover:shadow-md transition-all duration-300">
                    T
                </button>
                <button class="w-10 h-10 rounded-full border border-black/10 flex items-center justify-center text-dark-brown hover:border-vintage-gold hover:text-vintage-gold hover:shadow-md transition-all duration-300">
                    P
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Secțiune Alte Articole -->
@if(isset($recentPosts) && $recentPosts->count() > 0)
<div class="bg-warm-beige/20 py-24 border-t border-black/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-serif text-dark-brown">Din Același Jurnal</h2>
            <div class="w-12 h-px bg-vintage-gold mx-auto mt-6"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentPosts as $recentPost)
                <a href="{{ route('blog.show', $recentPost->slug) }}" class="group block bg-ivory shadow-sm border border-black/5 hover:border-vintage-gold/40 hover:shadow-md transition-all duration-300">
                    <div class="aspect-[4/3] bg-white relative overflow-hidden flex items-center justify-center p-4 border-b border-black/5">
                        @if($recentPost->featured_image)
                            <img src="{{ Storage::url($recentPost->featured_image) }}" alt="{{ $recentPost->title }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700 ease-out">
                        @else
                            <img src="{{ asset('/img/logo.png') }}" alt="{{ $recentPost->title }}" class="w-1/2 h-auto object-contain opacity-30">
                        @endif
                        <div class="absolute top-4 right-4 bg-ivory/90 backdrop-blur-sm px-3 py-1 text-[10px] font-sans tracking-widest text-vintage-gold uppercase shadow-sm">
                            {{ $recentPost->published_at->translatedFormat('d M') }}
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="font-serif text-xl text-dark-brown mb-4 line-clamp-2 group-hover:text-vintage-gold transition-colors leading-snug">
                            {{ $recentPost->title }}
                        </h3>
                        <p class="text-sm font-light text-dark-brown/70 line-clamp-3 mb-6 leading-relaxed">
                            {{ strip_tags(substr($recentPost->content, 0, 150)) }}...
                        </p>
                        <span class="inline-flex items-center gap-2 text-[10px] font-sans tracking-[0.2em] font-medium text-dark-brown uppercase group-hover:text-vintage-gold transition-colors">
                            Citiți <span class="w-4 h-px bg-current transition-all group-hover:w-8"></span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection