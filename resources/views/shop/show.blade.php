@extends('layouts.app')

@section('seo_meta')
{!! seo($product) !!}
@endsection

@section('content')
<div class="bg-ivory min-h-screen pt-12 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs minimaliste premium -->
        <nav class="mb-12 text-[10px] font-sans font-medium uppercase tracking-[0.2em] text-dark-brown/50 flex items-center gap-3">
            <a href="{{ route('home') }}" class="hover:text-vintage-gold transition-colors">Acasă</a>
            <span class="w-1 h-1 rounded-full bg-vintage-gold/50"></span>
            <a href="{{ route('shop.index') }}" class="hover:text-vintage-gold transition-colors">Galerie</a>
            @if($product->category)
                <span class="w-1 h-1 rounded-full bg-vintage-gold/50"></span>
                <a href="{{ route('shop.category', $product->category->slug) }}" class="text-dark-brown hover:text-vintage-gold transition-colors">
                    {{ $product->category->name }}
                </a>
            @endif
        </nav>

        <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-start">
            
            <!-- Zona de Imagine (Slider pe fundal curat) -->
            <div class="w-full lg:w-3/5" x-data="{ activeSlide: 0 }">
                @php
                    $images = [];
                    if (isset($product->images) && $product->images->count() > 0) {
                        $featuredImage = $product->images->where('is_featured', true)->first();
                        if ($featuredImage) {
                            $images[] = asset('storage/' . $featuredImage->image_path);
                        }
                        foreach ($product->images as $image) {
                            if (!$featuredImage || $image->id !== $featuredImage->id) {
                                $images[] = asset('storage/' . $image->image_path);
                            }
                        }
                    }
                    
                    if (empty($images)) {
                        $images[] = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MDAiIGhlaWdodD0iODAwIiBmaWxsPSIjRkRGQkY3Ij48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjgwMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2VyaWYiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNDNUE4ODAiPkl2b3J5IFZpbnRhZ2U8L3RleHQ+PC9zdmc+';
                    }
                @endphp

                <!-- Container Principal Slider: Fundal alb, spatios, object-contain -->
                <div class="aspect-[4/5] overflow-hidden bg-white relative group ring-1 ring-inset ring-black/5 shadow-sm p-8 flex items-center justify-center">
                    <div class="relative w-full h-full">
                        @foreach($images as $index => $imageUrl)
                            <div x-show="activeSlide === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-300 absolute inset-0"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-105"
                                 class="w-full h-full flex items-center justify-center {{ $index === 0 ? '' : 'hidden' }}"
                                 style="{{ $index === 0 ? '' : 'display: none;' }}">
                                <!-- Imaginea incape perfect fara a fi taiata -->
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-contain drop-shadow-sm transition-all duration-700">
                            </div>
                        @endforeach
                    </div>
                         
                    @if($product->is_custom)
                        <div class="absolute top-6 left-6 z-10 bg-ivory/90 backdrop-blur-sm text-vintage-gold border border-vintage-gold/20 text-[10px] px-4 py-2 uppercase tracking-[0.2em] font-semibold shadow-sm pointer-events-none">
                            Lucrare Unicat / Comandă
                        </div>
                    @elseif($product->stock <= 0)
                        <div class="absolute top-6 left-6 z-10 bg-dark-brown/90 backdrop-blur-sm text-white border border-black/10 text-[10px] px-4 py-2 uppercase tracking-[0.2em] font-semibold shadow-sm pointer-events-none">
                            Stoc Epuizat
                        </div>
                    @endif

                    @if(count($images) > 1)
                        <!-- Navigation Arrows Premium -->
                        <button @click="activeSlide = activeSlide === 0 ? {{ count($images) - 1 }} : activeSlide - 1"
                                class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/80 hover:bg-white backdrop-blur-md border border-black/5 rounded-full flex items-center justify-center text-dark-brown shadow-sm opacity-0 group-hover:opacity-100 hover:text-vintage-gold transition-all duration-300 z-10 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button @click="activeSlide = activeSlide === {{ count($images) - 1 }} ? 0 : activeSlide + 1"
                                class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/80 hover:bg-white backdrop-blur-md border border-black/5 rounded-full flex items-center justify-center text-dark-brown shadow-sm opacity-0 group-hover:opacity-100 hover:text-vintage-gold transition-all duration-300 z-10 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    @endif
                </div>

                <!-- Galerie secundară (Thumbnails) -->
                @if(count($images) > 1)
                    <div class="grid grid-cols-4 gap-4 mt-4">
                        @foreach($images as $index => $imageUrl)
                            <!-- Thumbnails integrate pe fundal alb, object-contain -->
                            <div @click="activeSlide = {{ $index }}"
                                 class="aspect-square bg-white p-2 overflow-hidden cursor-pointer relative flex items-center justify-center transition-all duration-300"
                                 :class="{ 'ring-1 ring-inset ring-vintage-gold shadow-sm': activeSlide === {{ $index }}, 'ring-1 ring-inset ring-black/5 hover:border-black/10': activeSlide !== {{ $index }} }">
                                <img src="{{ $imageUrl }}"
                                     class="w-full h-full object-contain transition duration-500"
                                     :class="{ 'opacity-100': activeSlide === {{ $index }}, 'opacity-70 hover:opacity-100': activeSlide !== {{ $index }} }">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Zona de Detalii / Editorial -->
            <div class="w-full lg:w-2/5 py-4 lg:sticky lg:top-32">
                
                <div class="mb-10">
                    <h1 class="font-serif text-4xl lg:text-5xl text-dark-brown mb-6 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="text-xl font-sans text-vintage-gold font-medium tracking-[0.1em] uppercase">
                        @if($product->is_custom)
                            Preț la cerere
                        @else
                            {{ number_format($product->price, 0, ',', '.') }} <span class="text-sm font-light">RON</span>
                        @endif
                    </div>
                </div>

                <div class="w-12 h-px bg-vintage-gold/50 mb-10"></div>

                <!-- Tipografie aliniata cu editorialul jurnalului -->
                <div class="prose prose-stone max-w-none font-light leading-loose text-dark-brown/80 mb-12 prose-a:text-vintage-gold hover:prose-a:text-dark-brown prose-a:transition-colors">
                    {!! $product->description !!}
                </div>

                <div class="pt-10 border-t border-black/5">
                    @if(session('success'))
                        <div class="mb-8 p-4 bg-white shadow-sm border border-vintage-gold/30 text-dark-brown text-sm font-light flex items-center gap-3">
                            <svg class="w-5 h-5 text-vintage-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-8 p-4 bg-white shadow-sm border border-red-900/30 text-red-900 text-sm font-light flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($product->is_custom)
                        <p class="text-xs font-light text-dark-brown/60 mb-8 leading-relaxed italic">
                            * Această piesă este o lucrare unicat de referință. Putem realiza o operă similară, adaptată dimensiunilor și preferințelor dumneavoastră cromatice.
                        </p>
                        <button x-data @click="$dispatch('open-custom-modal', { productId: {{ $product->id }} })" class="group relative flex items-center justify-center w-full bg-dark-brown text-white px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors duration-500 overflow-hidden shadow-sm">
                            <span class="relative z-10">Solicită o propunere</span>
                        </button>
                    @else
                        @if($product->stock > 0)
                            <form action="{{ route('cart.add') }}" method="POST" class="space-y-4 add-to-cart-ajax-form mb-6">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="flex gap-4">
                                    <button type="submit" name="redirect_to_checkout" value="0" class="flex-1 bg-white border border-black/10 text-dark-brown px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-semibold hover:border-vintage-gold hover:text-vintage-gold transition-colors duration-300 shadow-sm">
                                        Adaugă în Colecție
                                    </button>
                                    <button type="submit" name="redirect_to_checkout" value="1" class="flex-1 bg-vintage-gold border border-vintage-gold text-white px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-semibold hover:bg-dark-brown hover:border-dark-brown transition-colors duration-300 shadow-sm">
                                        Cumpără Acum
                                    </button>
                                </div>
                            </form>
                            <p class="text-[9px] text-center text-dark-brown/50 mb-6 tracking-[0.2em] uppercase font-semibold flex items-center justify-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-600/80"></span>
                                Disponibil pentru livrare
                            </p>
                        @else
                            <button disabled class="w-full border border-black/5 text-dark-brown/40 bg-black/5 px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-semibold cursor-not-allowed mb-6 shadow-inner">
                                Stoc Epuizat
                            </button>
                        @endif
                        
                        <div class="w-full flex items-center justify-center mt-6">
                            <span class="w-full h-px bg-black/5"></span>
                            <span class="px-4 text-[10px] uppercase tracking-[0.2em] text-dark-brown/40 font-semibold">SAU</span>
                            <span class="w-full h-px bg-black/5"></span>
                        </div>
                        <button x-data @click="$dispatch('open-custom-modal', { productId: {{ $product->id }} })" class="w-full mt-6 bg-transparent border border-dark-brown text-dark-brown px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-semibold hover:bg-dark-brown hover:text-white transition-colors duration-500 shadow-sm">
                            Comandă Variantă Personalizată
                        </button>
                    @endif
                </div>
                
                <div class="mt-16 space-y-5 text-[10px] font-semibold tracking-[0.15em] text-dark-brown/60 uppercase border-t border-black/5 pt-10">
                    <div class="flex items-center gap-5">
                        <span class="w-8 h-px bg-vintage-gold/50"></span>
                        <span>Design și manufactură în România</span>
                    </div>
                    <div class="flex items-center gap-5">
                        <span class="w-8 h-px bg-vintage-gold/50"></span>
                        <span>Procesare securizată (Stripe)</span>
                    </div>
                    <div class="flex items-center gap-5">
                        <span class="w-8 h-px bg-vintage-gold/50"></span>
                        <span>Certificat de autenticitate inclus</span>
                    </div>
                </div>

            </div>
        </div>

        <x-flashy-custom-order />
    </div>
</div>
@endsection