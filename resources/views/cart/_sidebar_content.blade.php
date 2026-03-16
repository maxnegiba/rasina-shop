<div class="flex flex-col h-full bg-ivory">
    <div class="flex justify-between items-center p-6 border-b border-black/5 bg-white">
        <span class="font-serif text-lg tracking-[0.1em] uppercase text-smoked-black flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-vintage-gold"></span>
            Colecția Mea
        </span>
        <button id="cart-sidebar-close" class="text-smoked-black focus:outline-none hover:text-vintage-gold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex-grow py-6 px-4 sm:px-6 overflow-y-auto" id="cart-items-container">
        @php
            $cart = session()->get('cart', []);
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
        @endphp

        @if(count($cart) > 0)
            <div class="space-y-6">
                @foreach($cart as $id => $details)
                    <div class="flex gap-4 items-center bg-white p-3 border border-black/5 relative group">
                        <div class="w-20 h-24 flex-shrink-0 bg-warm-beige/20 overflow-hidden">
                            <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}" class="w-full h-full object-cover filter contrast-95">
                        </div>

                        <div class="flex-grow pr-6">
                            <h3 class="font-serif text-sm text-smoked-black mb-1 line-clamp-1" title="{{ $details['name'] }}">{{ $details['name'] }}</h3>
                            <p class="text-[10px] font-sans uppercase tracking-[0.1em] text-smoked-black/50 mb-2">
                                Cantitate: {{ $details['quantity'] }}
                            </p>
                            <p class="text-xs font-sans font-medium text-smoked-black tracking-wide">
                                {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} RON
                            </p>
                        </div>

                        <!-- Buton eliminare -->
                        <button type="button" class="remove-from-cart-btn absolute top-3 right-3 text-smoked-black/30 hover:text-red-500 transition-colors" data-id="{{ $id }}" title="Elimină">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="h-full flex flex-col items-center justify-center text-center py-12">
                <span class="block text-vintage-gold text-2xl mb-4">✧</span>
                <h3 class="font-serif text-lg mb-2 text-smoked-black">Colecția ta este goală</h3>
                <p class="font-light text-smoked-black/50 text-xs max-w-[200px] mx-auto">Nu ai adăugat încă nicio piesă în colecție.</p>
            </div>
        @endif
    </div>

    <!-- Sumar Footer -->
    <div class="p-6 border-t border-black/5 bg-white">
        <div class="flex justify-between items-end mb-6">
            <span class="text-xs uppercase tracking-[0.1em] text-smoked-black font-medium">Subtotal</span>
            <span class="font-sans text-lg text-smoked-black tracking-wide font-medium" id="cart-sidebar-total">
                {{ number_format($total ?? 0, 0, ',', '.') }} RON
            </span>
        </div>

        @if(count($cart) > 0)
            <form action="{{ route('checkout.session') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-smoked-black text-white px-6 py-4 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 shadow-sm flex justify-center items-center gap-2 group">
                    <span>Spre Plată</span>
                    <span class="transform group-hover:translate-x-1 transition-transform duration-300">&rarr;</span>
                </button>
            </form>
        @else
            <button disabled class="w-full bg-black/5 text-smoked-black/30 px-6 py-4 uppercase tracking-[0.2em] text-[10px] font-medium cursor-not-allowed">
                Spre Plată
            </button>
        @endif
    </div>
</div>
