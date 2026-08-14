@extends('layouts.app')

@php
    $cardButtonLabel = 'Plătește '.number_format($totalAmount, 2, ',', '.').' RON';
    $codButtonLabel = 'Plasează comanda cu ramburs';
@endphp

@section('content')
<div class="bg-ivory min-h-screen py-12 md:py-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav aria-label="Pașii comenzii" class="mb-8 md:mb-12">
            <ol class="flex items-center justify-center gap-2 sm:gap-4 text-[9px] sm:text-[10px] uppercase tracking-[0.12em] sm:tracking-[0.18em]">
                <li class="text-dark-brown/45">1. Coș</li>
                <li class="w-6 sm:w-10 h-px bg-vintage-gold/40"></li>
                <li class="font-semibold text-vintage-gold" aria-current="step">2. Livrare și plată</li>
                <li class="w-6 sm:w-10 h-px bg-black/10"></li>
                <li class="text-dark-brown/35">3. Confirmare</li>
            </ol>
        </nav>

        <div class="mb-9 text-center">
            <span class="block text-vintage-gold tracking-[0.26em] text-[10px] font-semibold uppercase mb-3">Finalizare comandă</span>
            <h1 class="font-serif text-3xl md:text-5xl text-dark-brown mb-4">Finalizați comanda în siguranță</h1>
            <p class="text-sm font-light text-dark-brown/60 max-w-2xl mx-auto leading-relaxed">
                Completați datele de livrare și alegeți plata online sau ramburs, la curier.
            </p>
        </div>

        <div class="grid lg:grid-cols-[minmax(0,1fr)_360px] gap-7 lg:gap-10 items-start">
            <section class="bg-white p-5 sm:p-8 border border-black/10 shadow-sm">
                <form id="payment-form" class="space-y-8" novalidate>
                    <div>
                        <h2 class="font-serif text-2xl text-dark-brown border-b border-black/10 pb-3 mb-5">Date de contact</h2>
                        <label for="customer-email" class="block text-[10px] uppercase tracking-[0.14em] text-dark-brown/60 mb-2">Adresă de email</label>
                        <input id="customer-email" type="email" inputmode="email" autocomplete="email" required
                            aria-describedby="email-error"
                            class="w-full border border-black/20 px-4 py-3.5 text-dark-brown focus:border-vintage-gold focus:ring-vintage-gold"
                            placeholder="nume@exemplu.ro">
                        <p class="mt-2 text-xs text-dark-brown/50">Veți primi confirmarea comenzii la această adresă.</p>
                        <p id="email-error" role="alert" class="mt-2 text-xs text-red-700 hidden"></p>
                    </div>

                    <div>
                        <h2 class="font-serif text-2xl text-dark-brown border-b border-black/10 pb-3 mb-5">Adresa de livrare</h2>
                        <div id="address-element" aria-label="Formular adresă de livrare"></div>
                        <p class="mt-3 text-xs text-dark-brown/50">Livrăm în România. Numărul de telefon este necesar pentru curier.</p>
                    </div>

                    <fieldset>
                        <legend class="font-serif text-2xl text-dark-brown border-b border-black/10 pb-3 mb-5 w-full">Metoda de plată</legend>
                        <div class="grid sm:grid-cols-2 gap-3">
                            <label class="relative flex gap-3 border border-vintage-gold bg-vintage-gold/[0.06] p-4 cursor-pointer transition" data-payment-card="card">
                                <input type="radio" name="payment_method" value="card" checked class="mt-1 text-vintage-gold focus:ring-vintage-gold">
                                <span>
                                    <strong class="block text-sm text-dark-brown">Card online</strong>
                                    <span class="block mt-1 text-xs text-dark-brown/55">Card, Apple Pay sau Google Pay, prin Stripe.</span>
                                </span>
                            </label>
                            <label class="relative flex gap-3 border border-black/15 p-4 cursor-pointer transition" data-payment-card="cash_on_delivery">
                                <input type="radio" name="payment_method" value="cash_on_delivery" class="mt-1 text-vintage-gold focus:ring-vintage-gold">
                                <span>
                                    <strong class="block text-sm text-dark-brown">Ramburs</strong>
                                    <span class="block mt-1 text-xs text-dark-brown/55">Plătiți comanda curierului în momentul livrării.</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div id="card-payment-panel">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <p class="text-xs text-dark-brown/55">Plată online securizată</p>
                            <span class="text-[9px] uppercase tracking-[0.1em] text-emerald-700">Securizat de Stripe</span>
                        </div>
                        <div id="payment-loading" class="space-y-3" aria-live="polite">
                            <div class="h-12 bg-black/[0.04] animate-pulse"></div>
                            <div class="h-24 bg-black/[0.04] animate-pulse"></div>
                            <p class="text-xs text-dark-brown/50">Se încarcă metodele de plată disponibile…</p>
                        </div>
                        <div id="payment-element" class="hidden"></div>
                    </div>

                    <div id="cod-note" class="hidden border border-vintage-gold/30 bg-vintage-gold/[0.06] p-4 text-sm text-dark-brown/70 leading-relaxed">
                        Ați ales <strong class="text-dark-brown">plata ramburs</strong>. Nu veți introduce date de card; suma comenzii se achită curierului la livrare.
                    </div>

                    <fieldset class="border-t border-black/10 pt-6 space-y-4">
                        <legend class="sr-only">Confirmări obligatorii</legend>
                        <div>
                            <label class="flex items-start gap-3 text-sm text-dark-brown/75 cursor-pointer">
                                <input id="accept-terms" type="checkbox" required aria-describedby="terms-error" class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                                <span>Accept <a href="{{ route('page.show', 'termeni-si-conditii') }}" target="_blank" rel="noopener" class="text-vintage-gold underline">Termenii și Condițiile</a>, inclusiv condițiile de livrare și <a href="{{ route('page.show', 'politica-de-retur') }}" target="_blank" rel="noopener" class="text-vintage-gold underline">politica de retur</a>.</span>
                            </label>
                            <p id="terms-error" role="alert" class="mt-2 ml-7 text-xs text-red-700 hidden"></p>
                        </div>
                        <div>
                            <label class="flex items-start gap-3 text-sm text-dark-brown/75 cursor-pointer">
                                <input id="acknowledge-privacy" type="checkbox" required aria-describedby="privacy-error" class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                                <span>Confirm că am citit <a href="{{ route('page.show', 'politica-de-confidentialitate') }}" target="_blank" rel="noopener" class="text-vintage-gold underline">Politica de Confidențialitate</a>.</span>
                            </label>
                            <p id="privacy-error" role="alert" class="mt-2 ml-7 text-xs text-red-700 hidden"></p>
                        </div>
                    </fieldset>

                    <div id="error-message" role="alert" aria-live="assertive" class="text-red-800 text-sm hidden bg-red-50 border border-red-200 p-4"></div>

                    <button id="submit" type="submit" class="w-full min-h-14 bg-dark-brown text-white px-5 py-4 uppercase tracking-[0.16em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors shadow-sm flex justify-center items-center disabled:opacity-60 disabled:cursor-wait">
                        <span id="button-text">{{ $cardButtonLabel }}</span>
                        <span id="spinner" class="hidden ml-3" aria-hidden="true">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </span>
                    </button>
                    <p id="submit-help" class="text-center text-[10px] text-dark-brown/45 leading-relaxed">După confirmarea plății veți vedea imediat starea comenzii.</p>
                </form>

                <form method="POST" action="{{ route('checkout.cancel') }}" class="mt-5 text-center">
                    @csrf
                    <button type="submit" class="text-xs text-dark-brown/55 underline hover:text-dark-brown">Renunță și revino la coș</button>
                </form>
            </section>

            <aside class="bg-white p-5 sm:p-6 border border-black/10 shadow-sm lg:sticky lg:top-32" aria-labelledby="order-summary-heading">
                <div class="flex justify-between items-center gap-4 mb-5">
                    <h2 id="order-summary-heading" class="font-serif text-2xl text-dark-brown">Sumar comandă</h2>
                    <form method="POST" action="{{ route('checkout.cancel') }}">@csrf<button type="submit" class="text-[10px] underline text-dark-brown/55 hover:text-vintage-gold">Modifică</button></form>
                </div>
                <div class="space-y-4 max-h-64 overflow-y-auto pr-1">
                    @foreach($order->items as $item)
                        <div class="flex justify-between gap-4 text-xs">
                            <div class="min-w-0"><p class="text-dark-brown line-clamp-2">{{ $item->displayName() }}</p><p class="mt-1 text-dark-brown/45">{{ $item->quantity }} × {{ number_format($item->unit_price, 2, ',', '.') }} RON</p></div>
                            <p class="font-medium text-dark-brown whitespace-nowrap">{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} RON</p>
                        </div>
                    @endforeach
                </div>
                <dl class="mt-6 pt-5 border-t border-black/10 space-y-3 text-xs text-dark-brown/65">
                    <div class="flex justify-between gap-4"><dt>Subtotal</dt><dd>{{ number_format($order->subtotal_amount, 2, ',', '.') }} RON</dd></div>
                    @if((float) $order->discount_amount > 0)<div class="flex justify-between gap-4 text-emerald-700"><dt>Reducere</dt><dd>−{{ number_format($order->discount_amount, 2, ',', '.') }} RON</dd></div>@endif
                    <div class="flex justify-between gap-4 pt-4 border-t border-black/10 text-sm text-dark-brown font-semibold"><dt>Total</dt><dd>{{ number_format($order->total_amount, 2, ',', '.') }} RON</dd></div>
                </dl>
                <p class="mt-3 text-[10px] text-dark-brown/55">Livrarea nu este inclusă în preț.</p>
                <div class="mt-7 pt-5 border-t border-black/5 space-y-3 text-[10px] text-dark-brown/55 leading-relaxed">
                    <p>✓ Puteți plăti online securizat sau ramburs la curier.</p>
                    <p>✓ La plata cu cardul, datele complete ale cardului nu sunt stocate de MTD Art.</p>
                    <p>✓ Pentru ramburs, produsele rămân rezervate după plasarea comenzii.</p>
                    <p>✓ Pentru ajutor: <a href="mailto:{{ config('shop.legal.email') }}" class="underline hover:text-vintage-gold">{{ config('shop.legal.email') }}</a></p>
                </div>
            </aside>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
(() => {
    const stripe = Stripe({{ Illuminate\Support\Js::from($stripeKey) }}, {locale: 'ro'});
    const elements = stripe.elements({
        mode: 'payment', amount: {{ Illuminate\Support\Js::from($totalAmountCents) }}, currency: 'ron', locale: 'ro',
        appearance: {theme: 'stripe', variables: {colorPrimary: '#CFB53B', colorBackground: '#ffffff', colorText: '#2C1E16', colorDanger: '#b42318', fontFamily: 'Manrope, ui-sans-serif, system-ui, sans-serif', spacingUnit: '4px', borderRadius: '0px'}}
    });

    const addressElement = elements.create('address', {mode: 'shipping', allowedCountries: ['RO'], fields: {phone: 'always'}, validation: {phone: {required: 'always'}}});
    addressElement.mount('#address-element');
    const paymentElement = elements.create('payment', {layout: {type: 'tabs', defaultCollapsed: false}, wallets: {applePay: 'auto', googlePay: 'auto', link: 'auto'}, fields: {billingDetails: {email: 'never'}}});
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const submitHelp = document.getElementById('submit-help');
    const errorMessage = document.getElementById('error-message');
    const emailInput = document.getElementById('customer-email');
    const termsInput = document.getElementById('accept-terms');
    const privacyInput = document.getElementById('acknowledge-privacy');
    const cardPanel = document.getElementById('card-payment-panel');
    const codNote = document.getElementById('cod-note');
    const cardButtonLabel = {{ Illuminate\Support\Js::from($cardButtonLabel) }};
    const codButtonLabel = {{ Illuminate\Support\Js::from($codButtonLabel) }};
    let isSubmitting = false;

    const selectedMethod = () => document.querySelector('input[name="payment_method"]:checked')?.value || 'card';

    try { emailInput.value = window.sessionStorage.getItem('mtd-checkout-email') || ''; } catch (_) {}
    emailInput.addEventListener('input', () => { try { window.sessionStorage.setItem('mtd-checkout-email', emailInput.value.trim()); } catch (_) {} });

    paymentElement.on('ready', () => {
        document.getElementById('payment-loading')?.classList.add('hidden');
        document.getElementById('payment-element')?.classList.remove('hidden');
    });
    paymentElement.on('loaderror', () => showGlobalError('Metodele de plată online nu s-au încărcat. Puteți alege Ramburs sau reîncărca pagina.'));

    document.querySelectorAll('input[name="payment_method"]').forEach((radio) => radio.addEventListener('change', updatePaymentMode));

    function updatePaymentMode() {
        const cod = selectedMethod() === 'cash_on_delivery';
        cardPanel.classList.toggle('hidden', cod);
        codNote.classList.toggle('hidden', !cod);
        buttonText.textContent = cod ? codButtonLabel : cardButtonLabel;
        submitHelp.textContent = cod ? 'Comanda va fi înregistrată fără debitarea unui card.' : 'După confirmarea plății veți vedea imediat starea comenzii.';
        document.querySelectorAll('[data-payment-card]').forEach((card) => {
            const active = card.dataset.paymentCard === selectedMethod();
            card.classList.toggle('border-vintage-gold', active);
            card.classList.toggle('bg-vintage-gold/[0.06]', active);
            card.classList.toggle('border-black/15', !active);
        });
    }

    function clearErrors() {
        errorMessage.classList.add('hidden');
        ['email-error', 'terms-error', 'privacy-error'].forEach((id) => { const el = document.getElementById(id); el.textContent = ''; el.classList.add('hidden'); });
        [emailInput, termsInput, privacyInput].forEach((el) => el.removeAttribute('aria-invalid'));
    }
    function fieldError(input, id, message) { input.setAttribute('aria-invalid', 'true'); const el = document.getElementById(id); el.textContent = message; el.classList.remove('hidden'); }
    function showGlobalError(message) { errorMessage.textContent = message; errorMessage.classList.remove('hidden'); errorMessage.scrollIntoView({behavior: 'smooth', block: 'center'}); }
    function setLoading(loading) { isSubmitting = loading; submitButton.disabled = loading; spinner.classList.toggle('hidden', !loading); buttonText.textContent = loading ? (selectedMethod() === 'cash_on_delivery' ? 'Se plasează comanda…' : 'Se procesează plata…') : (selectedMethod() === 'cash_on_delivery' ? codButtonLabel : cardButtonLabel); }

    function validateCustomerFields() {
        let valid = true;
        if (!emailInput.value.trim() || !emailInput.checkValidity()) { fieldError(emailInput, 'email-error', 'Introduceți o adresă de email validă.'); valid = false; }
        if (!termsInput.checked) { fieldError(termsInput, 'terms-error', 'Trebuie să acceptați Termenii și Condițiile.'); valid = false; }
        if (!privacyInput.checked) { fieldError(privacyInput, 'privacy-error', 'Trebuie să confirmați că ați citit Politica de Confidențialitate.'); valid = false; }
        return valid;
    }

    async function createPaymentIntentAfterAcceptance() {
        const response = await fetch({{ Illuminate\Support\Js::from(route('checkout.accept-terms')) }}, {method: 'POST', headers: {Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': {{ Illuminate\Support\Js::from(csrf_token()) }}}, credentials: 'same-origin', body: JSON.stringify({order_token: {{ Illuminate\Support\Js::from($orderToken) }}, accept_terms: termsInput.checked, acknowledge_privacy: privacyInput.checked})});
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(payload.message || 'Confirmările obligatorii nu au putut fi salvate.');
        if (!payload.client_secret) throw new Error('Sesiunea securizată de plată nu a putut fi creată.');
        return payload.client_secret;
    }

    async function placeCashOnDelivery() {
        const addressResult = await addressElement.getValue();
        if (!addressResult.complete) throw new Error('Completați toate datele obligatorii pentru livrare.');
        const value = addressResult.value || {};
        const response = await fetch({{ Illuminate\Support\Js::from(route('checkout.cash-on-delivery')) }}, {
            method: 'POST', headers: {Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': {{ Illuminate\Support\Js::from(csrf_token()) }}}, credentials: 'same-origin',
            body: JSON.stringify({order_token: {{ Illuminate\Support\Js::from($orderToken) }}, email: emailInput.value.trim(), name: value.name, phone: value.phone, address: value.address, accept_terms: termsInput.checked, acknowledge_privacy: privacyInput.checked})
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            const validationMessage = payload.errors ? Object.values(payload.errors).flat()[0] : null;
            throw new Error(validationMessage || payload.message || 'Comanda cu ramburs nu a putut fi plasată.');
        }
        if (!payload.redirect_url) throw new Error('Comanda a fost înregistrată, dar redirecționarea nu a putut fi pregătită.');
        window.location.assign(payload.redirect_url);
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (isSubmitting) return;
        clearErrors();
        if (!validateCustomerFields()) { document.querySelector('[aria-invalid="true"]')?.focus(); return; }
        setLoading(true);
        try {
            if (selectedMethod() === 'cash_on_delivery') {
                await placeCashOnDelivery();
                return;
            }
            const submitResult = await elements.submit();
            if (submitResult.error) throw submitResult.error;
            const clientSecret = await createPaymentIntentAfterAcceptance();
            const email = emailInput.value.trim();
            const {error} = await stripe.confirmPayment({elements, clientSecret, confirmParams: {return_url: {{ Illuminate\Support\Js::from(route('checkout.success', ['order' => $orderToken])) }}, receipt_email: email, payment_method_data: {billing_details: {email}}}});
            if (error) throw error;
        } catch (error) {
            showGlobalError(error?.message || 'Operațiunea nu a putut fi finalizată. Verificați datele și încercați din nou.');
            setLoading(false);
        }
    });

    updatePaymentMode();
})();
</script>
@endsection
