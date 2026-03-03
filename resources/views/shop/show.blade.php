@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12 md:py-24">
    
    <nav class="mb-8 text-sm font-light text-espresso/60 tracking-wide">
        <a href="{{ route('shop.index') }}" class="hover:text-brass transition">Galeria de Artă</a>
        <span class="mx-2">/</span>
        @if($product->category)
            <a href="{{ route('shop.category', $product->category->slug) }}" class="hover:text-brass transition">
                {{ $product->category->name }}
            </a>
            <span class="mx-2">/</span>
        @endif
        <span class="text-espresso font-medium">{{ $product->name }}</span>
    </nav>

    <div class="flex flex-col md:flex-row gap-12 lg:gap-20 items-start">
        
        <div class="w-full md:w-1/2">
            <div class="aspect-[4/5] bg-white overflow-hidden relative border border-brass/10 shadow-lg">
                @php
                    // Aceeași logică antiglonț pentru imagine ca pe pagina principală
                    $imageUrl = null;
                    if (!empty($product->image)) {
                        $imageUrl = asset('storage/' . $product->image);
                    } elseif (isset($product->images) && $product->images->count() > 0) {
                        $firstImage = $product->images->where('is_featured', true)->first() ?? $product->images->first();
                        $imageUrl = asset('storage/' . $firstImage->image_path);
                    } else {
                        $imageUrl = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MDAiIGhlaWdodD0iODAwIiBmaWxsPSIjRkRGQkY3Ij48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjgwMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2VyaWYiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNDNUE4ODAiPkl2b3J5IFZpbnRhZ2U8L3RleHQ+PC9zdmc+';
                    }
                @endphp
                
                <img src="{{ $imageUrl }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover">
                     
                @if($product->is_custom)
                    <div class="absolute top-6 left-6 bg-espresso/90 backdrop-blur-sm text-ivory text-xs px-4 py-2 uppercase tracking-widest">
                        Piesă Unicat / La Comandă
                    </div>
                @endif
            </div>
        </div>

        <div class="w-full md:w-1/2 py-4 md:py-8 md:sticky md:top-32">
            
            <h1 class="font-serif text-3xl md:text-5xl text-espresso mb-4 leading-tight">
                {{ $product->name }}
            </h1>
            
            <div class="text-2xl text-brass font-light tracking-wider mb-8">
                @if($product->is_custom)
                    Preț la cerere
                @else
                    {{ number_format($product->price, 2, ',', '.') }} RON
                @endif
            </div>

            <div class="prose prose-espresso prose-a:text-brass font-light leading-relaxed text-espresso/80 mb-10">
                {!! $product->description !!}
            </div>

            <div class="border-t border-brass/20 pt-8 mt-8">
                @if($product->is_custom)
                    <p class="text-sm font-light text-espresso/70 mb-6 italic">
                        Această piesă este unicat sau se realizează pe comandă. Contactați-ne pentru a discuta dimensiunile, nuanțele de rășină dorite și timpul de execuție.
                    </p>
                    <a href="#contact" class="block w-full text-center bg-espresso text-ivory px-8 py-4 uppercase tracking-widest text-sm hover:bg-brass transition duration-300">
                        Solicită Detalii / Ofertă
                    </a>
                @else
                    @if($product->stock > 0)
                        <form action="#" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="w-full bg-brass text-ivory px-8 py-4 uppercase tracking-widest text-sm hover:bg-espresso transition duration-300 shadow-md">
                                Adaugă în Coș
                            </button>
                        </form>
                        <p class="text-xs text-center text-espresso/50 mt-4 tracking-wide uppercase">
                            ✓ În Stoc ({{ $product->stock }} buc.)
                        </p>
                    @else
                        <button disabled class="w-full border border-espresso/20 text-espresso/40 px-8 py-4 uppercase tracking-widest text-sm cursor-not-allowed">
                            Stoc Epuizat
                        </button>
                    @endif
                @endif
            </div>
            
            <div class="mt-12 space-y-4 text-xs font-light tracking-wide text-espresso/60 uppercase">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-brass" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Lucrat manual în România</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-brass" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Plată securizată via Stripe</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection