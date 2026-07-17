<div class="my-24 relative overflow-hidden bg-dark-brown text-ivory py-20 px-6 sm:px-12 lg:px-24">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <path d="M0 0 L100 100 M100 0 L0 100" stroke="currentColor" stroke-width="0.5" fill="none" />
        </svg>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto text-center">
        <span class="block text-vintage-gold text-4xl mb-6">✧</span>
        <h2 class="font-serif text-3xl md:text-5xl lg:text-6xl mb-6 leading-tight">
            Creează un Produs Unicat
        </h2>
        <p class="font-light text-ivory/80 text-sm md:text-base lg:text-lg mb-12 max-w-2xl mx-auto leading-relaxed">
            Transformăm viziunea ta în realitate. Alege finisaje, culori și detalii complet personalizate.
        </p>

        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <button x-data @click="$dispatch('open-custom-modal')" class="w-full sm:w-auto bg-vintage-gold text-white px-10 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-white hover:text-dark-brown transition-colors duration-500 shadow-lg">
                Solicită Proiect
            </button>

            @php
                $settings = app(\App\Settings\GeneralSettings::class);
                $whatsappNumber = preg_replace('/[^0-9]/', '', $settings->contact_whatsapp_number);
                $whatsappText = urlencode($settings->default_whatsapp_greeting_text);
                $whatsappUrl = $whatsappNumber ? "https://wa.me/{$whatsappNumber}?text={$whatsappText}" : "#";
            @endphp

            @if($whatsappNumber)
                <a href="{{ $whatsappUrl }}" target="_blank" class="w-full sm:w-auto bg-transparent border border-vintage-gold text-vintage-gold px-10 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold hover:text-white transition-colors duration-500 flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Scrie-ne pe WhatsApp
                </a>
            @endif
        </div>
    </div>
</div>
