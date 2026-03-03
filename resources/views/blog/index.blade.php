@extends('layouts.app')

@section('content')
    <section class="relative min-h-[60vh] flex flex-col justify-center overflow-hidden bg-smoked-black">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1455390582262-044cdead27d8?auto=format&fit=crop&q=80&w=2000"
                 class="w-full h-full object-cover opacity-30 mix-blend-luminosity scale-105 transform origin-center" alt="Jurnal de Atelier">
            <div class="absolute inset-0 bg-gradient-to-t from-smoked-black via-transparent to-smoked-black/60"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-20 text-center">
            <div class="max-w-3xl mx-auto">
                <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-6 drop-shadow-sm">Jurnal & Inspirație</span>
                <h1 class="font-serif text-5xl md:text-7xl text-white leading-[1.1] mb-8 drop-shadow-lg">
                    Povești din<br>
                    <span class="italic text-white/90">Atelier</span>
                </h1>
                <p class="text-white/70 text-lg font-light tracking-wide mb-0 max-w-xl mx-auto leading-relaxed">
                    Descoperă procesul creativ, tehnicile de prelucrare a lemnului cu rășină și sursele noastre de inspirație pentru fiecare colecție.
                </p>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto py-24 md:py-32 px-4 sm:px-6 lg:px-8">
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-20">
                @foreach($posts as $post)
                    <article class="group block">
                        <a href="{{ route('blog.show', $post->slug) }}" class="block relative overflow-hidden aspect-[4/5] mb-8 bg-warm-beige/30">
                            @php
                                // Logica antiglonț pentru imagini
                                $imageUrl = $post->image 
                                    ? asset('storage/' . $post->image) 
                                    : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MDAiIGhlaWdodD0iODAwIiBmaWxsPSIjRkRGQkY3Ij48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjgwMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2VyaWYiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNDNUE4ODAiPkl2b3J5IFZpbnRhZ2U8L3RleHQ+PC9zdmc+';
                            @endphp
                            
                            <img src="{{ $imageUrl }}" 
                                 class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-700 ease-out" 
                                 alt="{{ $post->title }}">
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-500"></div>
                        </a>

                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <span class="text-vintage-gold text-[10px] uppercase tracking-[0.2em] font-medium">
                                    {{ $post->category->name ?? 'Studio Design' }}
                                </span>
                                <span class="w-4 h-px bg-smoked-black/20"></span>
                                <span class="text-smoked-black/50 text-[10px] uppercase tracking-[0.1em]">
                                    {{ $post->created_at->format('d M, Y') }}
                                </span>
                            </div>

                            <h3 class="font-serif text-2xl text-smoked-black mb-4 leading-snug group-hover:text-vintage-gold transition-colors duration-300">
                                <a href="{{ route('blog.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            <p class="text-smoked-black/60 font-light text-sm md:text-base leading-relaxed mb-6 line-clamp-3">
                                {{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}
                            </p>

                            <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-3 text-smoked-black group-hover:text-vintage-gold transition-colors duration-300">
                                <span class="text-[10px] uppercase tracking-[0.2em] font-semibold">Citește Articolul</span>
                                <span class="w-8 h-px bg-smoked-black group-hover:bg-vintage-gold transition-colors duration-300"></span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-24 flex justify-center border-t border-smoked-black/10 pt-12">
                {{ $posts->links() }}
            </div>
            
        @else
            <div class="text-center py-32 border border-dashed border-smoked-black/20 bg-smoked-black/5">
                <h3 class="font-serif text-3xl mb-4 text-smoked-black/60 italic">Pagina este albă.</h3>
                <p class="font-light text-smoked-black/50 mb-8 max-w-md mx-auto text-sm leading-relaxed">
                    Momentan suntem în atelier și lucrăm la noi concepte. Întoarce-te curând pentru a descoperi poveștile din spatele creațiilor noastre.
                </p>
                <a href="{{ route('shop.index') }}" class="inline-block border border-vintage-gold text-vintage-gold px-8 py-3 uppercase tracking-[0.2em] text-xs hover:bg-vintage-gold hover:text-white transition duration-300">
                    Vizitează Galeria
                </a>
            </div>
        @endif
    </section>
@endsection