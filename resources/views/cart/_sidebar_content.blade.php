@php
    $cart = $cart ?? session()->get('cart', []);
    $summary = $summary ?? [
        'item_count' => (int) collect($cart)->sum(fn ($item) => (int) ($item['quantity'] ?? 0)),
        'subtotal' => round((float) collect($cart)->sum(fn ($item) => (float) $item['price'] * (int) $item['quantity']), 2),
        'shipping' => count($cart) > 0 ? round((float) config('shop.shipping_cost', 0), 2) : 0,
        'discount' => 0,
        'total' => 0,
    ];
    $summary['total'] = $summary['total'] ?: round($summary['subtotal'] + $summary['shipping'] - $summary['discount'], 2);
@endphp

<div class="flex flex-col h-full bg-ivory"
     data-cart-remove-url="{{ route('cart.remove') }}"
     data-cart-update-url="{{ route('cart.update') }}">
    <div class="flex justify-between items-center p-5 sm:p-6 border-b border-black/5 bg-ivory">
        <div>
            <span class="font-serif text-lg tracking-[0.08em] uppercase text-dark-brown flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-vintage-gold"></span>
                Coșul dumneavoastră
            </span>
            <p class="mt-1 text-[10px] text-dark-brown/50">{{ $summary['item_count'] }} {{ $summary['item_count'] === 1 ? 'produs' : 'produse' }}</p>
        </div>
        <button type="button" id="cart-sidebar-close" aria-label="Închide coșul" class="text-dark-brown focus:outline-none hover:text-vintage-gold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex-grow py-5 px-4 sm:px-6 overflow-y-auto" id="cart-items-container">
        @if(count($cart) > 0)
            <div class="space-y-4">
                @foreach($cart as $id => $details)
                    <article class="flex gap-4 items-start bg-white/60 p-3 border border-black/5 relative group">
                        <a href="{{ isset($details['slug']) ? route('shop.show', $details['slug']) : route('shop.index') }}" class="w-20 h-24 flex-shrink-0 bg-warm-beige/20 overflow-hidden">
                            <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}" class="w-full h-full object-cover filter contrast-95">
                        </a>

                        <div class="flex-grow min-w-0 pr-5">
                            <a href="{{ isset($details['slug']) ? route('shop.show', $details['slug']) : route('shop.index') }}" class="font-serif text-sm text-dark-brown hover:text-vintage-gold transition-colors line-clamp-2" title="{{ $details['name'] }}">
                                {{ $details['name'] }}
                            </a>
                            <p class="mt-1 text-[10px] text-dark-brown/50">
                                {{ number_format($details['price'], 2, ',', '.') }} RON / buc.
                            </p>

                            <form action="{{ route('cart.update') }}" method="POST" class="cart-quantity-form mt-3 flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <label for="sidebar-quantity-{{ $id }}" class="sr-only">Cantitate pentru {{ $details['name'] }}</label>
                                <input id="sidebar-quantity-{{ $id }}"
                                       name="quantity"
                                       type="number"
                                       inputmode="numeric"
                                       min="1"
                                       max="{{ $details['stock'] ?? $details['quantity'] }}"
                                       value="{{ $details['quantity'] }}"
                                       class="w-16 border border-black/15 bg-white px-2 py-2 text-center text-xs focus:border-vintage-gold focus:ring-vintage-gold">
                                <button type="submit" class="px-2 py-2 text-[9px] uppercase tracking-[0.1em] border border-black/10 hover:border-vintage-gold hover:text-vintage-gold disabled:opacity-50">
                                    Actualizează
                                </button>
                            </form>

                            <p class="mt-3 text-xs font-medium text-dark-brown tracking-wide">
                                {{ number_format($details['price'] * $details['quantity'], 2, ',', '.') }} RON
                            </p>
                        </div>

                        <button type="button"
                                class="remove-from-cart-btn absolute top-3 right-3 text-dark-brown/30 hover:text-red-600 transition-colors disabled:opacity-50"
                                data-id="{{ $id }}"
                                aria-label="Elimină {{ $details['name'] }} din coș"
                                title="Elimină">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </article>
                @endforeach
            </div>
        @else
            <div class="h-full flex flex-col items-center justify-center text-center py-12">
                <span class="block text-vintage-gold text-3xl mb-4" aria-hidden="true">✧</span>
                <h3 class="font-serif text-xl mb-2 text-dark-brown">Coșul este gol</h3>
                <p class="font-light text-dark-brown/55 text-xs max-w-[230px] mx-auto mb-6">Descoperiți piesele disponibile și adăugați produsul preferat în coș.</p>
                <a href="{{ route('shop.index') }}" class="border border-dark-brown px-5 py-3 text-[9px] uppercase tracking-[0.16em] hover:bg-dark-brown hover:text-white transition-colors">
                    Vezi galeria
                </a>
            </div>
        @endif
    </div>

    <div class="p-5 sm:p-6 border-t border-black/5 bg-ivory">
        @if(count($cart) > 0)
            <dl class="space-y-2 text-xs text-dark-brown/70 mb-5">
                <div class="flex justify-between gap-4">
                    <dt>Subtotal</dt>
                    <dd>{{ number_format($summary['subtotal'], 2, ',', '.') }} RON</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt>Livrare</dt>
                    <dd>{{ $summary['shipping'] > 0 ? number_format($summary['shipping'], 2, ',', '.').' RON' : 'Gratuită' }}</dd>
                </div>
                @if($summary['discount'] > 0)
                    <div class="flex justify-between gap-4 text-emerald-700">
                        <dt>Reducere</dt>
                        <dd>−{{ number_format($summary['discount'], 2, ',', '.') }} RON</dd>
                    </div>
                @endif
                <div class="flex justify-between items-end pt-3 border-t border-black/10 text-dark-brown">
                    <dt class="uppercase tracking-[0.1em] font-semibold">Total</dt>
                    <dd class="font-sans text-lg font-semibold">{{ number_format($summary['total'], 2, ',', '.') }} RON</dd>
                </div>
            </dl>

            <a href="{{ route('checkout.index') }}" class="w-full bg-dark-brown text-white px-6 py-4 uppercase tracking-[0.18em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors duration-300 shadow-sm flex justify-center items-center gap-2 group">
                <span>Finalizează comanda</span>
                <span class="transform group-hover:translate-x-1 transition-transform" aria-hidden="true">→</span>
            </a>
            <a href="{{ route('cart.index') }}" class="mt-3 block text-center text-[10px] text-dark-brown/55 underline hover:text-vintage-gold">Vezi coșul complet</a>
            <p class="mt-4 text-center text-[9px] text-dark-brown/45 leading-relaxed">Plată securizată prin Stripe. Coșul se păstrează dacă părăsești pagina.</p>
        @endif
    </div>
</div>
