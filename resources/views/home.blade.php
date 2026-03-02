@extends('layouts.app')

@section('content')
    <section class="relative h-[80vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-espresso/30 z-10"></div>
        <img src="https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&q=80&w=2070" 
             class="absolute inset-0 w-full h-full object-cover scale-105" alt="Artă din rășină">
        
        <div class="relative z-20 text-center px-4">
            <h1 class="font-serif text-5xl md:text-7xl text-ivory mb-6 italic">Eleganță în Rășină</h1>
            <p class="text-ivory text-lg md:text-xl font-light tracking-wide mb-10 max-w-2xl mx-auto">
                Descoperă obiecte de cult și piese de mobilier unicat, create manual pentru a dăinui o viață.
            </p>
            <a href="{{ route('shop.index') }}" 
               class="inline-block border border-ivory text-ivory px-10 py-4 uppercase tracking-widest text-sm hover:bg-ivory hover:text-espresso transition duration-500">
                Explorează Galeria
            </a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto py-24 px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            @foreach($featuredCategories as $category)
                <div class="group cursor-pointer">
                    <div class="overflow-hidden aspect-[3/4] mb-6">
                        <img src="https://via.placeholder.com/600x800" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    </div>
                    <h3 class="font-serif text-2xl text-center uppercase tracking-wider">{{ $category->name }}</h3>
                    <div class="w-10 h-px bg-brass mx-auto mt-4 group-hover:w-20 transition-all"></div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-24">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <span class="text-brass uppercase tracking-widest text-xs">Colecția Nouă</span>
                    <h2 class="font-serif text-4xl mt-2">Piese Recente</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="text-sm uppercase tracking-widest border-b border-brass pb-1">Vezi tot</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($latestProducts as $product)
                    <div class="group">
                        <a href="{{ route('shop.show', $product->slug) }}">
                            <div class="aspect-square overflow-hidden bg-ivory mb-4 relative">
                                <img src="https://via.placeholder.com/500" class="w-full h-full object-cover group-hover:opacity-80 transition">
                                
                                @if($product->is_custom)
                                    <span class="absolute top-4 left-4 bg-brass text-ivory text-[10px] px-2 py-1 uppercase tracking-tighter">La Comandă</span>
                                @endif
                            </div>
                            <h4 class="font-medium text-espresso uppercase text-sm tracking-wide">{{ $product->name }}</h4>
                            <p class="text-brass mt-1">
                                {{ $product->is_custom ? 'Preț la cerere' : $product->price . ' RON' }}
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
