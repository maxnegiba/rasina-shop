@extends('layouts.app')

@section('content')
<div class="bg-ivory py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-sm font-sans tracking-[0.3em] text-vintage-gold uppercase mb-4">Contact</h1>
            <h2 class="text-4xl sm:text-5xl font-serif text-dark-brown mb-6">Ne Găsiți Aici</h2>
            <div class="w-16 h-px bg-vintage-gold mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Informații de Contact -->
            <div class="space-y-12">
                <div class="space-y-6">
                    <p class="text-dark-brown/80 font-light leading-relaxed">
                        Suntem deschiși la discuții, idei noi și, bineînțeles, cereri personalizate. Deoarece piesele noastre necesită un proces meticulos, fiecare proiect începe cu o conversație.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <h3 class="text-xs font-sans tracking-[0.2em] text-vintage-gold uppercase">Adresă Atelier</h3>
                        <p class="font-serif text-dark-brown text-lg">
                            Strada Pasiunii, Nr. 10<br>
                            București, România
                        </p>
                        <p class="text-sm text-dark-brown/60 italic">(Vizite doar cu programare prealabilă)</p>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xs font-sans tracking-[0.2em] text-vintage-gold uppercase">Comunicare</h3>
                        <p class="font-serif text-dark-brown text-lg">
                            <a href="mailto:contact@ivoryvintage.ro" class="hover:text-vintage-gold transition-colors">contact@ivoryvintage.ro</a><br>
                            <a href="tel:+40700000000" class="hover:text-vintage-gold transition-colors">+40 700 000 000</a>
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-sans tracking-[0.2em] text-vintage-gold uppercase">Program Atelier</h3>
                    <ul class="font-light text-dark-brown/80 space-y-1 text-sm">
                        <li class="flex justify-between border-b border-black/5 pb-1">
                            <span>Luni - Vineri:</span>
                            <span>10:00 - 18:00</span>
                        </li>
                        <li class="flex justify-between border-b border-black/5 pb-1">
                            <span>Sâmbătă:</span>
                            <span>10:00 - 14:00 (Doar programări)</span>
                        </li>
                        <li class="flex justify-between pb-1">
                            <span>Duminică:</span>
                            <span>Închis</span>
                        </li>
                    </ul>
                </div>

                <!-- Secțiune Cereri Personalizate (Link rapid) -->
                <div class="p-6 bg-warm-beige/30 border border-vintage-gold/20 rounded-sm">
                    <h3 class="font-serif text-xl text-dark-brown mb-2">Comenzi Unicat</h3>
                    <p class="text-sm text-dark-brown/80 mb-4 font-light">Ai o idee specifică? Dorești un blat din rășină cu dimensiuni sau culori particulare?</p>
                    <a href="#cerere-personalizata" class="inline-block px-6 py-2 border border-dark-brown text-xs uppercase tracking-[0.15em] text-dark-brown hover:bg-dark-brown hover:text-white transition-all duration-300">
                        Vezi detalii comandă unicat
                    </a>
                </div>
            </div>

            <!-- Formular de Contact (Generic) -->
            <div class="bg-ivory p-8 sm:p-10 shadow-sm border border-black/5 rounded-sm relative">
                <!-- Decor -->
                <div class="absolute top-0 right-0 w-24 h-24 bg-vintage-gold/5 rounded-bl-full -z-0"></div>

                <h3 class="font-serif text-2xl text-dark-brown mb-8 relative z-10">Lăsați-ne un mesaj</h3>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-800 text-sm border-l-4 border-green-500 font-light">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6 relative z-10">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Nume Complet <span class="text-vintage-gold">*</span></label>
                            <input type="text" name="name" id="name" required class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light placeholder-black/30 text-dark-brown" placeholder="Ion Popescu">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Adresa de Email <span class="text-vintage-gold">*</span></label>
                            <input type="email" name="email" id="email" required class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light placeholder-black/30 text-dark-brown" placeholder="ion@exemplu.ro">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="subject" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Subiect</label>
                        <input type="text" name="subject" id="subject" class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light placeholder-black/30 text-dark-brown" placeholder="Întrebare despre un produs existent">
                        @error('subject') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="message" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Mesajul Dumneavoastră <span class="text-vintage-gold">*</span></label>
                        <textarea name="message" id="message" rows="4" required class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light placeholder-black/30 text-dark-brown resize-none" placeholder="Cum vă putem ajuta?"></textarea>
                        @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-dark-brown text-white text-xs tracking-[0.2em] uppercase py-4 hover:bg-vintage-gold transition-colors duration-300 font-medium">
                        Trimite Mesajul
                    </button>

                    <p class="text-xs text-center text-dark-brown/50 font-light mt-4">
                        Vă vom răspunde în cel mai scurt timp, de obicei în 24-48 de ore lucrătoare.
                    </p>
                </form>
            </div>
        </div>

        <!-- Zona pentru Cereri Personalizate (Ancoră din butonul de sus) -->
        <div id="cerere-personalizata" class="mt-24 border-t border-black/10 pt-24 pb-12">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-serif text-dark-brown mb-6">Formular pentru Comenzi Personalizate</h2>
                <p class="text-dark-brown/80 font-light mb-10">
                    Vă rugăm să ne oferiți detalii despre viziunea dumneavoastră. Tipul de lemn, nuanța dorită pentru rășină, dimensiunile aproximative și scopul piesei.
                </p>

                <form action="{{ route('custom-request.store') }}" method="POST" class="space-y-6 text-left bg-ivory p-8 border border-vintage-gold/20 shadow-sm rounded-sm">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="custom_name" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Nume Complet</label>
                            <input type="text" name="customer_name" id="custom_name" required class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light">
                            @error('customer_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="custom_email" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Email</label>
                            <input type="email" name="customer_email" id="custom_email" required class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light">
                            @error('customer_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="custom_phone" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Telefon</label>
                            <input type="text" name="customer_phone" id="custom_phone" class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light">
                            @error('customer_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="custom_dimensions" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Dimensiuni Dorite</label>
                            <input type="text" name="dimensions_requested" id="custom_dimensions" class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light">
                            @error('dimensions_requested') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="custom_colors" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Preferințe Culori / Rășină</label>
                        <input type="text" name="color_preferences" id="custom_colors" class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light">
                        @error('color_preferences') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="custom_details" class="block text-xs font-sans tracking-wider text-dark-brown/80 uppercase">Mesaj Special / Alte Detalii</label>
                        <textarea name="special_message" id="custom_details" rows="5" class="w-full bg-transparent border-b border-black/20 pb-2 focus:border-vintage-gold focus:outline-none transition-colors rounded-none font-light resize-none"></textarea>
                        @error('special_message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-vintage-gold text-white text-xs tracking-[0.2em] uppercase py-4 hover:bg-dark-brown transition-colors duration-300 font-medium">
                        Solicită Ofertă Personalizată
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
