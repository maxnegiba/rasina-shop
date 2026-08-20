@extends('layouts.app')

@section('content')
    <main class="min-h-[60vh] bg-ivory px-6 py-16 text-dark-brown">
        <div class="mx-auto max-w-2xl space-y-8">
            <div class="space-y-3 text-center">
                <h1 class="font-serif text-3xl">Marketing all-events preview</h1>
                <p class="text-sm text-dark-brown/70">
                    Staging-only validation page. It does not create orders, requests, messages, stock reservations, or Stripe calls.
                </p>
            </div>

            <div class="rounded border border-dark-brown/20 bg-white p-6 space-y-4">
                <p class="text-sm">
                    Current consent cookie:
                    <code id="marketing-consent-value" class="font-mono">unknown</code>
                </p>

                <button
                    id="marketing-fire-all-events"
                    type="button"
                    class="w-full border border-dark-brown px-6 py-3 text-xs uppercase tracking-[0.18em] transition hover:bg-dark-brown hover:text-white"
                >
                    Fire all 8 marketing events
                </button>

                <button
                    id="marketing-clear-consent"
                    type="button"
                    class="w-full border border-dark-brown/40 px-6 py-3 text-xs uppercase tracking-[0.18em] transition hover:bg-dark-brown hover:text-white"
                >
                    Clear consent cookie & reload
                </button>

                <div id="marketing-preview-status" class="text-sm text-dark-brown/70"></div>
            </div>

            <ol class="list-decimal space-y-1 pl-5 text-sm text-dark-brown/70">
                <li>view_product</li>
                <li>add_to_cart</li>
                <li>begin_checkout</li>
                <li>purchase</li>
                <li>custom_order_started</li>
                <li>custom_order_sent</li>
                <li>whatsapp_click</li>
                <li>contact_form_sent</li>
            </ol>
        </div>
    </main>

    <script>
        (() => {
            const consentValue = document.getElementById('marketing-consent-value');
            const fireButton = document.getElementById('marketing-fire-all-events');
            const clearButton = document.getElementById('marketing-clear-consent');
            const status = document.getElementById('marketing-preview-status');

            const readConsent = () => {
                const item = document.cookie
                    .split('; ')
                    .find((cookie) => cookie.startsWith('__cookie_consent='));

                return item ? decodeURIComponent(item.split('=').slice(1).join('=')) : 'not-set';
            };

            const refreshConsentLabel = () => {
                consentValue.textContent = readConsent();
            };

            const ecommerce = {
                currency: 'RON',
                value: 90,
                items: [{
                    item_id: 'PREVIEW-001',
                    item_name: 'MTD ART Marketing Preview',
                    item_category: 'Preview',
                    item_variant: 'preview',
                    price: 90,
                    quantity: 1,
                }],
            };

            const events = [
                { event: 'view_product', ecommerce },
                { event: 'add_to_cart', ecommerce },
                { event: 'begin_checkout', ecommerce },
                {
                    event: 'purchase',
                    ecommerce: {
                        ...ecommerce,
                        transaction_id: `MTD-PREVIEW-${Date.now()}`,
                        shipping: 0,
                    },
                },
                { event: 'custom_order_started', custom_order: { source: 'staging_preview' } },
                { event: 'custom_order_sent', custom_order: { source: 'staging_preview' } },
                { event: 'whatsapp_click', contact: { source: 'staging_preview' } },
                { event: 'contact_form_sent', contact: { source: 'staging_preview' } },
            ];

            fireButton?.addEventListener('click', () => {
                window.dataLayer = window.dataLayer || [];

                events.forEach((event, index) => {
                    window.setTimeout(() => window.dataLayer.push(event), index * 150);
                });

                status.textContent = `Queued ${events.length} events. Check Tag Assistant.`;
            });

            clearButton?.addEventListener('click', () => {
                document.cookie = '__cookie_consent=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax';
                window.location.reload();
            });

            refreshConsentLabel();
        })();
    </script>
@endsection
