@extends('layouts.app')

@php
    $paymentButtonLabel = 'Plătește '.number_format($totalAmount, 2, ',', '.').' RON';
@endphp

@section('content')
<div class="bg-ivory min-h-screen py-12 md:py-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav aria-label="Pașii comenzii" class="mb-8 md:mb-12">
            <ol class="flex items-center justify-center gap-2 sm:gap-4 text-[9px] sm:text-[10px] uppercase tracking-[0.12em] sm:tracking-[0.18em]">
                <li class="text-dark-brown/45">1. Coș</li>
                <li class="w-6 sm:w-10 h-px bg-vintage-gold/40" aria-hidden="true"></li>
                <li class="font-semibold text-vintage-gold" aria-current="step">2. Livrare și plată</li>
                <li class="w-6 sm:w-10 h-px bg-black/10" aria-hidden="true"></li>
                <li class="text-dark-brown/35">3. Confirmare</li>
            </ol>
        </nav>

        <div class="mb-9 text-center">
            <span class="block text-vintage-gold tracking-[0.26em] text-[10px] font-semibold uppercase mb-3">Finalizare comandă</span>
            <h1 class="font-serif text-3xl md:text-5xl text-dark-brown mb-4">Finalizați comanda în siguranță</h1>
            <p class="text-sm font-light text-dark-brown/60 max-w-2xl mx-auto leading-relaxed">
                Completați datele de livrare, alegeți metoda de plată preferată și verificați totalul comenzii înainte de confirmare.
            </p>
        </div>

        <div class="grid lg:grid-cols-[minmax(0,1fr)_360px] gap-7 lg:gap-10 items-start">
            <section class="bg-white p-5 sm:p-8 border border-black/10 shadow-sm" aria-labelledby="payment-heading">
                <form id="payment-form" class="space-y-8" novalidate>
                    <div>
                        <h2 id="payment-heading" class="font-serif text-2xl text-dark-brown border-b border-black/10 pb-3 mb-5">Date de contact</h2>
                        <label for="customer-email" class="block text-[10px] uppercase tracking-[0.14em] text-dark-brown/60 mb-2">Adresă de email</label>
                        <input
                            id="customer-email"
                            type="email"
                            inputmode="email"
                            autocomplete="email"
                            required
                            aria-describedby="email-help email-error"
                            class="w-full border border-black/20 px-4 py-3.5 text-dark-brown focus:border-vintage-gold focus:ring-vintage-gold"
                            placeholder="nume@exemplu.ro"
                        >
                        <p id="email-help" class="mt-2 text-xs text-dark-brown/50">Veți primi pe această adresă confirmarea comenzii și documentul proforma.</p>
                        <p id="email-error" role="alert" class="mt-2 text-xs text-red-700 hidden"></p>
                    </div>

                    <div>
                        <h2 class="font-serif text-2xl text-dark-brown border-b border-black/10 pb-3 mb-5">Adresa de livrare</h2>
                        <div id="address-element" aria-label="Formular adresă de livrare"></div>
                        <p class="mt-3 text-xs text-dark-brown/50">Livrăm în România. Numărul de telefon este necesar pentru curier.</p>
                    </div>

                    <div>
                        <div class="flex items-start justify-between gap-4 border-b border-black/10 pb-3 mb-5">
                            <div>
                                <h2 class="font-serif text-2xl text-dark-brown">Metoda de plată</h2>
                                <p class="mt-1 text-xs text-dark-brown/50">Alegeți metoda de plată preferată dintre opțiunile disponibile.</p>
                            </div>
                            <span class="flex items-center gap-1.5 text-[9px] uppercase tracking-[0.1em] text-emerald-700 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Securizat
                            </span>
                        </div>
                        <div id="payment-loading" class="space-y-3" aria-live="polite">
                            <div class="h-12 bg-black/[0.04] animate-pulse"></div>
                            <div class="h-24 bg-black/[0.04] animate-pulse"></div>
                            <p class="text-xs text-dark-brown/50">Se încarcă metodele de plată disponibile…</p>
                        </div>
                        <div id="payment-element" class="hidden"></div>
                    </div>

                    <fieldset class="border-t border-black/10 pt-6 space-y-4">
                        <legend class="sr-only">Confirmări obligatorii</legend>
                        <div>
                            <label class="flex items-start gap-3 text-sm text-dark-brown/75 cursor-pointer">
                                <input id="accept-terms" type="checkbox" required aria-describedby="terms-error" class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                                <span>
                                    Accept <a href="{{ route('page.show', 'termeni-si-conditii') }}" target="_blank" rel="noopener" class="text-vintage-gold underline hover:text-dark-brown">Termenii și Condițiile</a>, inclusiv condițiile de livrare și <a href="{{ route('page.show', 'politica-de-retur') }}" target="_blank" rel="noopener" class="text-vintage-gold underline hover:text-dark-brown">politica de retur</a>.
                                </span>
                            </label>
                            <p id="terms-error" role="alert" class="mt-2 ml-7 text-xs text-red-700 hidden"></p>
                        </div>

                        <div>
                            <label class="flex items-start gap-3 text-sm text-dark-brown/75 cursor-pointer">
                                <input id="acknowledge-privacy" type="checkbox" required aria-describedby="privacy-error" class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                                <span>
                                    Confirm că am citit <a href="{{ route('page.show', 'politica-de-confidentialitate') }}" target="_blank" rel="noopener" class="text-vintage-gold underline hover:text-dark-brown">Politica de Confidențialitate</a>.
                                </span>
                            </label>
                            <p id="privacy-error" role="alert" class="mt-2 ml-7 text-xs text-red-700 hidden"></p>
                        </div>
                    </fieldset>

                    <div id="error-message" role="alert" aria-live="assertive" class="text-red-800 text-sm hidden bg-red-50 border border-red-200 p-4"></div>

                    <button id="submit" type="submit" class="w-full min-h-14 bg-dark-brown text-white px-5 py-4 uppercase tracking-[0.16em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors shadow-sm flex justify-center items-center disabled:opacity-60 disabled:cursor-wait">
                        <span id="button-text">{{ $paymentButtonLabel }}</span>
                        <span id="spinner" class="hidden ml-3" aria-hidden="true">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                    <p class="text-center text-[10px] text-dark-brown/45 leading-relaxed">După confirmarea plății, veți vedea imediat starea comenzii.</p>
                </form>

                <form method="POST" action="{{ route('checkout.cancel') }}" class="mt-5 text-center">
                    @csrf
                    <button type="submit" class="text-xs text-dark-brown/55 underline hover:text-dark-brown">Renunță la plată și revino la coș</button>
                </form>
            </section>

            <aside class="bg-white p-5 sm:p-6 border border-black/10 shadow-sm lg:sticky lg:top-32" aria-labelledby="order-summary-heading">
                <div class="flex justify-between items-center gap-4 mb-5">
                    <h2 id="order-summary-heading" class="font-serif text-2xl text-dark-brown">Sumar comandă</h2>
                    <form method="POST" action="{{ route('checkout.cancel') }}">
                        @csrf
                        <button type="submit" class="text-[10px] underline text-dark-brown/55 hover:text-vintage-gold">Modifică</button>
                    </form>
                </div>

                <div class="space-y-4 max-h-64 overflow-y-auto pr-1">
                    @foreach($order->items as $item)
                        <div class="flex justify-between gap-4 text-xs">
                            <div class="min-w-0">
                                <p class="text-dark-brown line-clamp-2">{{ $item->displayName() }}</p>
                                <p class="mt-1 text-dark-brown/45">{{ $item->quantity }} × {{ number_format($item->unit_price, 2, ',', '.') }} RON</p>
                            </div>
                            <p class="font-medium text-dark-brown whitespace-nowrap">{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} RON</p>
                        </div>
                    @endforeach
                </div>

                <dl class="mt-6 pt-5 border-t border-black/10 space-y-3 text-xs text-dark-brown/65">
                    <div class="flex justify-between gap-4"><dt>Subtotal</dt><dd>{{ number_format($order->subtotal_amount, 2, ',', '.') }} RON</dd></div>
                    @if((float) $order->discount_amount > 0)
                        <div class="flex justify-between gap-4 text-emerald-700"><dt>Reducere</dt><dd>−{{ number_format($order->discount_amount, 2, ',', '.') }} RON</dd></div>
                    @endif
                    <div class="flex justify-between gap-4 pt-4 border-t border-black/10 text-sm text-dark-brown font-semibold"><dt>Total de plată</dt><dd>{{ number_format($order->total_amount, 2, ',', '.') }} RON</dd></div>
                </dl>
                <p class="mt-3 text-[10px] text-dark-brown/55 leading-relaxed">Livrarea nu este inclusă în preț.</p>

                <div class="mt-7 pt-5 border-t border-black/5 space-y-3 text-[10px] text-dark-brown/55 leading-relaxed">
                    <p>✓ Plata este procesată securizat de Stripe; datele complete ale cardului nu sunt stocate de MTD Art.</p>
                    <p>✓ Produsele sunt rezervate pentru aproximativ {{ config('shop.checkout_reservation_minutes') }} de minute cât finalizați comanda.</p>
                    <p>✓ Confirmarea comenzii și documentul proforma sunt trimise pe email după validarea plății.</p>
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
        mode: 'payment',
        amount: {{ Illuminate\Support\Js::from($totalAmountCents) }},
        currency: 'ron',
        locale: 'ro',
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#CFB53B',
                colorBackground: '#ffffff',
                colorText: '#2C1E16',
                colorDanger: '#b42318',
                fontFamily: 'Manrope, ui-sans-serif, system-ui, sans-serif',
                spacingUnit: '4px',
                borderRadius: '0px',
            },
        },
    });

    const addressElement = elements.create('address', {
        mode: 'shipping',
        allowedCountries: ['RO'],
        fields: {phone: 'always'},
        validation: {phone: {required: 'always'}},
    });
    addressElement.mount('#address-element');

    const paymentElement = elements.create('payment', {
        layout: {type: 'tabs', defaultCollapsed: false},
        wallets: {
            applePay: 'auto',
            googlePay: 'auto',
            link: 'auto',
        },
        fields: {
            billingDetails: {email: 'never'},
        },
    });
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const errorMessage = document.getElementById('error-message');
    const emailInput = document.getElementById('customer-email');
    const termsInput = document.getElementById('accept-terms');
    const privacyInput = document.getElementById('acknowledge-privacy');
    const storageKey = 'mtd-checkout-email';
    const paymentButtonLabel = {{ Illuminate\Support\Js::from($paymentButtonLabel) }};
    let isSubmitting = false;

    try {
        emailInput.value = window.sessionStorage.getItem(storageKey) || '';
    } catch (_) {
        // Checkout remains fully usable when browser storage is unavailable.
    }

    emailInput.addEventListener('input', () => {
        try {
            window.sessionStorage.setItem(storageKey, emailInput.value.trim());
        } catch (_) {}
    });

    paymentElement.on('ready', () => {
        document.getElementById('payment-loading')?.classList.add('hidden');
        document.getElementById('payment-element')?.classList.remove('hidden');
    });

    paymentElement.on('loaderror', () => {
        showGlobalError('Metodele de plată nu s-au încărcat. Verificați conexiunea și reîncărcați pagina.');
    });

    function clearErrors() {
        errorMessage.classList.add('hidden');
        ['email-error', 'terms-error', 'privacy-error'].forEach((id) => {
            const element = document.getElementById(id);
            element.textContent = '';
            element.classList.add('hidden');
        });
        [emailInput, termsInput, privacyInput].forEach((element) => element.removeAttribute('aria-invalid'));
    }

    function fieldError(input, errorId, message) {
        input.setAttribute('aria-invalid', 'true');
        const element = document.getElementById(errorId);
        element.textContent = message;
        element.classList.remove('hidden');
    }

    function showGlobalError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
        errorMessage.scrollIntoView({behavior: 'smooth', block: 'center'});
    }

    function setLoading(loading) {
        isSubmitting = loading;
        submitButton.disabled = loading;
        spinner.classList.toggle('hidden', !loading);
        buttonText.textContent = loading
            ? 'Se procesează plata…'
            : paymentButtonLabel;
    }

    function paymentErrorMessage(error) {
        const messages = {
            card_declined: 'Cardul a fost refuzat. Încercați un alt card sau contactați banca.',
            expired_card: 'Cardul este expirat. Folosiți un alt card.',
            incorrect_cvc: 'Codul de securitate al cardului este incorect.',
            processing_error: 'Plata nu a putut fi procesată momentan. Încercați din nou.',
            incomplete_number: 'Numărul cardului este incomplet.',
            incomplete_expiry: 'Data expirării este incompletă.',
            incomplete_cvc: 'Codul de securitate este incomplet.',
        };

        return messages[error?.code]
            || (error?.type === 'validation_error' ? error.message : null)
            || error?.message
            || 'Plata nu a putut fi procesată. Verificați datele și încercați din nou.';
    }

    function validateCustomerFields() {
        let valid = true;
        const email = emailInput.value.trim();

        if (!email || !emailInput.checkValidity()) {
            fieldError(emailInput, 'email-error', 'Introduceți o adresă de email validă.');
            valid = false;
        }
        if (!termsInput.checked) {
            fieldError(termsInput, 'terms-error', 'Trebuie să acceptați Termenii și Condițiile.');
            valid = false;
        }
        if (!privacyInput.checked) {
            fieldError(privacyInput, 'privacy-error', 'Trebuie să confirmați că ați citit Politica de Confidențialitate.');
            valid = false;
        }

        return valid;
    }

    async function createPaymentIntentAfterAcceptance() {
        const response = await fetch({{ Illuminate\Support\Js::from(route('checkout.accept-terms')) }}, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': {{ Illuminate\Support\Js::from(csrf_token()) }},
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                order_token: {{ Illuminate\Support\Js::from($orderToken) }},
                accept_terms: termsInput.checked,
                acknowledge_privacy: privacyInput.checked,
            }),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (payload.errors?.accept_terms) {
                fieldError(termsInput, 'terms-error', payload.errors.accept_terms[0]);
            }
            if (payload.errors?.acknowledge_privacy) {
                fieldError(privacyInput, 'privacy-error', payload.errors.acknowledge_privacy[0]);
            }
            throw new Error(payload.message || 'Confirmările obligatorii nu au putut fi salvate.');
        }

        if (!payload.client_secret) {
            throw new Error('Sesiunea securizată de plată nu a putut fi creată.');
        }

        return payload.client_secret;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (isSubmitting) {
            return;
        }

        clearErrors();

        if (!validateCustomerFields()) {
            document.querySelector('[aria-invalid="true"]')?.focus();
            return;
        }

        setLoading(true);

        try {
            const submitResult = await elements.submit();

            if (submitResult.error) {
                throw submitResult.error;
            }

            const clientSecret = await createPaymentIntentAfterAcceptance();
            const email = emailInput.value.trim();
            const {error} = await stripe.confirmPayment({
                elements,
                clientSecret,
                confirmParams: {
                    return_url: {{ Illuminate\Support\Js::from(route('checkout.success', ['order' => $orderToken])) }},
                    receipt_email: email,
                    payment_method_data: {
                        billing_details: {email},
                    },
                },
            });

            if (error) {
                throw error;
            }
        } catch (error) {
            showGlobalError(paymentErrorMessage(error));
            setLoading(false);
        }
    });
})();
</script>
@endsection