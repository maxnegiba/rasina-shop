@extends('layouts.app')

@section('content')
<div class="bg-ivory py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-sm font-sans tracking-[0.3em] text-vintage-gold uppercase mb-4">Povestea Noastră</h1>
            <h2 class="text-4xl sm:text-5xl font-serif text-dark-brown mb-6">Arta Născută din Pasiune</h2>
            <div class="w-16 h-px bg-vintage-gold mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="relative">
                <div class="aspect-[4/5] bg-warm-beige rounded-sm overflow-hidden relative">
                    <!-- Aici ar veni o imagine cu atelierul sau artistul -->
                    <div class="absolute inset-0 flex items-center justify-center text-dark-brown/20 font-serif italic text-2xl">
                        Imagine Atelier / Artist
                    </div>
                </div>
                <!-- Element decorativ -->
                <div class="absolute -bottom-8 -right-8 w-48 h-48 border border-vintage-gold/30 rounded-full -z-10 hidden md:block"></div>
                <div class="absolute -top-8 -left-8 w-32 h-32 bg-vintage-gold/5 rounded-full -z-10 hidden md:block"></div>
            </div>

            <div class="space-y-8 text-dark-brown/80 font-light leading-relaxed">
                <p class="text-lg font-serif italic text-dark-brown">
                    "Ivory Vintage a pornit de la fascinația pentru frumusețea imperfectă a naturii și dorința de a o păstra vie pentru totdeauna."
                </p>

                <p>
                    Fiecare piesă creată în atelierul nostru spune o poveste unică. Folosim esențe de lemn nobil, atent selecționate, adesea cu imperfecțiuni naturale care, sub magia rășinii epoxidice, devin adevărate capodopere vizuale. Nu credem în producția de masă, ci în timpul, atenția și sufletul pus în fiecare creație.
                </p>

                <p>
                    De la blaturi impresionante de masă, până la obiecte de cult delicate și piese comemorative emoționante, procesul nostru este unul meticulos, pur manual. Transluciditatea rășinii, combinată cu inserții de pigmenți, foiță de aur sau elemente naturale, creează o fuziune atemporală între clasic și modern.
                </p>

                <div class="grid grid-cols-2 gap-8 pt-8 border-t border-black/5">
                    <div>
                        <h3 class="font-serif text-xl text-dark-brown mb-2">Măiestrie</h3>
                        <p class="text-sm">Finisaje executate la cele mai înalte standarde, cu atenție la fiecare micron.</p>
                    </div>
                    <div>
                        <h3 class="font-serif text-xl text-dark-brown mb-2">Unicitate</h3>
                        <p class="text-sm">Nicio piesă nu este identică cu alta. Lemnul și rășina dictează designul final.</p>
                    </div>
                </div>

                <div class="pt-8">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-3 text-sm font-sans tracking-[0.15em] text-vintage-gold uppercase hover:text-dark-brown transition-colors duration-300 group">
                        <span>Descoperă Galeria</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
