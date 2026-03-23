@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-24">
    
    <!-- Breadcrumbs minimaliste -->
    <nav class="mb-12 text-[10px] font-medium uppercase tracking-[0.2em] text-dark-brown/40 flex items-center gap-3">
        <a href="{{ route('home') }}" class="hover:text-dark-brown transition-colors">Acasă</a>
        <span class="w-1 h-1 rounded-full bg-black/10"></span>
        <a href="{{ route('shop.index') }}" class="hover:text-dark-brown transition-colors">Galerie</a>
        @if($product->category)
            <span class="w-1 h-1 rounded-full bg-black/10"></span>
            <a href="{{ route('shop.category', $product->category->slug) }}" class="hover:text-dark-brown transition-colors">
                {{ $product->category->name }}
            </a>
        @endif
    </nav>

    <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-start">
        
        <!-- Zona de Imagine -->
        <div class="w-full lg:w-3/5">
            <div class="aspect-[4/5] overflow-hidden bg-warm-beige/20 relative group">
                @php
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
                     class="w-full h-full object-cover filter contrast-[0.95] group-hover:contrast-100 transition-all duration-700">
                     
                @if($product->is_custom)
                    <div class="absolute top-6 left-6 bg-ivory/90 backdrop-blur-sm text-dark-brown text-[10px] px-4 py-2 uppercase tracking-[0.2em] font-medium shadow-sm">
                        Lucrare Unicat / Comandă
                    </div>
                @endif
            </div>

            <!-- Galerie secundară (dacă există alte imagini) -->
            @if(isset($product->images) && $product->images->count() > 1)
                <div class="grid grid-cols-3 gap-4 mt-4">
                    @foreach($product->images->where('is_featured', false)->take(3) as $image)
                        <div class="aspect-square bg-warm-beige/20 overflow-hidden cursor-pointer">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover filter grayscale hover:grayscale-0 transition duration-500">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Zona de Detalii -->
        <div class="w-full lg:w-2/5 py-4 lg:sticky lg:top-32">
            
            <div class="mb-8">
                <h1 class="font-serif text-4xl lg:text-5xl text-dark-brown mb-6 leading-tight">
                    {{ $product->name }}
                </h1>

                <div class="text-xl font-sans text-dark-brown/80 tracking-[0.1em] uppercase">
                    @if($product->is_custom)
                        Preț la cerere
                    @else
                        {{ number_format($product->price, 0, ',', '.') }} <span class="text-sm font-medium">RON</span>
                    @endif
                </div>
            </div>

            <div class="w-12 h-px bg-vintage-gold mb-8"></div>

            <div class="prose prose-sm prose-dark-brown prose-a:text-vintage-gold font-light leading-relaxed text-dark-brown/70 mb-12">
                {!! $product->description !!}
            </div>

            <div class="pt-8 border-t border-black/5">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50/50 text-green-800 text-sm border-l border-green-500/30 font-light">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50/50 text-red-800 text-sm border-l border-red-500/30 font-light">
                        {{ session('error') }}
                    </div>
                @endif

                @if($product->is_custom)
                    <p class="text-xs font-light text-dark-brown/60 mb-6 leading-relaxed">
                        * Această piesă este o lucrare unicat de referință. Putem realiza o operă similară, adaptată dimensiunilor și preferințelor dumneavoastră cromatice.
                    </p>
                    <a href="{{ route('contact') }}#cerere-personalizata" class="group relative flex items-center justify-center w-full bg-dark-brown text-white px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 overflow-hidden">
                        <span class="relative z-10">Solicită o propunere</span>
                    </a>
                @else
                    @if($product->stock > 0)
                        <form action="{{ route('cart.add') }}" method="POST" class="space-y-4 add-to-cart-ajax-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="flex gap-4">
                                <button type="submit" name="redirect_to_checkout" value="0" class="flex-1 bg-ivory border border-dark-brown/20 text-dark-brown px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:border-vintage-gold hover:text-vintage-gold transition-colors duration-500 shadow-sm">
                                    Adaugă în Colecție
                                </button>
                                <button type="submit" name="redirect_to_checkout" value="1" class="flex-1 bg-vintage-gold text-white px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-dark-brown transition-colors duration-500 shadow-sm">
                                    Cumpără Acum
                                </button>
                            </div>
                        </form>
                        <p class="text-[10px] text-center text-dark-brown/40 mt-4 tracking-[0.2em] uppercase font-medium">
                            Disponibil pentru livrare
                        </p>
                    @else
                        <button disabled class="w-full border border-dark-brown/20 text-dark-brown/30 bg-black/5 px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-medium cursor-not-allowed">
                            Lucrare Achiziționată
                        </button>
                    @endif
                @endif
            </div>
            
            <div class="mt-16 space-y-5 text-[10px] font-medium tracking-[0.15em] text-dark-brown/50 uppercase border-t border-black/5 pt-8">
                <div class="flex items-center gap-4">
                    <span class="w-6 h-px bg-vintage-gold"></span>
                    <span>Design și manufactură în România</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-6 h-px bg-vintage-gold"></span>
                    <span>Procesare securizată (Stripe)</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-6 h-px bg-vintage-gold"></span>
                    <span>Certificat de autenticitate inclus</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection