@extends('layouts.app')

@section('content')
@php
    $state = $paymentState ?? 'invalid';
    $isPaid = $state === 'paid';
    $isCod = $state === 'cod';
    $isPending = $state === 'pending';
    $isFailed = $state === 'failed';
    $isConfirmed = $isPaid || $isCod;
@endphp

<div class="bg-ivory min-h-[70vh] py-20 md:py-28">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex w-20 h-20 rounded-full items-center justify-center mb-7 border {{ $isConfirmed ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($isPending ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-red-50 border-red-200 text-red-700') }}">
            @if($isConfirmed)
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            @elseif($isPending)
                <svg class="w-9 h-9 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            @else
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            @endif
        </div>

        <span class="block text-vintage-gold tracking-[0.22em] text-[10px] font-semibold uppercase mb-3">
            {{ $isCod ? 'Comandă înregistrată' : ($isPaid ? 'Comandă confirmată' : ($isPending ? 'Plată în curs de confirmare' : 'Plată nefinalizată')) }}
        </span>
        <h1 class="font-serif text-4xl md:text-5xl text-dark-brown mb-5">
            {{ $isConfirmed ? 'Vă mulțumim pentru comandă!' : ($isPending ? 'Verificăm plata' : 'Plata nu a fost confirmată') }}
        </h1>
        <div class="w-14 h-px bg-vintage-gold mx-auto mb-7"></div>

        @if($isCod)
            <p class="text-dark-brown/65 font-light leading-relaxed">
                Comanda a fost înregistrată cu plata ramburs. Produsele se achită curierului la livrare, iar livrarea nu este inclusă în preț. Confirmarea comenzii și documentul proforma sunt trimise la adresa de email completată la checkout.
            </p>
            @if($order)
                <div class="mt-8 bg-white border border-black/5 p-5 text-left shadow-sm">
                    <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-[10px] uppercase tracking-[0.12em] text-dark-brown/45">Număr comandă</dt><dd class="mt-1 font-semibold text-dark-brown">{{ $order->order_number }}</dd></div>
                        <div><dt class="text-[10px] uppercase tracking-[0.12em] text-dark-brown/45">Total produse</dt><dd class="mt-1 font-semibold text-dark-brown">{{ number_format($order->total_amount, 2, ',', '.') }} RON</dd></div>
                    </dl>
                </div>
            @endif
            <p class="mt-6 text-xs text-dark-brown/50">Pregătim piesele cu grijă și vă vom contacta pentru detaliile livrării.</p>
        @elseif($isPaid)
            <p class="text-dark-brown/65 font-light leading-relaxed">
                Plata a fost validată în siguranță. Confirmarea comenzii și documentul proforma au fost trimise la adresa de email folosită la plată.
            </p>
            @if($order)
                <div class="mt-8 bg-white border border-black/5 p-5 text-left shadow-sm">
                    <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-[10px] uppercase tracking-[0.12em] text-dark-brown/45">Număr comandă</dt><dd class="mt-1 font-semibold text-dark-brown">{{ $order->order_number }}</dd></div>
                        <div><dt class="text-[10px] uppercase tracking-[0.12em] text-dark-brown/45">Total achitat</dt><dd class="mt-1 font-semibold text-dark-brown">{{ number_format($order->total_amount, 2, ',', '.') }} RON</dd></div>
                    </dl>
                </div>
            @endif
            <p class="mt-6 text-xs text-dark-brown/50">Pregătim piesele cu grijă și vă vom contacta pentru detaliile livrării.</p>
        @elseif($isPending)
            <p class="text-dark-brown/65 font-light leading-relaxed">
                Confirmarea poate dura câteva momente. Nu inițiați o plată nouă și nu apăsați din nou butonul de plată. Pagina va verifica automat starea tranzacției.
            </p>
            <p class="mt-5 text-xs text-dark-brown/50">Dacă starea nu se actualizează, contactați-ne la <a href="mailto:{{ config('shop.legal.email') }}" class="underline hover:text-vintage-gold">{{ config('shop.legal.email') }}</a>.</p>
        @elseif($isFailed)
            <p class="text-dark-brown/65 font-light leading-relaxed">
                Nu a fost confirmată nicio debitare. Produsele rămân în coș, iar plata poate fi încercată din nou cu aceeași metodă sau puteți alege plata ramburs.
            </p>
        @else
            <p class="text-dark-brown/65 font-light leading-relaxed">
                Linkul de confirmare este incomplet sau sesiunea nu mai este validă. Verificați emailul de confirmare înainte de a iniția o nouă comandă.
            </p>
        @endif

        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            @if($isFailed)
                <a href="{{ route('checkout.index') }}" class="bg-dark-brown text-white px-8 py-4 uppercase tracking-[0.16em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors">Revino la checkout</a>
                <form method="POST" action="{{ route('checkout.cancel') }}">@csrf<button type="submit" class="w-full border border-dark-brown text-dark-brown px-8 py-4 uppercase tracking-[0.16em] text-[10px] font-semibold hover:bg-dark-brown hover:text-white transition-colors">Revino la coș</button></form>
            @else
                <a href="{{ route('shop.index') }}" class="bg-dark-brown text-white px-8 py-4 uppercase tracking-[0.16em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors">Înapoi la galerie</a>
            @endif
        </div>
    </div>
</div>

@if($isPending)
<script>
(() => {
    const key = 'mtd-payment-status-refreshes';
    const refreshes = Number(window.sessionStorage.getItem(key) || '0');
    if (refreshes < 4) {
        window.sessionStorage.setItem(key, String(refreshes + 1));
        window.setTimeout(() => window.location.reload(), 3500);
    }
})();
</script>
@elseif($isConfirmed)
<script>
try {
    window.sessionStorage.removeItem('mtd-payment-status-refreshes');
    window.sessionStorage.removeItem('mtd-checkout-email');
} catch (_) {}
</script>
@endif
@endsection
