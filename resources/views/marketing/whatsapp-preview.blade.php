@extends('layouts.app')

@section('content')
    <main class="min-h-[50vh] bg-ivory px-6 py-16 text-dark-brown">
        <div class="mx-auto max-w-xl space-y-6 text-center">
            <h1 class="font-serif text-3xl">WhatsApp tracking preview</h1>
            <p class="text-sm text-dark-brown/70">
                Staging-only analytics validation. Clicking the link should emit
                <code>whatsapp_click</code> through the same production JavaScript bundle.
            </p>

            <a
                id="marketing-whatsapp-preview"
                href="https://wa.me/40000000000"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center border border-dark-brown px-6 py-3 text-xs uppercase tracking-[0.18em] transition hover:bg-dark-brown hover:text-white"
            >
                Test WhatsApp click
            </a>
        </div>
    </main>
@endsection
