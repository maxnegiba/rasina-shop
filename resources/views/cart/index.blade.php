@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    <div class="mb-12">
        <h1 class="font-serif text-3xl md:text-4xl text-smoked-black mb-4">Colecția Mea</h1>
        <div class="w-12 h-px bg-vintage-gold"></div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50/50 text-green-800 text-sm border-l border-green-500/30 font-light">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 p-4 bg-red-50/50 text-red-800 text-sm border-l border-red-500/30 font-light">
            {{ session('error') }}
        </div>
    @endif

    @if(count($cart) > 0)
        <div class="flex flex-col lg:flex-row gap-16 items-start">

            <!-- Tabel Produse -->
            <div class="w-full lg:w-2/3">
                <div class="border-t border-black/5">
                    @foreach($cart as $id => $details)
                        <div class="py-8 border-b border-black/5 flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                            <div class="w-24 h-32 flex-shrink-0 bg-warm-beige/20 overflow-hidden">
                                <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}" class="w-full h-full object-cover filter contrast-95">
                            </div>

                            <div class="flex-grow">
                                <h3 class="font-serif text-lg text-smoked-black mb-1">{{ $details['name'] }}</h3>
                                <p class="text-xs font-sans uppercase tracking-[0.1em] text-smoked-black/50 mb-4">
                                    Cantitate: {{ $details['quantity'] }}
                                </p>
                                <p class="text-sm font-sans text-smoked-black/80 tracking-wide">
                                    {{ number_format($details['price'], 0, ',', '.') }} RON
                                </p>
                            </div>

                            <div class="sm:text-right mt-4 sm:mt-0">
                                <p class="text-sm font-sans font-medium text-smoked-black tracking-wide mb-4">
                                    {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} RON
                                </p>
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" class="text-[10px] uppercase tracking-[0.15em] text-smoked-black/40 hover:text-red-500 transition-colors duration-300">
                                        Elimină
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <a href="{{ route('shop.index') }}" class="text-[10px] uppercase tracking-[0.15em] text-smoked-black/60 hover:text-vintage-gold transition-colors duration-300 flex items-center gap-2">
                        &larr; Continuă Explorarea
                    </a>
                </div>
            </div>

            <!-- Sumar Comandă -->
            <div class="w-full lg:w-1/3 bg-warm-beige/10 p-8 border border-black/5 sticky top-32">
                <h3 class="font-serif text-xl text-smoked-black mb-6">Sumar</h3>

                <div class="space-y-4 text-sm font-light text-smoked-black/80 mb-6 pb-6 border-b border-black/5">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>{{ number_format($total, 0, ',', '.') }} RON</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Livrare</span>
                        <span class="text-xs italic text-smoked-black/50">Calculată la pasul următor</span>
                    </div>
                </div>

                <div class="flex justify-between items-end mb-8">
                    <span class="text-sm uppercase tracking-[0.1em] text-smoked-black font-medium">Total Estimativ</span>
                    <span class="font-sans text-xl text-smoked-black tracking-wide">
                        {{ number_format($total, 0, ',', '.') }} RON
                    </span>
                </div>

                <form action="{{ route('checkout.session') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-smoked-black text-white px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 shadow-sm flex justify-center items-center gap-2 group">
                        <span>Spre Plată</span>
                        <span class="transform group-hover:translate-x-1 transition-transform duration-300">&rarr;</span>
                    </button>
                </form>

                <div class="mt-6 text-center space-y-2">
                    <p class="text-[9px] uppercase tracking-[0.15em] text-smoked-black/40 flex items-center justify-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Plată securizată prin Stripe
                    </p>
                </div>
            </div>

        </div>
    @else
        <div class="text-center py-24 bg-warm-beige/5 border border-black/5">
            <span class="block text-vintage-gold text-3xl mb-4">✧</span>
            <h3 class="font-serif text-xl mb-3 text-smoked-black">Colecția ta este goală</h3>
            <p class="font-light text-smoked-black/50 mb-8 text-sm max-w-sm mx-auto">Nu ai adăugat încă nicio piesă în colecție. Te invităm să explorezi galeria noastră.</p>
            <a href="{{ route('shop.index') }}" class="inline-block border border-smoked-black/20 px-8 py-4 uppercase tracking-[0.2em] text-[10px] text-smoked-black hover:bg-smoked-black hover:text-white transition-all duration-300">
                Explorează Galeria
            </a>
        </div>
    @endif
</div>
@endsection