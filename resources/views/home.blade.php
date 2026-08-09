@extends('layouts.app')

@section('content')
    <x-atelier-hero />

    @once
        <style>
            /*
             * Mobile/tablet override for the atelier puzzle hero.
             * Desktop keeps the original absolute-positioned collage.
             * Below 1024px we use a dense intrinsic grid so the artwork remains
             * visible without forcing the user through several screens of images.
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
                    aspect-ratio: 1.15 / 1;
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

            @media (max-width: 639px) {
                #atelier .atelier-puzzle-stage {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 2px;
                    padding: 4px;
                }

                #atelier .atelier-puzzle-piece {
                    grid-column: span 1 !important;
                    aspect-ratio: 1.12 / 1;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(1),
                #atelier .atelier-puzzle-piece:nth-of-type(7),
                #atelier .atelier-puzzle-piece:nth-of-type(11) {
                    grid-column: span 2 !important;
                    aspect-ratio: 2.35 / 1;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(2),
                #atelier .atelier-puzzle-piece:nth-of-type(4),
                #atelier .atelier-puzzle-piece:nth-of-type(12) {
                    aspect-ratio: 1 / 1.08;
                }

                #atelier .atelier-puzzle-piece__resin-rim {
                    stroke-width: 3.2;
                }

                #atelier .atelier-puzzle-piece__edge {
                    stroke-width: 1.65;
                }
            }

            @media (max-width: 374px) {
                #atelier .atelier-puzzle-stage {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 1px;
                    padding: 3px;
                }

                #atelier .atelier-puzzle-piece {
                    aspect-ratio: 1.08 / 1;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(1),
                #atelier .atelier-puzzle-piece:nth-of-type(7),
                #atelier .atelier-puzzle-piece:nth-of-type(11) {
                    grid-column: span 2 !important;
                    aspect-ratio: 2.25 / 1;
                }
            }

            @media (min-width: 640px) and (max-width: 1023px) {
                #atelier .atelier-puzzle-stage {
                    grid-template-columns: repeat(6, minmax(0, 1fr));
                    gap: 4px;
                    padding: 7px;
                }

                #atelier .atelier-puzzle-piece {
                    grid-column: span 1 !important;
                    aspect-ratio: 1.15 / 1;
                }

                #atelier .atelier-puzzle-piece:nth-of-type(1),
                #atelier .atelier-puzzle-piece:nth-of-type(7),
                #atelier .atelier-puzzle-piece:nth-of-type(11),
                #atelier .atelier-puzzle-piece:nth-of-type(13) {
                    grid-column: span 2 !important;
                    aspect-ratio: 2.35 / 1;
                }
            }
        </style>
    @endonce

    <!-- COLECȚII PRINCIPALE -->
    <section class="max-w-7xl mx-auto py-28 md:py-32 px-4 sm:px-6 lg:px-8 bg-ivory" style="content-visibility:auto; contain-intrinsic-size: 900px;">
        <div class="text-center mb-20 md:mb-24">
            <h2 class="font-serif text-4xl md:text-5xl text-dark-brown mb-6">Esență & Măiestrie</h2>
            <div class="w-12 h-px bg-vintage-gold mx-auto mb-8" aria-hidden="true"></div>
            <p class="text-dark-brown/70 font-light max-w-2xl mx-auto tracking-wide text-base md:text-lg leading-relaxed">
                Fiecare colecție reprezintă un studiu al materialului. Lemnul capătă noi valențe prin incluziunea rășinii epoxidice, într-o simbioză perfectă de texturi și transparențe.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-12 gap-y-20">
            @if(isset($featuredCategories))
                @foreach($featuredCategories as $index => $category)
                    <article class="group block {{ $index === 1 ? 'md:mt-16' : '' }}">
                        <a href="{{ route('shop.category', $category->slug ?? '#') }}" class="block relative overflow-hidden aspect-[4/5] mb-8 bg-warm-beige/30 ring-1 ring-inset ring-black/5">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     width="800"
                                     height="1000"
                                     loading="lazy"
                                     fetchpriority="low"
                                     decoding="async"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                                     alt="{{ $category->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-warm-beige/40 text-dark-brown/60 font-serif text-3xl" aria-hidden="true">MTD ART</div>
                            @endif
                        </a>
                        <div class="flex items-center justify-between">
                            <h3 class="font-serif text-2xl md:text-3xl text-dark-brown">{{ $category->name }}</h3>
                            <span class="text-vintage-gold text-xl opacity-0 group-hover:opacity-100 transition duration-300 transform -translate-x-4 group-hover:translate-x-0" aria-hidden="true">
                                &rarr;
                            </span>
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </section>

    <!-- PIESE RECENTE -->
    <section class="bg-warm-beige/30 py-28 md:py-32 border-t border-black/5" style="content-visibility:auto; contain-intrinsic-size: 850px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 md:mb-20 gap-8">
                <div class="max-w-2xl">
                    <span class="text-vintage-gold uppercase tracking-[0.16em] text-sm font-semibold block mb-4">Selecție Curată</span>
                    <h2 class="font-serif text-4xl md:text-5xl text-dark-brown">Opere Recente</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="group inline-flex items-center gap-4 text-dark-brown hover:text-vintage-gold transition-colors duration-300">
                    <span class="text-sm uppercase tracking-[0.16em] font-semibold">Vezi tot portofoliul</span>
                    <span class="w-12 h-px bg-dark-brown group-hover:bg-vintage-gold transition-colors duration-300" aria-hidden="true"></span>
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
                                : ($product->image ? asset('storage/'.$product->image) : null);
                        @endphp
                        <article class="group bg-ivory shadow-sm border border-black/5 hover:shadow-md transition-all duration-300">
                            <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                <div class="aspect-[3/4] overflow-hidden bg-white relative p-4 flex items-center justify-center">
                                    @if($homeImageUrl)
                                        <img src="{{ $homeImageUrl }}"
                                             alt="{{ $product->name }}"
                                             width="600"
                                             height="800"
                                             loading="lazy"
                                             fetchpriority="low"
                                             decoding="async"
                                             class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700 ease-out">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-warm-beige/30 text-dark-brown/60 font-serif text-2xl" aria-hidden="true">MTD ART</div>
                                    @endif

                                    @if($product->is_custom)
                                        <span class="absolute top-4 left-4 bg-vintage-gold text-white text-xs px-3 py-2 uppercase tracking-[0.14em] font-semibold shadow-sm">
                                            Unicat / Comandă
                                        </span>
                                    @endif
                                </div>
                                <div class="p-5 border-t border-black/5">
                                    <h3 class="font-serif text-xl md:text-2xl text-dark-brown mb-2 truncate">{{ $product->name }}</h3>
                                    <p class="text-vintage-gold font-sans text-sm tracking-[0.12em] uppercase font-semibold">
                                        {{ $product->displayPrice() }}
                                    </p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endsection
