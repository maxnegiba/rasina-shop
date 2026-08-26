@extends('layouts.app')

@section('content')
    <main class="min-h-[60vh] bg-ivory px-6 py-16 text-dark-brown">
        <div class="mx-auto max-w-2xl space-y-6 text-center">
            <h1 class="font-serif text-3xl">Meta CAPI Purchase Preview</h1>

            <p class="text-sm text-dark-brown/70">
                Staging-only validation. No order, Stripe payment, stock reservation, email, or customer record was created.
            </p>

            <div class="space-y-2 rounded border border-dark-brown/20 bg-white p-6 text-left text-sm">
                <p><strong>event_id:</strong> <code>{{ $eventId }}</code></p>
                <p><strong>Browser purchase:</strong> queued in dataLayer</p>
                <p><strong>Server CAPI:</strong> {{ $serverSent ? 'sent to Meta Test Events' : 'not sent' }}</p>
                <p><strong>Marketing consent:</strong> {{ $marketingConsent ? 'granted' : 'not granted' }}</p>
            </div>

            @if ($serverError)
                <p class="text-sm text-red-700">
                    Server CAPI test failed: {{ $serverError }}
                </p>
            @endif
        </div>
    </main>
@endsection
