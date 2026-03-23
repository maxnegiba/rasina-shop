@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    
    <div class="flex flex-col md:flex-row gap-16 lg:gap-24 items-start">
        
        <!-- Sidebar -->
        <aside class="w-full md:w-1/4 md:sticky md:top-32 border-r border-black/5 pr-8">
            <h1 class="font-serif text-3xl md:text-4xl text-smoked-black mb-8 leading-tight">
                {{ isset($category) ? $category->name : 'Galerie' }}
            </h1>
            <p class="text-smoked-black/50 font-light text-xs md:text-sm leading-relaxed mb-12">
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
                       class="{{ !isset($category) ? 'text-smoked-black font-medium border-b border-smoked-black pb-1' : 'text-smoked-black/40 hover:text-vintage-gold' }} transition-colors duration-300 flex items-center">
                        Toate Piesele
                    </a>
                </li>
                @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('shop.category', $cat->slug) }}" 
                           class="flex justify-between items-center {{ isset($category) && $category->id === $cat->id ? 'text-smoked-black font-medium border-b border-smoked-black pb-1' : 'text-smoked-black/40 hover:text-vintage-gold' }} transition-colors duration-300">
                            <span>{{ $cat->name }}</span>
                            <span class="text-[9px] text-smoked-black/30 font-sans">
                                {{ str_pad($cat->products_count, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <!-- Product Grid -->
        <div class="w-full md:w-3/4">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-16">
                    @foreach($products as $product)
                        <a href="{{ route('shop.show', $product->slug) }}" class="group block">
                            <div class="aspect-[3/4] overflow-hidden bg-warm-beige/20 mb-6 relative">
                                
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
                                     class="w-full h-full object-cover filter contrast-[0.95] group-hover:contrast-100 group-hover:scale-105 transition duration-700 ease-out">
                                
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-500"></div>

                                @if($product->is_custom)
                                    <div class="absolute top-4 left-4 bg-ivory/90 backdrop-blur-sm text-smoked-black text-[9px] px-3 py-1.5 uppercase tracking-[0.2em] font-medium shadow-sm">
                                        Comandă
                                    </div>
                                @endif
                            </div>
                            
                            <div class="text-left px-1">
                                <h2 class="font-serif text-lg text-smoked-black mb-2 group-hover:text-vintage-gold transition-colors duration-300">
                                    {{ $product->name }}
                                </h2>
                                <p class="text-smoked-black/60 font-sans text-xs tracking-[0.1em] uppercase">
                                    {{ $product->is_custom ? 'Preț la cerere' : $product->price . ' RON' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-24 pt-8 border-t border-black/5 flex justify-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-32">
                    <span class="block text-vintage-gold text-4xl mb-6">✧</span>
                    <h3 class="font-serif text-2xl mb-4 text-smoked-black">Colecția este momentan privată</h3>
                    <p class="font-light text-smoked-black/50 mb-10 text-sm max-w-md mx-auto">Nu am găsit lucrări publicate în această secțiune. Vă invităm să explorați restul galeriei noastre.</p>
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-3 group text-xs uppercase tracking-[0.2em] text-smoked-black font-medium">
                        <span>Înapoi la galerie</span>
                        <span class="w-8 h-px bg-smoked-black group-hover:w-12 transition-all duration-300"></span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection