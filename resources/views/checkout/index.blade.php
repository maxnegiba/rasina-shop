@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
    <div class="mb-12 text-center">
        <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-4">Finalizare Comandă</span>
        <h1 class="font-serif text-3xl md:text-4xl text-smoked-black mb-6">Verifică și continuă spre plată</h1>
        <p class="text-sm font-light text-smoked-black/60 tracking-wide">
            Plata cu cardul și introducerea adresei se fac securizat în pagina Stripe.
        </p>
    </div>

    <div class="bg-white p-6 md:p-8 border border-smoked-black/10 shadow-sm">
        <div class="space-y-4 border-b border-black/10 pb-6 mb-6">
            @foreach($cart as $item)
                <div class="flex items-start justify-between gap-6 text-sm">
                    <div>
                        <p class="font-medium text-dark-brown">{{ $item['name'] }}</p>
                        <p class="text-dark-brown/50">Cantitate: {{ $item['quantity'] }}</p>
                    </div>
                    <p class="text-dark-brown whitespace-nowrap">
                        {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }} RON
                    </p>
                </div>
            @endforeach
        </div>

        <div class="flex justify-between items-center mb-8">
            <span class="font-serif text-xl text-dark-brown">Total</span>
            <strong class="font-serif text-2xl text-dark-brown">{{ number_format($totalAmount, 2, ',', '.') }} RON</strong>
        </div>

        @if ($errors->any())
            <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.start') }}" id="checkout-form" class="space-y-4">
            @csrf

            <label class="flex items-start gap-3 text-sm text-dark-brown/75">
                <input type="checkbox" name="accept_terms" value="1" required class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                <span>
                    Accept <a href="{{ route('page.show', 'termeni-si-conditii') }}" target="_blank" class="text-vintage-gold underline">Termenii și Condițiile</a>, inclusiv politica de livrare și retragere.
                </span>
            </label>

            <label class="flex items-start gap-3 text-sm text-dark-brown/75">
                <input type="checkbox" name="acknowledge_privacy" value="1" required class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                <span>
                    Confirm că am citit <a href="{{ route('page.show', 'politica-de-confidentialitate') }}" target="_blank" class="text-vintage-gold underline">Politica de Confidențialitate</a>.
                </span>
            </label>

            <button id="checkout-submit" type="submit" class="w-full bg-dark-brown text-white py-4 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 shadow-sm mt-8 disabled:opacity-60">
                Continuă spre plata securizată
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('checkout-form')?.addEventListener('submit', function () {
        const button = document.getElementById('checkout-submit');
        button.disabled = true;
        button.textContent = 'Se deschide plata securizată...';
    });
</script>
@endsection
