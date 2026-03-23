@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 text-center">

    <div class="inline-block w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mb-8 mx-auto border border-green-200">
        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    </div>

    <h1 class="font-serif text-4xl text-dark-brown mb-6">Vă Mulțumim pentru Comandă!</h1>

    <div class="w-16 h-px bg-vintage-gold mx-auto mb-8"></div>

    <p class="text-dark-brown/60 font-light max-w-xl mx-auto mb-12 leading-relaxed">
        Plata dumneavoastră a fost procesată cu succes. Vă vom trimite un email de confirmare cu detaliile comenzii.
        Pregătim cu mare grijă piesele dumneavoastră.
    </p>

    <a href="{{ route('shop.index') }}" class="inline-block bg-dark-brown text-white px-10 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 shadow-sm">
        Înapoi la Galerie
    </a>
</div>
@endsection