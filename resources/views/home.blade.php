@extends('layouts.app')

@section('content')
    <!-- EROU (HERO) SECTION -->
    <section class="relative min-h-[90vh] flex flex-col justify-center overflow-hidden bg-dark-brown border-b border-vintage-gold/30">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&q=80&w=2070"
                 class="w-full h-full object-cover opacity-40 mix-blend-luminosity scale-105 transform origin-center" alt="Artă din rășină">
            <div class="absolute inset-0 bg-gradient-to-t from-dark-brown via-transparent to-dark-brown/60"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-20">
            <div class="max-w-3xl">
                <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-6 drop-shadow-sm">Studio de Artă și Design</span>
                <h1 class="font-serif text-6xl md:text-8xl text-white leading-[1.1] mb-8 drop-shadow-lg">
                    Eleganță<br>
                    <span class="italic text-white/90">în Rășină</span>
                </h1>
                <p class="text-white/80 text-lg md:text-xl font-light tracking-wide mb-12 max-w-xl leading-relaxed">
                    Descoperă obiecte de cult și piese de mobilier unicat, create manual pentru a dăinui o viață. O fuziune între tradiție și minimalism modern.
                </p>
                <div class="flex flex-col sm:flex-row gap-6">
                    <a href="{{ route('shop.index') }}"
                       class="group relative inline-flex items-center justify-center px-10 py-4 bg-vintage-gold text-white font-medium uppercase tracking-[0.2em] text-xs hover:bg-white hover:text-dark-brown transition duration-500 overflow-hidden shadow-lg shadow-vintage-gold/20">
                        <span class="relative z-10">Explorați Galeria</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce text-white/50">
            <span class="text-[10px] uppercase tracking-[0.3em]">Descoperă</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </div>
    </section>

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
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://via.placeholder.com/600x800' }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" alt="{{ $category->name }}">
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
                        <div class="group bg-ivory shadow-sm border border-black/5 hover:shadow-md transition-all duration-300">
                            <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                <!-- Modificat object-cover in object-contain -->
                                <div class="aspect-[3/4] overflow-hidden bg-white relative p-4 flex items-center justify-center">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/500' }}" 
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
                                        {{ $product->is_custom ? 'Preț la cerere' : $product->price . ' RON' }}
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