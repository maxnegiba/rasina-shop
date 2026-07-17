@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    <div class="max-w-3xl mx-auto text-center mb-16">
        <h1 class="font-serif text-4xl lg:text-5xl text-dark-brown mb-8 leading-tight">
            Procesul Nostru Artizanal
        </h1>
        <div class="w-12 h-px bg-vintage-gold mx-auto mb-8"></div>
        <p class="font-light text-dark-brown/70 text-base md:text-lg leading-relaxed">
            Fiecare lucrare pe care o creăm poartă o amprentă unică. Prin serviciul nostru de comenzi personalizate, colaborăm îndeaproape cu dumneavoastră pentru a aduce la viață o piesă de artă care rezonează perfect cu spațiul și estetica dorită. De la alegerea nuanțelor și până la definirea dimensiunilor ideale, suntem aici să vă ghidăm.
        </p>
    </div>

    <x-flashy-custom-order />
</div>
@endsection
