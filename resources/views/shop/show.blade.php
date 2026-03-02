@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12 md:py-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
        
        <div class="space-y-4">
            <div class="aspect-square bg-white overflow-hidden border border-brass/10">
                <img src="{{ $featuredImage ? asset('storage/' . $featuredImage->image_path) : 'https://via.placeholder.com/800' }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover hover:scale-105 transition duration-700">
            </div>
            
            @if($product->images->count() > 1)
            <div class="grid grid-cols-4 gap-4">
                @foreach($product->images as $image)
                    <div class="aspect-square bg-white border border-brass/5 cursor-pointer hover:border-brass transition">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="sticky top-32">
            <nav class="flex mb-4 text-[10px] uppercase tracking-widest opacity-60">
                <a href="{{ route('home') }}" class="hover:text-brass">Acasă</a>
                <span class="mx-2">/</span>
                <a href="{{ route('shop.index') }}" class="hover:text-brass">Galerie</a>
            </nav>

            <h1 class="font-serif text-4xl md:text-5xl mb-4 italic">{{ $product->name }}</h1>
            
            <div class="h-px w-20 bg-brass mb-8"></div>

            <div class="prose prose-sm font-light text-espresso/80 mb-10">
                {!! $product->description !!}
            </div>

            @if($product->is_custom)
                <div class="bg-white border border-brass/20 p-8 shadow-sm">
                    <h3 class="font-serif text-xl mb-6">Solicită o Piesă Personalizată</h3>
                    
                    <form action="{{ route('custom-request.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest mb-2">Nume Complet</label>
                            <input type="text" name="customer_name" required class="w-full bg-ivory border border-brass/20 px-4 py-3 focus:outline-none focus:border-brass transition">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest mb-2">Email</label>
                                <input type="email" name="customer_email" required class="w-full bg-ivory border border-brass/20 px-4 py-3 focus:outline-none focus:border-brass">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest mb-2">Telefon</label>
                                <input type="text" name="customer_phone" class="w-full bg-ivory border border-brass/20 px-4 py-3 focus:outline-none focus:border-brass">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase tracking-widest mb-2">Dimensiuni dorite / Mesaj</label>
                            <textarea name="special_message" rows="3" class="w-full bg-ivory border border-brass/20 px-4 py-3 focus:outline-none focus:border-brass"></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase tracking-widest mb-2 text-brass">Încarcă o imagine de referință (Opțional)</label>
                            <input type="file" name="reference_image" class="text-xs">
                        </div>

                        <button type="submit" class="w-full bg-espresso text-ivory py-4 uppercase tracking-[0.2em] text-xs hover:bg-brass transition duration-500 mt-4">
                            Trimite Solicitarea
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center justify-between mb-8">
                    <span class="text-3xl font-serif tracking-wider">{{ $product->price }} RON</span>
                    <span class="text-[10px] uppercase tracking-widest {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock > 0 ? 'În Stoc' : 'Indisponibil' }}
                    </span>
                </div>

                <button class="w-full bg-espresso text-ivory py-5 uppercase tracking-[0.3em] text-xs hover:bg-brass transition duration-500 shadow-xl">
                    Adaugă în Coș
                </button>
            @endif

            <div class="mt-12 pt-8 border-t border-brass/10">
                <p class="text-[10px] uppercase tracking-[0.2em] opacity-50 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 13l4 4L19 7" />
                    </svg>
                    Livrare asigurată în condiții de maximă siguranță
                </p>
            </div>
        </div>
    </div>
</div>

@if($relatedProducts->count() > 0)
<section class="max-w-7xl mx-auto px-4 py-24 border-t border-brass/10">
    <h2 class="font-serif text-3xl mb-12 italic text-center">Alte Creații din Galerie</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        @foreach($relatedProducts as $related)
            <a href="{{ route('shop.show', $related->slug) }}" class="group">
                <div class="aspect-square overflow-hidden bg-white mb-4">
                    <img src="{{ $related->images->first() ? asset('storage/' . $related->images->first()->image_path) : 'https://via.placeholder.com/400' }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                </div>
                <h4 class="uppercase text-[10px] tracking-widest text-center">{{ $related->name }}</h4>
            </a>
        @endforeach
    </div>
</section>
@endif

@endsection
