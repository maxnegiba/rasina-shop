@extends('layouts.app')

@section('content')
<div class="bg-ivory min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        
        <div class="flex flex-col md:flex-row gap-16 lg:gap-24 items-start">
            
            <!-- Sidebar -->
            <aside class="w-full md:w-1/4 md:sticky md:top-32 border-r border-black/5 pr-8">
                <h1 class="font-serif text-3xl md:text-4xl text-dark-brown mb-8 leading-tight">
                    {{ isset($category) ? $category->name : 'Galerie' }}
                </h1>
                <p class="text-dark-brown/60 font-light text-xs md:text-sm leading-relaxed mb-12">
                    {{ isset($category) && $category->description
                        ? $category->description
                        : 'Piese unicat, o colecție definită de contrastul dintre materie și claritate.' }}
                </p>

                <h3 class="font-sans text-[10px] uppercase tracking-[0.2em] text-vintage-gold mb-6 font-semibold">
                    Explorați
                </h3>
                <ul class="space-y-4 font-light text-xs tracking-[0.1em] uppercase">
                    <li>
                        <a href="{{ route('shop.index') }}" 
                           wire:navigate
                           class="{{ !isset($category) ? 'text-vintage-gold font-medium border-b border-vintage-gold pb-1' : 'text-dark-brown/70 hover:text-vintage-gold' }} transition-colors duration-300 flex items-center">
                            Toate Piesele
                        </a>
                    </li>
                    @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('shop.category', $cat->slug) }}" 
                               wire:navigate
                               class="flex justify-between items-center {{ isset($category) && $category->id === $cat->id ? 'text-vintage-gold font-medium border-b border-vintage-gold pb-1' : 'text-dark-brown/70 hover:text-vintage-gold' }} transition-colors duration-300">
                                <span>{{ $cat->name }}</span>
                                <span class="text-[9px] text-dark-brown/40 font-sans font-medium">
                                    {{ str_pad($cat->products_count, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </a>
                            @if(isset($category) && $category->id === $cat->id && $cat->sold_products_count > 0)
                                <a href="#produse-vandute"
                                   class="mt-3 ml-4 flex justify-between items-center text-[10px] tracking-[0.12em] text-dark-brown/50 hover:text-vintage-gold transition-colors">
                                    <span>Produse vândute</span>
                                    <span>{{ str_pad($cat->sold_products_count, 2, '0', STR_PAD_LEFT) }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </aside>

            <!-- Product Grid -->
            <div class="w-full md:w-3/4">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-16">
                        @foreach($products as $product)
                            <article class="group flex h-full flex-col">
                                <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                <!-- Imagine incadrata elegant pe fundal alb, fara trunchiere -->
                                <div class="aspect-[3/4] overflow-hidden bg-white relative ring-1 ring-inset ring-black/5 p-4 flex items-center justify-center group-hover:shadow-md transition-all duration-500 mb-6">
                                    
                                    @php
                                        $imageUrl = null;
                                        if (isset($product->images) && $product->images->count() > 0) {
                                            $firstImage = $product->images->where('is_featured', true)->first() ?? $product->images->first();
                                            $imageUrl = asset('storage/' . $firstImage->image_path);
                                        } elseif ($product->image) {
                                            $imageUrl = asset('storage/'.$product->image);
                                        } else {
                                            $imageUrl = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MDAiIGhlaWdodD0iODAwIiBmaWxsPSIjRkRGQkY3Ij48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjgwMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2VyaWYiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNDNUE4ODAiPkl2b3J5IFZpbnRhZ2U8L3RleHQ+PC9zdmc+';
                                        }
                                    @endphp
                                    
                                    <!-- object-contain repara problema taierii marginilor -->
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $product->name }}" 
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-contain group-hover:scale-[1.03] transition-transform duration-700 ease-out">

                                    <!-- Badges premium -->
                                    @if($product->isSold())
                                        <div class="absolute top-4 left-4 bg-dark-brown/90 backdrop-blur-sm text-white border border-black/10 text-[9px] px-3 py-1.5 uppercase tracking-[0.2em] font-semibold shadow-sm">
                                            Vândut
                                        </div>
                                    @elseif($product->is_custom)
                                        <div class="absolute top-4 left-4 bg-ivory/90 backdrop-blur-sm text-vintage-gold border border-vintage-gold/20 text-[9px] px-3 py-1.5 uppercase tracking-[0.2em] font-semibold shadow-sm">
                                            Piesă Unicat
                                        </div>
                                    @elseif($product->stock <= 0)
                                        <div class="absolute top-4 left-4 bg-dark-brown/90 backdrop-blur-sm text-white border border-black/10 text-[9px] px-3 py-1.5 uppercase tracking-[0.2em] font-semibold shadow-sm">
                                            Stoc Epuizat
                                        </div>
                                    @endif
                                </div>
                                </a>

                                <div class="text-left px-1">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                        <h2 class="font-serif text-lg text-dark-brown mb-2 leading-snug group-hover:text-vintage-gold transition-colors duration-300 truncate">
                                            {{ $product->name }}
                                        </h2>
                                    </a>
                                    <p class="text-dark-brown/60 font-sans text-xs tracking-[0.15em] uppercase font-medium">
                                        {{ $product->displayPrice() }}
                                    </p>
                                </div>

                                <div class="mt-auto pt-5 px-1">
                                    @if($product->isPurchasable())
                                        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-ajax-form grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="request_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                                            <button type="submit" name="redirect_to_checkout" value="0" class="min-h-11 border border-dark-brown px-3 py-3 text-[9px] uppercase tracking-[0.12em] font-semibold text-dark-brown hover:bg-dark-brown hover:text-white transition-colors disabled:opacity-60">
                                                Adaugă în coș
                                            </button>
                                            <button type="submit" name="redirect_to_checkout" value="1" class="min-h-11 bg-vintage-gold border border-vintage-gold px-3 py-3 text-[9px] uppercase tracking-[0.12em] font-semibold text-white hover:bg-dark-brown hover:border-dark-brown transition-colors disabled:opacity-60">
                                                Cumpără acum
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('shop.show', $product->slug) }}" class="min-h-11 w-full flex items-center justify-center border border-black/10 bg-black/[0.03] px-3 py-3 text-[9px] uppercase tracking-[0.12em] font-semibold text-dark-brown/50 hover:text-dark-brown transition-colors">
                                            {{ $product->isSold() ? 'Vezi piesa vândută' : 'Vezi detaliile' }}
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                    
                    <div class="mt-24 pt-12 border-t border-black/5 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-32 bg-white ring-1 ring-inset ring-black/5 shadow-sm">
                        <span class="block text-vintage-gold text-4xl mb-6 opacity-50">✧</span>
                        <h3 class="font-serif text-2xl mb-4 text-dark-brown">{{ isset($category) ? 'Momentan nu sunt piese disponibile' : 'Colecția este momentan privată' }}</h3>
                        <p class="font-light text-dark-brown/60 mb-10 text-sm max-w-md mx-auto leading-relaxed">{{ isset($category) ? 'Puteți explora mai jos piesele vândute și comanda o lucrare asemănătoare.' : 'Nu am găsit lucrări publicate în această secțiune. Vă invităm să explorați restul galeriei noastre.' }}</p>
                        <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-3 group text-[10px] uppercase tracking-[0.2em] text-dark-brown font-semibold hover:text-vintage-gold transition-colors duration-300">
                            <span>Înapoi la galerie</span>
                            <span class="w-8 h-px bg-dark-brown group-hover:bg-vintage-gold group-hover:w-12 transition-all duration-300"></span>
                        </a>
                    </div>
                @endif
            

                @if(isset($category) && isset($soldProducts) && $soldProducts->isNotEmpty())
                    <section id="produse-vandute" class="mt-24 pt-16 border-t border-black/10 scroll-mt-32">
                        <div class="mb-12">
                            <span class="block text-vintage-gold text-[10px] uppercase tracking-[0.25em] font-semibold mb-3">Arhiva colecției</span>
                            <h2 class="font-serif text-3xl text-dark-brown mb-4">Produse vândute</h2>
                            <p class="font-light text-dark-brown/60 text-sm leading-relaxed max-w-2xl">
                                Aceste piese unicat și-au găsit deja locul. Putem crea pentru dumneavoastră o lucrare asemănătoare, inspirată de modelul ales.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-16">
                            @foreach($soldProducts as $product)
                                <article class="group block">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                        <div class="aspect-[3/4] overflow-hidden bg-white relative ring-1 ring-inset ring-black/5 p-4 flex items-center justify-center group-hover:shadow-md transition-all duration-500 mb-6">
                                            @php
                                                $soldImage = $product->images->where('is_featured', true)->first() ?? $product->images->first();
                                                $soldImageUrl = $soldImage
                                                    ? asset('storage/' . $soldImage->image_path)
                                                    : ($product->image
                                                        ? asset('storage/'.$product->image)
                                                        : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MDAiIGhlaWdodD0iODAwIiBmaWxsPSIjRkRGQkY3Ij48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjgwMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2VyaWYiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNDNUE4ODAiPkl2b3J5IFZpbnRhZ2U8L3RleHQ+PC9zdmc+');
                                            @endphp
                                            <img src="{{ $soldImageUrl }}"
                                                 alt="{{ $product->name }}"
                                                 loading="lazy"
                                                 decoding="async"
                                                 class="w-full h-full object-contain opacity-80 grayscale-[20%] group-hover:scale-[1.03] group-hover:grayscale-0 transition-all duration-700 ease-out">
                                            <div class="absolute top-4 left-4 bg-dark-brown/90 backdrop-blur-sm text-white border border-black/10 text-[9px] px-3 py-1.5 uppercase tracking-[0.2em] font-semibold shadow-sm">
                                                Vândut
                                            </div>
                                        </div>

                                        <div class="text-left px-1 mb-5">
                                            <h3 class="font-serif text-lg text-dark-brown mb-2 leading-snug group-hover:text-vintage-gold transition-colors duration-300 truncate">
                                                {{ $product->name }}
                                            </h3>
                                            <p class="text-dark-brown/60 font-sans text-xs tracking-[0.15em] uppercase font-medium">
                                                {{ $product->displayPrice() }}
                                            </p>
                                        </div>
                                    </a>

                                    <button type="button" x-data
                                            @click="$dispatch('open-custom-modal', { productId: {{ $product->id }} })"
                                            class="w-full border border-dark-brown text-dark-brown px-5 py-4 uppercase tracking-[0.16em] text-[9px] font-semibold hover:bg-dark-brown hover:text-white transition-colors duration-300">
                                        Comandă o piesă asemănătoare
                                    </button>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

            </div>
        </div>

        <x-flashy-custom-order />
    </div>
</div>
@endsection
