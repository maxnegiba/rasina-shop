@extends('layouts.app')

@section('content')
<div class="bg-ivory pt-12 pb-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-sans tracking-widest text-smoked-black/40 uppercase mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-vintage-gold transition-colors">Acasă</a>
                </li>
                <li><span class="mx-1">/</span></li>
                <li>
                    <a href="{{ route('blog.index') }}" class="hover:text-vintage-gold transition-colors">Jurnal</a>
                </li>
                <li><span class="mx-1">/</span></li>
                <li aria-current="page" class="text-smoked-black truncate max-w-[150px] sm:max-w-[300px]">
                    {{ $post->title }}
                </li>
            </ol>
        </nav>

        <!-- Antet Articol -->
        <header class="text-center mb-12">
            <div class="inline-block px-3 py-1 bg-warm-beige text-[10px] font-sans tracking-[0.2em] text-vintage-gold uppercase mb-6 border border-vintage-gold/20">
                Jurnal de Atelier
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-serif text-smoked-black mb-6 leading-tight">
                {{ $post->title }}
            </h1>

            <div class="flex items-center justify-center space-x-4 text-xs font-sans tracking-widest text-smoked-black/60 uppercase">
                <time datetime="{{ $post->published_at->format('Y-m-d') }}">
                    {{ $post->published_at->translatedFormat('d F Y') }}
                </time>
                <span>&bull;</span>
                <span>Atelierul Ivory Vintage</span>
            </div>
        </header>

        <!-- Imagine Principală -->
        @if($post->image_path)
            <div class="w-full aspect-video sm:aspect-[21/9] bg-warm-beige mb-16 relative overflow-hidden rounded-sm group">
                <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                <div class="absolute inset-0 ring-1 ring-inset ring-black/10 rounded-sm"></div>
            </div>
        @else
            <!-- Placeholder elegant în caz că lipsește imaginea -->
            <div class="w-full aspect-video sm:aspect-[21/9] bg-warm-beige mb-16 relative overflow-hidden rounded-sm flex items-center justify-center border border-black/5">
                <span class="font-serif italic text-smoked-black/20 text-2xl">Ivory Vintage Art</span>
            </div>
        @endif

        <!-- Conținut Articol -->
        <article class="prose prose-stone lg:prose-lg max-w-none mx-auto prose-headings:font-serif prose-headings:font-normal prose-headings:text-smoked-black prose-p:font-light prose-p:text-smoked-black/80 prose-p:leading-relaxed prose-a:text-vintage-gold prose-a:no-underline hover:prose-a:underline prose-img:rounded-sm">
            {!! $post->content !!}
        </article>

        <!-- Distribuire & Taguri (Opțional) -->
        <div class="mt-16 pt-8 border-t border-black/10 flex flex-col sm:flex-row justify-between items-center gap-6">
            <div class="text-xs font-sans tracking-widest text-smoked-black/60 uppercase">
                <span>Distribuiți această poveste</span>
            </div>
            <div class="flex space-x-4">
                <button class="w-10 h-10 rounded-full border border-black/10 flex items-center justify-center text-smoked-black hover:border-vintage-gold hover:text-vintage-gold transition-colors">
                    F
                </button>
                <button class="w-10 h-10 rounded-full border border-black/10 flex items-center justify-center text-smoked-black hover:border-vintage-gold hover:text-vintage-gold transition-colors">
                    T
                </button>
                <button class="w-10 h-10 rounded-full border border-black/10 flex items-center justify-center text-smoked-black hover:border-vintage-gold hover:text-vintage-gold transition-colors">
                    P
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Secțiune Alte Articole -->
@if($recentPosts->count() > 0)
<div class="bg-warm-beige/30 py-20 border-t border-black/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-serif text-smoked-black">Din Același Jurnal</h2>
            <div class="w-12 h-px bg-vintage-gold mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentPosts as $recentPost)
                <a href="{{ route('blog.show', $recentPost->slug) }}" class="group block bg-white shadow-sm border border-black/5 hover:border-vintage-gold/30 transition-all duration-300 hover:-translate-y-1">
                    <div class="aspect-[4/3] bg-warm-beige relative overflow-hidden">
                        @if($recentPost->image_path)
                            <img src="{{ Storage::url($recentPost->image_path) }}" alt="{{ $recentPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-smoked-black/20 font-serif italic">Ivory Vintage</div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 text-[10px] font-sans tracking-widest text-vintage-gold uppercase">
                            {{ $recentPost->published_at->translatedFormat('d M') }}
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-serif text-xl text-smoked-black mb-3 line-clamp-2 group-hover:text-vintage-gold transition-colors">
                            {{ $recentPost->title }}
                        </h3>
                        <p class="text-sm font-light text-smoked-black/60 line-clamp-3 mb-4">
                            {{ strip_tags(substr($recentPost->content, 0, 150)) }}...
                        </p>
                        <span class="inline-flex items-center gap-2 text-[10px] font-sans tracking-[0.2em] text-smoked-black uppercase group-hover:text-vintage-gold transition-colors">
                            Citiți <span class="w-4 h-px bg-current transition-all group-hover:w-6"></span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
