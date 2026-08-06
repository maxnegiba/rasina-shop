@extends('layouts.app')

@section('content')
<div class="bg-ivory min-h-[70vh] py-16 md:py-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 md:mb-14">
            <span class="block text-vintage-gold tracking-[0.24em] text-[10px] font-semibold uppercase mb-3">Comanda dumneavoastră</span>
            <h1 class="font-serif text-4xl md:text-5xl text-dark-brown">Coș de cumpărături</h1>
            <p class="mt-3 text-sm text-dark-brown/55">Verificați produsele și cantitățile înainte de plată.</p>
        </div>

        @if(count($cart) === 0)
            <div class="bg-white border border-black/5 px-6 py-20 text-center shadow-sm">
                <span class="block text-vintage-gold text-4xl mb-5" aria-hidden="true">✧</span>
                <h2 class="font-serif text-2xl text-dark-brown mb-3">Coșul este gol</h2>
                <p class="text-sm text-dark-brown/55 mb-8">Alegeți o piesă disponibilă pentru a începe comanda.</p>
                <a href="{{ route('shop.index') }}" class="inline-flex bg-dark-brown text-white px-8 py-4 uppercase tracking-[0.16em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors">Descoperă produsele</a>
            </div>
        @else
            <div class="grid lg:grid-cols-[1fr_340px] gap-8 lg:gap-12 items-start">
                <div class="space-y-4">
                    @foreach($cart as $id => $details)
                        <article class="bg-white border border-black/5 p-4 sm:p-6 flex flex-col sm:flex-row gap-5 shadow-sm">
                            <a href="{{ route('shop.show', $details['slug']) }}" class="w-full sm:w-32 aspect-[4/5] flex-shrink-0 bg-warm-beige/20 overflow-hidden">
                                <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}" class="w-full h-full object-cover">
                            </a>
                            <div class="flex-grow min-w-0">
                                <a href="{{ route('shop.show', $details['slug']) }}" class="font-serif text-xl text-dark-brown hover:text-vintage-gold transition-colors">{{ $details['name'] }}</a>
                                <p class="mt-2 text-xs text-dark-brown/50">Preț unitar: {{ number_format($details['price'], 2, ',', '.') }} RON</p>
                                <div class="mt-5 flex flex-wrap items-end justify-between gap-4">
                                    <form action="{{ route('cart.update') }}" method="POST" class="cart-quantity-form flex items-end gap-2">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <div>
                                            <label for="cart-quantity-{{ $id }}" class="block text-[10px] uppercase tracking-[0.12em] text-dark-brown/55 mb-2">Cantitate</label>
                                            <input id="cart-quantity-{{ $id }}" name="quantity" type="number" inputmode="numeric" min="1" max="{{ $details['stock'] }}" value="{{ $details['quantity'] }}" class="w-20 border border-black/15 px-3 py-3 text-center text-sm focus:border-vintage-gold focus:ring-vintage-gold">
                                        </div>
                                        <button type="submit" class="border border-dark-brown px-4 py-3 text-[9px] uppercase tracking-[0.12em] hover:bg-dark-brown hover:text-white transition-colors disabled:opacity-50">Actualizează</button>
                                    </form>
                                    <div class="text-right">
                                        <p class="text-[10px] text-dark-brown/45 mb-1">Total produs</p>
                                        <p class="font-semibold text-dark-brown">{{ number_format($details['price'] * $details['quantity'], 2, ',', '.') }} RON</p>
                                    </div>
                                </div>
                                <form action="{{ route('cart.remove') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" class="text-xs text-red-700/70 underline hover:text-red-700">Elimină din coș</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="bg-white border border-black/5 p-6 lg:sticky lg:top-32 shadow-sm">
                    <h2 class="font-serif text-2xl text-dark-brown mb-6">Sumar comandă</h2>
                    <dl class="space-y-3 text-sm text-dark-brown/65">
                        <div class="flex justify-between gap-4"><dt>Produse ({{ $summary['item_count'] }})</dt><dd>{{ number_format($summary['subtotal'], 2, ',', '.') }} RON</dd></div>
                        <div class="flex justify-between gap-4"><dt>Livrare</dt><dd>{{ $summary['shipping'] > 0 ? number_format($summary['shipping'], 2, ',', '.').' RON' : 'Gratuită' }}</dd></div>
                        @if($summary['discount'] > 0)
                            <div class="flex justify-between gap-4 text-emerald-700"><dt>Reducere</dt><dd>−{{ number_format($summary['discount'], 2, ',', '.') }} RON</dd></div>
                        @endif
                        <div class="flex justify-between gap-4 border-t border-black/10 pt-4 text-dark-brown font-semibold"><dt>Total de plată</dt><dd>{{ number_format($summary['total'], 2, ',', '.') }} RON</dd></div>
                    </dl>
                    <a href="{{ route('checkout.index') }}" class="mt-7 w-full flex justify-center bg-dark-brown text-white px-6 py-4 uppercase tracking-[0.16em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors">Continuă către plată</a>
                    <a href="{{ route('shop.index') }}" class="mt-4 block text-center text-xs text-dark-brown/55 underline hover:text-vintage-gold">Continuă cumpărăturile</a>
                    <div class="mt-7 border-t border-black/5 pt-5 space-y-2 text-[10px] text-dark-brown/50">
                        <p>✓ Plată securizată prin Stripe</p>
                        <p>✓ Retur conform politicii magazinului</p>
                        <p>✓ Confirmare și proforma trimise pe email</p>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</div>
@endsection
