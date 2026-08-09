@extends('layouts.app')

@section('content')
    <x-atelier-hero />

    @once
        <style>
            /*
             * Mobile/tablet override for the atelier puzzle hero.
             * The component's desktop collage remains untouched; below 1024px
             * we use intrinsic aspect-ratio tiles instead of fixed row spans.
             */
            @media (max-width: 1023px) {
                #atelier {
                    min-height: 0;
                }

                #atelier .atelier-puzzle-stage {
                    position: relative;
                    inset: auto;
                    display: grid;
                    grid-auto-flow: dense;
                    grid-auto-rows: auto;
                    width: 100%;
                    min-height: 0;
                    overflow: hidden;
                }

                #atelier .atelier-puzzle-piece {
                    position: relative;
                    left: auto;
                    top: auto;
                    width: 100%;
                    height: auto;
                    min-width: 0;
                    grid-row: auto !important;
                    aspect-ratio: 1 / 1;
                    transform: none;
                    will-change: auto;
                }

                #atelier .atelier-puzzle-piece svg {
                    position: absolute;
                    inset: 0;
                    width: 100%;
                    height: 100%;
                }

                #atelier .atelier-puzzle-piece:hover,
                #atelier .atelier-puzzle-piece:focus-visible {
                    transform: none;
                }
            }

            /* Phones: compact three-column mosaic instead of a very tall masonry wall. */
            @media (max-width: 639px) {
                #atelier .atelier-puzzle-stage {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                    gap: 4px;
                    padding: 6px;
                }

                #atelier .atelier-puzzle-piece {
                    grid-column: span 1 !important;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(1),
                #atelier .atelier-puzzle-piece:nth-of-type(7),
                #atelier .atelier-puzzle-piece:nth-of-type(11) {
                    grid-column: span 2 !important;
                    aspect-ratio: 2 / 1;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(2),
                #atelier .atelier-puzzle-piece:nth-of-type(4),
                #atelier .atelier-puzzle-piece:nth-of-type(12) {
                    aspect-ratio: 4 / 5;
                }
            }

            /* Very narrow phones get two larger columns for better image legibility. */
            @media (max-width: 374px) {
                #atelier .atelier-puzzle-stage {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 5px;
                    padding: 6px;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(1),
                #atelier .atelier-puzzle-piece:nth-of-type(7),
                #atelier .atelier-puzzle-piece:nth-of-type(11) {
                    grid-column: 1 / -1 !important;
                    aspect-ratio: 2 / 1;
                }
            }

            /* Tablets: four-column collage keeps the hero compact and balanced. */
            @media (min-width: 640px) and (max-width: 1023px) {
                #atelier .atelier-puzzle-stage {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 7px;
                    padding: 10px;
                }

                #atelier .atelier-puzzle-piece {
                    grid-column: span 1 !important;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(1),
                #atelier .atelier-puzzle-piece:nth-of-type(7),
                #atelier .atelier-puzzle-piece:nth-of-type(11),
                #atelier .atelier-puzzle-piece:nth-of-type(13) {
                    grid-column: span 2 !important;
                    aspect-ratio: 2 / 1;
                }
            }
        </style>
    @endonce

    <!-- COLECȚII PRINCIPALE -->
    <section class="max-w-7xl mx-auto py-32 px-4 sm:px-6 lg:px-8 bg-ivory">
        <div class="text-center mb-24">
            <h2 class="font-serif text-4xl md:text-5xl text-dark-brown mb-6">Esență & Măiestrie</h2>
            <div class="w-12 h-px bg-vintage-gold mx-auto mb-8"></div>
            <p class="text-dark-brown/70 font-light max-w-2xl mx-auto tracking-wide text-sm md:text-base leading-relaxed">
                Fiecare colecție reprezintă un studiu al materialului. Lemnul capătă noi valențe prin incluziunea rășinii epoxidice, într-o simbioză perfectă de texturi și transparențe.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-12 gap-y-20">
            @if(isset($featuredCategories))
                @foreach($featuredCategories as $index => $category)
                    <div class="group block {{ $index === 1 ? 'md:mt-16' : '' }}">
                        <a href="{{ route('shop.category', $category->slug ?? '#') }}" class="block relative overflow-hidden aspect-[4/5] mb-8 bg-warm-beige/30 ring-1 ring-inset ring-black/5">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('img/logo.png') }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full {{ $category->image ? 'object-cover' : 'object-contain p-12 opacity-40' }} group-hover:scale-105 transition-transform duration-700 ease-out" alt="{{ $category->name }}">
                        </a>
                        <div class="flex items-center justify-between">
                            <h3 class="font-serif text-2xl text-dark-brown">{{ $category->name }}</h3>
                            <span class="text-vintage-gold text-sm opacity-0 group-hover:opacity-100 transition duration-300 transform -translate-x-4 group-hover:translate-x-0">
                                &rarr;
                            </span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    <!-- PIESE RECENTE (Refăcut pe tematica Ivory) -->
    <section class="bg-warm-beige/30 py-32 border-t border-black/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-8">
                <div class="max-w-2xl">
                    <span class="text-vintage-gold uppercase tracking-[0.2em] text-xs font-semibold block mb-4">Selecție Curată</span>
                    <h2 class="font-serif text-4xl md:text-5xl text-dark-brown">Opere Recente</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="group inline-flex items-center gap-4 text-dark-brown hover:text-vintage-gold transition-colors duration-300">
                    <span class="text-xs uppercase tracking-[0.2em] font-medium">Vezi tot portofoliul</span>
                    <span class="w-12 h-px bg-dark-brown group-hover:bg-vintage-gold transition-colors duration-300"></span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @if(isset($latestProducts))
                    @foreach($latestProducts as $product)
                        @php
                            $homeImage = $product->images->firstWhere('is_featured', true)
                                ?? $product->images->first();
                            $homeImageUrl = $homeImage
                                ? asset('storage/'.$homeImage->image_path)
                                : ($product->image ? asset('storage/'.$product->image) : asset('img/logo.png'));
                        @endphp
                        <div class="group bg-ivory shadow-sm border border-black/5 hover:shadow-md transition-all duration-300">
                            <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                <div class="aspect-[3/4] overflow-hidden bg-white relative p-4 flex items-center justify-center">
                                    <img src="{{ $homeImageUrl }}"
                                         alt="{{ $product->name }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700 ease-out">
                                    
                                    @if($product->is_custom)
                                        <span class="absolute top-4 left-4 bg-vintage-gold text-white text-[9px] px-3 py-1.5 uppercase tracking-[0.2em] font-medium shadow-sm">
                                            Unicat / Comandă
                                        </span>
                                    @endif
                                </div>
                                <div class="p-4 border-t border-black/5">
                                    <h4 class="font-serif text-lg text-dark-brown mb-2 truncate">{{ $product->name }}</h4>
                                    <p class="text-vintage-gold font-sans text-xs tracking-[0.15em] uppercase font-medium">
                                        {{ $product->displayPrice() }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endsection
