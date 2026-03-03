@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12 md:py-24">
    
    <div class="text-center mb-16 md:mb-24">
        <h1 class="font-serif text-4xl md:text-5xl italic mb-4 text-espresso">
            {{ isset($category) ? $category->name : 'Galeria de Artă' }}
        </h1>
        <p class="text-espresso/70 font-light max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
            {{ isset($category) && $category->description 
                ? $category->description 
                : 'Explorează colecția noastră de piese unicat, create manual cu pasiune și atenție la detalii.' }}
        </p>
        <div class="w-24 h-px bg-brass mx-auto mt-8"></div>
    </div>

    <div class="flex flex-col md:flex-row gap-12 items-start">
        
        <aside class="w-full md:w-1/4 sticky top-32">
            <h3 class="font-serif text-xl mb-6 uppercase tracking-widest border-b border-brass/20 pb-4 text-espresso">
                Colecții
            </h3>
            <ul class="space-y-4 font-light text-sm tracking-wide">
                <li>
                    <a href="{{ route('shop.index') }}" 
                       class="{{ !isset($category) ? 'text-brass font-medium' : 'text-espresso/70 hover:text-brass' }} transition flex items-center">
                        Toate Piesele
                    </a>
                </li>
                @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('shop.category', $cat->slug) }}" 
                           class="flex justify-between items-center {{ isset($category) && $category->id === $cat->id ? 'text-brass font-medium' : 'text-espresso/70 hover:text-brass' }} transition">
                            <span>{{ $cat->name }}</span>
                            <span class="text-[10px] bg-brass/10 text-brass px-2 py-0.5 rounded-full">
                                {{ $cat->products_count }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <div class="w-full md:w-3/4">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($products as $product)
                        <a href="{{ route('shop.show', $product->slug) }}" class="group block">
                            <div class="aspect-[4/5] bg-white overflow-hidden relative mb-4 border border-brass/5 group-hover:border-brass/30 transition duration-500 shadow-sm group-hover:shadow-md">
                                
                                @php
                                    // Logica de imagini antiglonț
                                    $imageUrl = null;
                                    
                                    if (!empty($product->image)) {
                                        // Varianta 1: Dacă folosești câmpul simplu 'image'
                                        $imageUrl = asset('storage/' . $product->image);
                                    } elseif (isset($product->images) && $product->images->count() > 0) {
                                        // Varianta 2: Dacă folosești relația cu mai multe imagini
                                        $firstImage = $product->images->where('is_featured', true)->first() ?? $product->images->first();
                                        $imageUrl = asset('storage/' . $firstImage->image_path);
                                    } else {
                                        // Varianta 3: Fallback local SVG (nu declanșează ad-blockerele)
                                        $imageUrl = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MDAiIGhlaWdodD0iODAwIiBmaWxsPSIjRkRGQkY3Ij48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjgwMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2VyaWYiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNDNUE4ODAiPkl2b3J5IFZpbnRhZ2U8L3RleHQ+PC9zdmc+';
                                    }
                                @endphp
                                
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                                
                                @if($product->is_custom)
                                    <div class="absolute top-4 left-4 bg-espresso/90 backdrop-blur-sm text-ivory text-[9px] px-3 py-1 uppercase tracking-widest">
                                        La Comandă
                                    </div>
                                @endif
                            </div>
                            
                            <div class="text-center px-2">
                                <h2 class="font-serif text-lg uppercase tracking-wider text-espresso mb-1 truncate">
                                    {{ $product->name }}
                                </h2>
                                <p class="text-brass text-sm tracking-widest">
                                    {{ $product->is_custom ? 'Preț la cerere' : $product->price . ' RON' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-16 flex justify-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-32 border border-dashed border-brass/30 bg-white/50">
                    <h3 class="font-serif text-2xl mb-2 text-espresso/50 italic">Nicio piesă găsită.</h3>
                    <p class="font-light text-espresso/50 mb-8 text-sm">Această colecție nu conține momentan lucrări publicate.</p>
                    <a href="{{ route('shop.index') }}" class="inline-block border border-brass text-brass px-8 py-3 uppercase tracking-widest text-xs hover:bg-brass hover:text-ivory transition duration-300">
                        Vezi Toate Piesele
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection