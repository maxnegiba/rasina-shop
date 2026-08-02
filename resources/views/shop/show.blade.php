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
            <div class="w-full lg:w-3/5"
                 data-product-gallery
                 data-active-index="0"
                 tabindex="0"
                 role="region"
                 aria-roledescription="carusel"
                 aria-label="Galeria produsului {{ $product->name }}">
                @php
                    $images = [];
                    $imageAlts = []; // MTD_ART_FINAL_ALT_TEXT
                    if (isset($product->images) && $product->images->count() > 0) {
                        $featuredImage = $product->images->where('is_featured', true)->first();
                        if ($featuredImage) {
                            $images[] = asset('storage/' . $featuredImage->image_path);
                            $imageAlts[] = $featuredImage->translatedAltText() ?: $product->name;
                        }
                        foreach ($product->images as $image) {
                            if (!$featuredImage || $image->id !== $featuredImage->id) {
                                $images[] = asset('storage/' . $image->image_path);
                                $imageAlts[] = $image->translatedAltText() ?: $product->name;
                            }
                        }
                    }

                    if (empty($images)) {
                        $images[] = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MDAiIGhlaWdodD0iODAwIiBmaWxsPSIjRkRGQkY3Ij48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjgwMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2VyaWYiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNDNUE4ODAiPkl2b3J5IFZpbnRhZ2U8L3RleHQ+PC9zdmc+';
                        $imageAlts[] = $product->name;
                    }
                @endphp

                <!-- Container Principal Slider: Fundal alb, spatios, object-contain -->
                <div class="aspect-[4/5] overflow-hidden bg-white relative group ring-1 ring-inset ring-black/5 shadow-sm p-8 flex items-center justify-center touch-pan-y">
                    <div class="relative w-full h-full">
                        @foreach($images as $index => $imageUrl)
                            <div data-gallery-slide
                                 data-gallery-index="{{ $index }}"
                                 aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
                                 class="absolute inset-0 w-full h-full flex items-center justify-center transition-opacity duration-300 {{ $index === 0 ? 'opacity-100' : 'hidden opacity-0' }}">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $imageAlts[$index] ?? $product->name }}"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                     fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                                     class="w-full h-full object-contain drop-shadow-sm transition-transform duration-700">
                            </div>
                        @endforeach
                    </div>

                    @if($product->isSold())
                        <div class="absolute top-6 left-6 z-10 bg-dark-brown/90 backdrop-blur-sm text-white border border-black/10 text-[10px] px-4 py-2 uppercase tracking-[0.2em] font-semibold shadow-sm pointer-events-none">
                            Vândut
                        </div>
                    @elseif($product->is_custom)
                        <div class="absolute top-6 left-6 z-10 bg-ivory/90 backdrop-blur-sm text-vintage-gold border border-vintage-gold/20 text-[10px] px-4 py-2 uppercase tracking-[0.2em] font-semibold shadow-sm pointer-events-none">
                            Piesă Unicat
                        </div>
                    @elseif($product->stock <= 0)
                        <div class="absolute top-6 left-6 z-10 bg-dark-brown/90 backdrop-blur-sm text-white border border-black/10 text-[10px] px-4 py-2 uppercase tracking-[0.2em] font-semibold shadow-sm pointer-events-none">
                            Stoc Epuizat
                        </div>
                    @endif

                    @if(count($images) > 1)
                        <button type="button"
                                data-gallery-prev
                                aria-label="Imaginea precedentă"
                                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-12 sm:h-12 bg-white/85 hover:bg-white backdrop-blur-md border border-black/5 rounded-full flex items-center justify-center text-dark-brown shadow-sm opacity-100 md:opacity-0 md:group-hover:opacity-100 hover:text-vintage-gold transition-all duration-300 z-20 focus:outline-none focus-visible:ring-2 focus-visible:ring-vintage-gold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button type="button"
                                data-gallery-next
                                aria-label="Imaginea următoare"
                                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-12 sm:h-12 bg-white/85 hover:bg-white backdrop-blur-md border border-black/5 rounded-full flex items-center justify-center text-dark-brown shadow-sm opacity-100 md:opacity-0 md:group-hover:opacity-100 hover:text-vintage-gold transition-all duration-300 z-20 focus:outline-none focus-visible:ring-2 focus-visible:ring-vintage-gold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <div class="absolute bottom-4 right-4 z-20 bg-dark-brown/75 text-white px-3 py-1.5 text-[9px] font-sans tracking-[0.15em] rounded-full backdrop-blur-sm"
                             data-gallery-position
                             aria-live="polite">
                            1 / {{ count($images) }}
                        </div>
                    @endif
                </div>

                <!-- Galerie secundară (Thumbnails) -->
                @if(count($images) > 1)
                    <div class="grid grid-cols-4 gap-4 mt-4" role="tablist" aria-label="Selectați imaginea produsului">
                        @foreach($images as $index => $imageUrl)
                            <button type="button"
                                    data-gallery-thumb
                                    data-gallery-index="{{ $index }}"
                                    role="tab"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-label="Afișează imaginea {{ $index + 1 }} din {{ count($images) }}"
                                    tabindex="{{ $index === 0 ? '0' : '-1' }}"
                                    class="aspect-square bg-white p-2 overflow-hidden cursor-pointer relative flex items-center justify-center transition-all duration-300 ring-1 ring-inset {{ $index === 0 ? 'ring-vintage-gold shadow-sm' : 'ring-black/5 hover:ring-vintage-gold/50' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-vintage-gold">
                                <img src="{{ $imageUrl }}"
                                     alt=""
                                     loading="lazy"
                                     class="w-full h-full object-contain transition duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-70 hover:opacity-100' }}">
                            </button>
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
                        {{ $product->displayPrice() }}
                    </div>
                </div>

                <div class="w-12 h-px bg-vintage-gold/50 mb-10"></div>

                @if($product->relatedPost)
                    <a href="{{ route('blog.show', $product->relatedPost->slug) }}"
                       class="mb-8 inline-flex items-center gap-2 text-[10px] uppercase tracking-[0.18em] font-semibold text-vintage-gold hover:text-dark-brown transition-colors">
                        Citește povestea acestei piese
                        <span aria-hidden="true">→</span>
                    </a>
                @endif

                <!-- MTD_ART_FINAL_RELATED_ARTICLE -->
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

                    @if($product->isSold())
                        <p class="text-xs font-light text-dark-brown/60 mb-8 leading-relaxed italic">
                            * Această piesă a fost vândută. Putem crea o lucrare asemănătoare, adaptată preferințelor dumneavoastră.
                        </p>
                    @elseif($product->is_custom)
                        <p class="text-xs font-light text-dark-brown/60 mb-8 leading-relaxed italic">
                            * Aceasta este o piesă unicat. Exemplarul disponibil poate fi cumpărat direct prin fluxul normal al magazinului.
                        </p>
                    @endif

                    @if($product->isPurchasable())
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
                            {{ $product->isSold() ? 'Vândut' : 'Indisponibil momentan' }}
                        </button>
                    @endif

                    <div class="w-full flex items-center justify-center mt-6">
                        <span class="w-full h-px bg-black/5"></span>
                        <span class="px-4 text-[10px] uppercase tracking-[0.2em] text-dark-brown/40 font-semibold">{{ $product->isSold() ? 'DORIȚI UNA ASEMĂNĂTOARE?' : 'SAU' }}</span>
                        <span class="w-full h-px bg-black/5"></span>
                    </div>
                    <button x-data @click="$dispatch('open-custom-modal', { productId: {{ $product->id }} })" class="w-full mt-6 bg-transparent border border-dark-brown text-dark-brown px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-semibold hover:bg-dark-brown hover:text-white transition-colors duration-500 shadow-sm">
                        {{ $product->isSold() ? 'Comandă o piesă asemănătoare' : 'Comandă Variantă Personalizată' }}
                    </button>
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
