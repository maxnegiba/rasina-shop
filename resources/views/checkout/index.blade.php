@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
    <div class="mb-12 text-center">
        <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-4">Finalizare Comandă</span>
        <h1 class="font-serif text-3xl md:text-4xl text-smoked-black mb-6">Plată Securizată</h1>
        <p class="text-sm font-light text-smoked-black/60 tracking-wide">Introduceți datele cardului pentru a finaliza comanda în valoare de {{ number_format($totalAmount, 2, ',', '.') }} RON.</p>
    </div>

    <div class="bg-white p-8 border border-smoked-black/10 shadow-sm">
        <form id="payment-form" class="space-y-6">
            <h2 class="font-serif text-xl text-smoked-black border-b border-black/10 pb-2 mb-4">Adresa de Livrare</h2>
            <div id="address-element" class="mb-6">
                <!-- Elements will create address elements here -->
            </div>

            <h2 class="font-serif text-xl text-smoked-black border-b border-black/10 pb-2 mb-4 mt-8">Date de Plată</h2>
            <div id="payment-element">
                <!-- Elements will create form elements here -->
            </div>

            <!-- Used to display form errors. -->
            <div id="error-message" class="text-red-500 text-sm hidden mt-4"></div>

            <button id="submit" class="w-full bg-dark-brown text-white py-4 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 shadow-sm mt-8 flex justify-center items-center">
                <span id="button-text">Plătește {{ number_format($totalAmount, 2, ',', '.') }} RON</span>
                <span id="spinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ env("STRIPE_KEY") }}');
    const options = {
        clientSecret: '{{ $clientSecret }}',
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#D4AF37', // vintage-gold
                colorBackground: '#ffffff',
                colorText: '#2C1E16', // dark-brown
                colorDanger: '#df1b41',
                fontFamily: 'Montserrat, sans-serif',
                spacingUnit: '4px',
                borderRadius: '0px',
            }
        },
    };

    const elements = stripe.elements(options);

    // Create and mount the Address Element
    const addressElement = elements.create('address', {
        mode: 'shipping',
        allowedCountries: ['RO'], // Limit to Romania based on previous logic
    });
    addressElement.mount('#address-element');

    const paymentElement = elements.create('payment', {
        layout: 'tabs', // Use tabs for a cleaner look
    });
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitBtn = document.getElementById('submit');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        submitBtn.disabled = true;
        spinner.classList.remove('hidden');
        buttonText.textContent = 'Se procesează...';
        errorMessage.classList.add('hidden');

        const {error} = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: '{{ route("checkout.success") }}',
            },
        });

        if (error) {
            errorMessage.textContent = error.message;
            errorMessage.classList.remove('hidden');

            submitBtn.disabled = false;
            spinner.classList.add('hidden');
            buttonText.textContent = 'Plătește {{ number_format($totalAmount, 2, ',', '.') }} RON';
        }
        // If successful, Stripe automatically redirects to the return_url.
    });
</script>
@endsection
