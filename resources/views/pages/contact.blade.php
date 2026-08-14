@extends('layouts.app')

@section('content')
@php
    $settings = app(\App\Settings\GeneralSettings::class);
    $contactEmail = $settings->contact_email ?: 'contact@mtdart.ro';
    $contactPhone = trim((string) $settings->contact_phone);
    $enabledSections = $settings->contact_enabled_sections;
    $customSections = collect($settings->contact_custom_sections);
    $isEnabled = static fn (string $section): bool => in_array($section, $enabledSections, true);

    $hasAddress = $isEnabled('address');
    $hasCommunication = $isEnabled('communication');
    $hasContactDetails = $hasAddress || $hasCommunication;
    $hasLeftColumn = $isEnabled('intro') || $hasContactDetails || $isEnabled('hours') || $isEnabled('custom_card');
    $hasContactForm = $isEnabled('contact_form');
    $hasMainArea = $hasLeftColumn || $hasContactForm;
    $mainGridClasses = $hasLeftColumn && $hasContactForm
        ? 'grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24'
        : 'grid grid-cols-1 gap-16';
    $contactDetailsClasses = $hasAddress && $hasCommunication
        ? 'grid grid-cols-1 sm:grid-cols-2 gap-10'
        : 'grid grid-cols-1 gap-10';
    $customCardHref = $isEnabled('custom_request')
        ? '#cerere-personalizata'
        : ($hasContactForm ? '#formular-contact' : null);
@endphp
<div class="bg-ivory min-h-screen py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($isEnabled('header'))
            <div class="text-center mb-20">
                <div class="inline-block px-4 py-1.5 bg-warm-beige/20 text-[10px] font-sans tracking-[0.2em] font-medium text-vintage-gold uppercase mb-8 border border-vintage-gold/20 shadow-sm">
                    {{ $settings->contact_eyebrow }}
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif text-dark-brown mb-8 leading-tight">{{ $settings->contact_title }}</h1>
                <div class="w-12 h-px bg-vintage-gold mx-auto"></div>
            </div>
        @endif

        @include('pages.partials.contact-custom-sections', ['position' => 'after_header'])

        @if($hasMainArea)
            <div class="{{ $mainGridClasses }} {{ ! $isEnabled('header') ? 'mt-4' : '' }}">
                @if($hasLeftColumn)
                    <div class="space-y-12 {{ $hasContactForm ? 'lg:pr-8' : 'max-w-4xl mx-auto w-full' }}">
                        @if($isEnabled('intro'))
                            <p class="text-dark-brown/80 font-light leading-loose text-lg whitespace-pre-line">{{ $settings->contact_intro }}</p>
                        @endif

                        @if($hasContactDetails)
                            <div class="{{ $contactDetailsClasses }}">
                                @if($hasAddress)
                                    <div class="space-y-4">
                                        <h2 class="text-[10px] font-sans tracking-[0.2em] font-semibold text-vintage-gold uppercase">{{ $settings->contact_address_label }}</h2>
                                        @if(trim((string) $settings->company_address) !== '')
                                            <p class="font-serif text-dark-brown text-xl leading-relaxed whitespace-pre-line">{{ $settings->company_address }}</p>
                                        @endif
                                        @if(trim((string) $settings->contact_address_note) !== '')
                                            <p class="text-xs text-dark-brown/50 italic font-light tracking-wide">({{ $settings->contact_address_note }})</p>
                                        @endif
                                    </div>
                                @endif

                                @if($hasCommunication)
                                    <div class="space-y-4">
                                        <h2 class="text-[10px] font-sans tracking-[0.2em] font-semibold text-vintage-gold uppercase">{{ $settings->contact_communication_label }}</h2>
                                        <div class="font-serif text-dark-brown text-xl leading-relaxed flex flex-col gap-2">
                                            <a href="mailto:{{ $contactEmail }}" class="hover:text-vintage-gold transition-colors duration-300">{{ $contactEmail }}</a>
                                            @if($contactPhone !== '')
                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="hover:text-vintage-gold transition-colors duration-300">{{ $contactPhone }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($isEnabled('hours'))
                            <div class="space-y-6 pt-6">
                                <h2 class="text-[10px] font-sans tracking-[0.2em] font-semibold text-vintage-gold uppercase">{{ $settings->contact_hours_label }}</h2>
                                <ul class="font-light text-dark-brown/70 space-y-4 text-sm">
                                    @foreach($settings->working_hours as $hours)
                                        <li class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 {{ !$loop->last ? 'border-b border-black/5 pb-3' : 'pb-2' }}">
                                            <span class="tracking-wide">{{ $hours['day'] ?? '' }}</span>
                                            <span class="font-medium">
                                                {{ $hours['hours'] ?? '' }}
                                                @if(!empty($hours['note']))
                                                    <span class="text-xs text-dark-brown/50 italic font-light ml-1">({{ $hours['note'] }})</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($isEnabled('custom_card'))
                            <div class="p-8 bg-warm-beige/20 border border-vintage-gold/20 shadow-sm mt-8">
                                <h2 class="font-serif text-2xl text-dark-brown mb-3">{{ $settings->contact_custom_card_title }}</h2>
                                <p class="text-sm text-dark-brown/70 {{ $customCardHref ? 'mb-8' : '' }} font-light leading-relaxed whitespace-pre-line">{{ $settings->contact_custom_card_text }}</p>
                                @if($customCardHref)
                                    <a href="{{ $customCardHref }}" class="inline-flex items-center gap-3 group text-[10px] uppercase tracking-[0.2em] text-dark-brown font-semibold hover:text-vintage-gold transition-colors duration-300">
                                        <span>{{ $settings->contact_custom_card_cta }}</span>
                                        <span class="w-8 h-px bg-dark-brown group-hover:bg-vintage-gold group-hover:w-12 transition-all duration-300"></span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @if($hasContactForm)
                    <div id="formular-contact" class="relative {{ ! $hasLeftColumn ? 'max-w-3xl mx-auto w-full' : '' }}">
                        <div class="bg-white p-8 sm:p-12 ring-1 ring-inset ring-black/5 shadow-sm relative z-10">
                            <h2 class="font-serif text-3xl text-dark-brown mb-10">{{ $settings->contact_form_title }}</h2>

                            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-8">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div class="space-y-3">
                                        <label for="name" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Nume Complet <span class="text-vintage-gold">*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown">
                                        @error('name') <span class="text-red-900/80 text-xs mt-2 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-3">
                                        <label for="email" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Adresa de Email <span class="text-vintage-gold">*</span></label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown">
                                        @error('email') <span class="text-red-900/80 text-xs mt-2 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <label for="subject" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Subiect</label>
                                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown">
                                </div>
                                <div class="space-y-3">
                                    <label for="message" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Mesajul Dumneavoastră <span class="text-vintage-gold">*</span></label>
                                    <textarea name="message" id="message" rows="5" maxlength="5000" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 transition-colors rounded-none font-light text-sm text-dark-brown resize-none">{{ old('message') }}</textarea>
                                    @error('message') <span class="text-red-900/80 text-xs mt-2 block">{{ $message }}</span> @enderror
                                </div>
                                <button type="submit" class="w-full bg-dark-brown text-white text-[10px] uppercase tracking-[0.2em] font-semibold py-5 hover:bg-vintage-gold transition-colors duration-500 shadow-sm">Trimite Mesajul</button>
                                <p class="text-xs text-center text-dark-brown/50 font-light mt-6 tracking-wide">{{ $settings->contact_form_response_note }}</p>
                            </form>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-vintage-gold/5 rounded-full z-0 hidden lg:block"></div>
                    </div>
                @endif
            </div>
        @endif

        @include('pages.partials.contact-custom-sections', ['position' => 'after_main'])
        @include('pages.partials.contact-custom-sections', ['position' => 'before_custom_request'])

        @if($isEnabled('custom_request'))
            <div id="cerere-personalizata" class="mt-32 pt-24 border-t border-black/5">
                <div class="max-w-4xl mx-auto text-center mb-16">
                    <div class="inline-block px-4 py-1.5 bg-warm-beige/20 text-[10px] font-sans tracking-[0.2em] font-medium text-vintage-gold uppercase mb-6 border border-vintage-gold/20 shadow-sm">{{ $settings->contact_custom_eyebrow }}</div>
                    <h2 class="text-4xl lg:text-5xl font-serif text-dark-brown mb-8 leading-tight">{{ $settings->contact_custom_title }}</h2>
                    <p class="text-dark-brown/70 font-light leading-loose max-w-2xl mx-auto whitespace-pre-line">{{ $settings->contact_custom_intro }}</p>
                </div>

                <div class="max-w-3xl mx-auto">
                    <form action="{{ route('custom-request.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 sm:p-12 ring-1 ring-inset ring-black/5 shadow-sm space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label for="custom_name" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Nume Complet <span class="text-vintage-gold">*</span></label>
                                <input type="text" name="customer_name" id="custom_name" value="{{ old('customer_name') }}" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 rounded-none text-sm">
                            </div>
                            <div class="space-y-3">
                                <label for="custom_email" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Email <span class="text-vintage-gold">*</span></label>
                                <input type="email" name="customer_email" id="custom_email" value="{{ old('customer_email') }}" required class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 rounded-none text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label for="custom_phone" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Telefon</label>
                                <input type="tel" name="customer_phone" id="custom_phone" value="{{ old('customer_phone') }}" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 rounded-none text-sm">
                            </div>
                            <div class="space-y-3">
                                <label for="custom_dimensions" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Dimensiuni Dorite</label>
                                <input type="text" name="dimensions_requested" id="custom_dimensions" value="{{ old('dimensions_requested') }}" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 rounded-none text-sm">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label for="custom_colors" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Preferințe Culori / Rășină</label>
                            <input type="text" name="color_preferences" id="custom_colors" value="{{ old('color_preferences') }}" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 rounded-none text-sm">
                        </div>
                        <div class="space-y-3">
                            <label for="custom_details" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Mesaj Special / Alte Detalii</label>
                            <textarea name="special_message" id="custom_details" rows="5" maxlength="5000" class="w-full bg-transparent border-b border-black/10 pb-3 pt-2 focus:border-vintage-gold focus:ring-0 rounded-none text-sm resize-none">{{ old('special_message') }}</textarea>
                        </div>
                        <div class="space-y-3">
                            <label for="reference_image" class="block text-[10px] uppercase tracking-[0.2em] font-semibold text-dark-brown/70">Imagine de Referință</label>
                            <input type="file" name="reference_image" id="reference_image" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-dark-brown/70 file:mr-4 file:border-0 file:bg-warm-beige/40 file:px-4 file:py-3 file:text-[10px] file:uppercase file:tracking-[0.15em] file:text-dark-brown">
                            @error('reference_image') <span class="text-red-900/80 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full bg-dark-brown text-white text-[10px] uppercase tracking-[0.2em] font-semibold py-5 hover:bg-vintage-gold transition-colors duration-500 shadow-sm">Trimite Cererea Personalizată</button>
                    </form>
                </div>
            </div>
        @endif

        @include('pages.partials.contact-custom-sections', ['position' => 'after_custom_request'])
    </div>
</div>
@endsection
