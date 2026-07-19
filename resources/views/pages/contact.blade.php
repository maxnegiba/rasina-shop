@extends('layouts.app')

@section('content')
@php $settings = app(\App\Settings\GeneralSettings::class); @endphp
<div class="bg-ivory min-h-screen py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Antet -->
        <div class="text-center mb-20">
            <div class="inline-block px-4 py-1.5 bg-warm-beige/20 text-[10px] font-sans tracking-[0.2em] font-medium text-vintage-gold uppercase mb-8 border border-vintage-gold/20 shadow-sm">
                Contact
            </div>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif text-dark-brown mb-8 leading-tight">Ne Găsiți Aici</h2>
            <div class="w-12 h-px bg-vintage-gold mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24">
            
            <!-- Informații de Contact -->
            <div class="space-y-12 lg:pr-8">
                <div class="space-y-6">
                    <p class="text-dark-brown/80 font-light leading-loose text-lg">
                        Suntem deschiși la discuții, idei noi și, bineînțeles, cereri personalizate. Deoarece piesele noastre necesită un proces meticulos, fiecare proiect începe cu o conversație.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-sans tracking-[0.2em] font-semibold text-vintage-gold uppercase">Adresă Atelier</h3>
                        <p class="font-serif text-dark-brown text-xl leading-relaxed whitespace-pre-line">
                            {{ $settings->company_address ?: "Strada Pasiunii, Nr. 10\nBucurești, România" }}
                        </p>
                        <p class="text-xs text-dark-brown/50 italic font-light tracking-wide">(Vizite doar cu programare prealabilă)</p>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-[10px] font-sans tracking-[0.2em] font-semibold text-vintage-gold uppercase">Comunicare</h3>
                        <div class="font-serif text-dark-brown text-xl leading-relaxed flex flex-col gap-2">
                            <a href="mailto:{{ $settings->contact_email ?: 'contact@mtdart.ro' }}" class="hover:text-vintage-gold transition-colors duration-300">{{ $settings->contact_email ?: 'contact@mtdart.ro' }}</a>
                            @if($settings->contact_phone)
                            <a href="tel:{{ str_replace(' ', '', $settings->contact_phone) }}" class="hover:text-vintage-gold transition-colors duration-300">{{ $settings->contact_phone }}</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6 pt-6">
                    <h3 class="text-[10px] font-sans tracking-[0.2em] font-semibold text-vintage-gold uppercase">Program Atelier</h3>
                    <ul class="font-light text-dark-brown/70 space-y-4 text-sm">
                        @if(empty($settings->working_hours))
                            <li class="flex justify-between items-center border-b border-black/5 pb-3">
                                <span class="tracking-wide">Luni - Vineri</span>
                                <span class="font-medium">10:00 - 18:00</span>
                            </li>
                        @else
                            @foreach($settings->working_hours as $index => $hours)
                                <li class="flex justify-between items-center {{ !$loop->last ? 'border-b border-black/5 pb-3' : 'pb-2' }}">
                                    <span class="tracking-wide">{{ $hours['day'] ?? '' }}</span>
                                    <span class="font-medium {{ ($hours['hours'] ?? '') == 'Închis' ? 'text-vintage-gold' : '' }}">
                                        {{ $hours['hours'] ?? '' }}
                                        @if(!empty($hours['note']))
                                        <span class="text-xs text-dark-brown/50 italic font-light ml-1">({{ $hours['note'] }})</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Secțiune Cereri Personalizate (Link rapid) -->
                <div class="p-8 bg-warm-beige/20 border border-vintage-gold/20 shadow-sm mt-8">
                    <h3 class="font-serif text-2xl text-dark-brown mb-3">Comenzi Unicat</h3>
                    <p class="text-sm text-dark-brown/70 mb-8 font-light leading-relaxed">Ai o idee specifică? Dorești un produs din rășină cu dimensiuni sau culori particulare?</p>
                    <a href="#cerere-personalizata" class="inline-flex items-center gap-3 group text-[10px] uppercase tracking-[0.2em] text-dark-brown font-semibold hover:text-vintage-gold transition-colors duration-300">
                        <span>Spre Formular Unicat</span>
                        <span class="w-8 h-px bg-dark-brown group-hover:bg-vintage-gold group-hover:w-12 transition-all duration-300"></span>
                    </a>
                </div>
            </div>

            <!-- Formular de Contact (Generic) -->
            <div class="relative">
                <div class="bg-white p-8 sm:p-12 ring-1 ring-inset ring-black/5 shadow-sm relative z-10">
                    <h3 class="font-serif text-3xl text-dark-brown mb-10">Lăsați-ne un mesaj</h3>

                    @if(session('success'))
                        <div class="mb-8 p-4 bg-white shadow-sm border border-vintage-gold/30 text-dark-brown text-sm font-light flex items-center gap-3">
                            <svg class="w-5 h-5 text-vintage-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') ?? '#' }}" method="POST" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label for="name" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Nume Complet <span class="text-vintage-gold">*</span></label>
                                <input type="text" name="name" id="name" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown placeholder:text-black/20" placeholder="Ex: Ion Popescu">
                                @error('name') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label for="email" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Adresa de Email <span class="text-vintage-gold">*</span></label>
                                <input type="email" name="email" id="email" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown placeholder:text-black/20" placeholder="Ex: contact@email.ro">
                                @error('email') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label for="subject" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Subiect</label>
                            <input type="text" name="subject" id="subject" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown placeholder:text-black/20" placeholder="Întrebare despre un produs existent">
                            @error('subject') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="message" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Mesajul Dumneavoastră <span class="text-vintage-gold">*</span></label>
                            <textarea name="message" id="message" rows="4" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown placeholder:text-black/20 resize-none" placeholder="Cum vă putem ajuta?"></textarea>
                            @error('message') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full bg-dark-brown text-white text-[10px] uppercase tracking-[0.2em] font-semibold py-5 hover:bg-vintage-gold transition-colors duration-500 shadow-sm mt-4">
                            Trimite Mesajul
                        </button>

                        <p class="text-xs text-center text-dark-brown/50 font-light mt-6 tracking-wide">
                            Răspundem de obicei în 24-48 de ore lucrătoare.
                        </p>
                    </form>
                </div>
                
                <!-- Element decorativ de fundal -->
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-vintage-gold/5 rounded-full z-0 hidden lg:block"></div>
            </div>
        </div>

        <!-- Zona pentru Cereri Personalizate -->
        <div id="cerere-personalizata" class="mt-32 pt-24 border-t border-black/5">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <div class="inline-block px-4 py-1.5 bg-warm-beige/20 text-[10px] font-sans tracking-[0.2em] font-medium text-vintage-gold uppercase mb-6 border border-vintage-gold/20 shadow-sm">
                    Proiecte Speciale
                </div>
                <h2 class="text-4xl lg:text-5xl font-serif text-dark-brown mb-8 leading-tight">Comenzi Personalizate</h2>
                <p class="text-dark-brown/70 font-light leading-loose max-w-2xl mx-auto">
                    Vă rugăm să ne oferiți detalii despre viziunea dumneavoastră. Tipul de lemn dorit, nuanța rășinii, dimensiunile aproximative și scopul final al piesei de artă.
                </p>
            </div>

            <div class="max-w-3xl mx-auto">
                <form action="{{ route('custom-request.store') ?? '#' }}" method="POST" class="bg-white p-8 sm:p-12 ring-1 ring-inset ring-black/5 shadow-sm space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label for="custom_name" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Nume Complet <span class="text-vintage-gold">*</span></label>
                            <input type="text" name="customer_name" id="custom_name" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown">
                            @error('customer_name') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-3">
                            <label for="custom_email" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Email <span class="text-vintage-gold">*</span></label>
                            <input type="email" name="customer_email" id="custom_email" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown">
                            @error('customer_email') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label for="custom_phone" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Telefon</label>
                            <input type="text" name="customer_phone" id="custom_phone" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown">
                            @error('customer_phone') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-3">
                            <label for="custom_dimensions" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Dimensiuni Dorite</label>
                            <input type="text" name="dimensions_requested" id="custom_dimensions" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown placeholder:text-black/20" placeholder="Ex: 120 x 60 cm">
                            @error('dimensions_requested') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label for="custom_colors" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Preferințe Culori / Rășină</label>
                        <input type="text" name="color_preferences" id="custom_colors" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown placeholder:text-black/20" placeholder="Ex: Rășină turcoaz translucidă, lemn nuc">
                        @error('color_preferences') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-3">
                        <label for="custom_details" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Mesaj Special / Alte Detalii</label>
                        <textarea name="special_message" id="custom_details" rows="5" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown resize-none"></textarea>
                        @error('special_message') <span class="text-red-900/80 text-xs mt-2 block font-light">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-vintage-gold text-white text-[10px] uppercase tracking-[0.2em] font-semibold py-5 hover:bg-dark-brown transition-colors duration-500 shadow-sm mt-4">
                        Solicită Ofertă Personalizată
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection