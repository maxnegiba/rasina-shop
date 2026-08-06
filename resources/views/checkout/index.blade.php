@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
    <div class="mb-12 text-center">
        <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-4">Finalizare Comandă</span>
        <h1 class="font-serif text-3xl md:text-4xl text-smoked-black mb-6">Plată Securizată</h1>
        <p class="text-sm font-light text-smoked-black/60 tracking-wide">
            Introduceți datele de livrare și datele cardului pentru a finaliza comanda în valoare de {{ number_format($totalAmount, 2, ',', '.') }} RON.
        </p>
    </div>

    <div class="bg-white p-8 border border-smoked-black/10 shadow-sm">
        <form id="payment-form" class="space-y-6">
            <h2 class="font-serif text-xl text-smoked-black border-b border-black/10 pb-2 mb-4">Date de Contact</h2>

            <div>
                <label for="customer-email" class="block text-xs uppercase tracking-wider text-smoked-black/60 mb-2">Email</label>
                <input
                    id="customer-email"
                    type="email"
                    autocomplete="email"
                    required
                    class="w-full border border-smoked-black/20 px-4 py-3 text-smoked-black focus:border-vintage-gold focus:ring-vintage-gold"
                    placeholder="nume@exemplu.ro"
                >
                <p class="mt-2 text-xs text-smoked-black/50">Confirmarea comenzii și proforma vor fi trimise la această adresă.</p>
            </div>

            <h2 class="font-serif text-xl text-smoked-black border-b border-black/10 pb-2 mb-4 mt-8">Adresa de Livrare</h2>
            <div id="address-element" class="mb-6"></div>

            <h2 class="font-serif text-xl text-smoked-black border-b border-black/10 pb-2 mb-4 mt-8">Date de Plată</h2>
            <div id="payment-element"></div>

            <div class="border-t border-black/10 pt-6 space-y-4">
                <label class="flex items-start gap-3 text-sm text-dark-brown/75">
                    <input id="accept-terms" type="checkbox" required class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                    <span>
                        Accept <a href="{{ route('page.show', 'termeni-si-conditii') }}" target="_blank" rel="noopener" class="text-vintage-gold underline">Termenii și Condițiile</a>, inclusiv politica de livrare și retragere.
                    </span>
                </label>

                <label class="flex items-start gap-3 text-sm text-dark-brown/75">
                    <input id="acknowledge-privacy" type="checkbox" required class="mt-1 rounded border-black/20 text-vintage-gold focus:ring-vintage-gold">
                    <span>
                        Confirm că am citit <a href="{{ route('page.show', 'politica-de-confidentialitate') }}" target="_blank" rel="noopener" class="text-vintage-gold underline">Politica de Confidențialitate</a>.
                    </span>
                </label>
            </div>

            <div id="error-message" role="alert" class="text-red-500 text-sm hidden mt-4"></div>

            <button id="submit" type="submit" class="w-full bg-dark-brown text-white py-4 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 shadow-sm mt-8 flex justify-center items-center disabled:opacity-60">
                <span id="button-text">Plătește {{ number_format($totalAmount, 2, ',', '.') }} RON</span>
                <span id="spinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </form>

        <form method="POST" action="{{ route('checkout.cancel') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-xs text-smoked-black/50 underline hover:text-smoked-black">
                Renunță și revino la coș
            </button>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe(@json($stripeKey));
    const elements = stripe.elements({
        clientSecret: @json($clientSecret),
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#D4AF37',
                colorBackground: '#ffffff',
                colorText: '#2C1E16',
                colorDanger: '#df1b41',
                fontFamily: 'Montserrat, sans-serif',
                spacingUnit: '4px',
                borderRadius: '0px',
            }
        },
    });

    const addressElement = elements.create('address', {
        mode: 'shipping',
        allowedCountries: ['RO'],
        fields: {
            phone: 'always',
        },
        validation: {
            phone: {
                required: 'always',
            },
        },
    });
    addressElement.mount('#address-element');

    const paymentElement = elements.create('payment', {
        layout: 'tabs',
        fields: {
            billingDetails: {
                email: 'never',
            },
        },
    });
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitBtn = document.getElementById('submit');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const errorMessage = document.getElementById('error-message');

    const showError = (message) => {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
    };

    const setLoading = (loading) => {
        submitBtn.disabled = loading;
        spinner.classList.toggle('hidden', !loading);
        buttonText.textContent = loading
            ? 'Se procesează...'
            : 'Plătește {{ number_format($totalAmount, 2, ',', '.') }} RON';
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        errorMessage.classList.add('hidden');

        if (!form.reportValidity()) {
            return;
        }

        setLoading(true);

        try {
            const acceptanceResponse = await fetch(@json(route('checkout.accept-terms')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    order_token: @json($orderToken),
                    accept_terms: document.getElementById('accept-terms').checked,
                    acknowledge_privacy: document.getElementById('acknowledge-privacy').checked,
                }),
            });

            const acceptance = await acceptanceResponse.json();

            if (!acceptanceResponse.ok) {
                const validationMessage = acceptance.errors
                    ? Object.values(acceptance.errors).flat()[0]
                    : acceptance.message;
                throw new Error(validationMessage || 'Acceptarea condițiilor nu a putut fi salvată.');
            }

            const email = document.getElementById('customer-email').value.trim();
            const {error} = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: @json(route('checkout.success')),
                    receipt_email: email,
                    payment_method_data: {
                        billing_details: { email },
                    },
                },
            });

            if (error) {
                throw error;
            }
        } catch (error) {
            showError(error.message || 'Plata nu a putut fi procesată. Încercați din nou.');
            setLoading(false);
        }
    });
</script>
@endsection
