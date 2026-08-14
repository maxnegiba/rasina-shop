@extends('layouts.app')

@section('content')
@php
    $settings = app(\App\Settings\GeneralSettings::class);
    $aboutImage = trim((string) $settings->about_image);
@endphp
<div class="bg-ivory min-h-screen py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <div class="inline-block px-4 py-1.5 bg-warm-beige/20 text-[10px] font-sans tracking-[0.2em] font-medium text-vintage-gold uppercase mb-8 border border-vintage-gold/20 shadow-sm">
                {{ $settings->about_eyebrow }}
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif text-dark-brown mb-8 leading-tight">{{ $settings->about_title }}</h1>
            <div class="w-12 h-px bg-vintage-gold mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <div class="relative">
                <div class="aspect-[4/5] bg-white ring-1 ring-inset ring-black/5 p-4 shadow-sm relative z-10 flex items-center justify-center group transition-all duration-500 hover:shadow-md">
                    <div class="w-full h-full bg-warm-beige/20 flex items-center justify-center border border-black/5 overflow-hidden">
                        @if($aboutImage !== '')
                            <img src="{{ asset('storage/' . ltrim($aboutImage, '/')) }}"
                                 alt="{{ $settings->about_image_alt }}"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]"
                                 loading="eager"
                                 decoding="async">
                        @else
                            <span class="text-dark-brown/30 font-serif italic text-xl sm:text-2xl tracking-wide text-center px-6">
                                {{ $settings->about_image_alt }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="absolute -bottom-8 -right-8 w-48 h-48 border border-vintage-gold/20 rounded-full -z-0 hidden md:block"></div>
                <div class="absolute -top-8 -left-8 w-32 h-32 bg-vintage-gold/5 rounded-full -z-0 hidden md:block"></div>
            </div>

            <div class="space-y-10 text-dark-brown/80 font-light leading-loose">
                <blockquote class="pl-6 border-l border-vintage-gold/50 bg-warm-beige/10 py-4 pr-4 italic text-lg sm:text-xl font-serif text-dark-brown leading-relaxed">
                    “{{ $settings->about_quote }}”
                </blockquote>

                <p class="whitespace-pre-line">{{ $settings->about_paragraph_one }}</p>
                <p class="whitespace-pre-line">{{ $settings->about_paragraph_two }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 pt-10 border-t border-black/5">
                    <div>
                        <h2 class="font-serif text-2xl text-dark-brown mb-4 flex items-center gap-3">
                            <span class="w-4 h-px bg-vintage-gold"></span> {{ $settings->about_value_one_title }}
                        </h2>
                        <p class="text-sm leading-relaxed text-dark-brown/70 whitespace-pre-line">{{ $settings->about_value_one_text }}</p>
                    </div>
                    <div>
                        <h2 class="font-serif text-2xl text-dark-brown mb-4 flex items-center gap-3">
                            <span class="w-4 h-px bg-vintage-gold"></span> {{ $settings->about_value_two_title }}
                        </h2>
                        <p class="text-sm leading-relaxed text-dark-brown/70 whitespace-pre-line">{{ $settings->about_value_two_text }}</p>
                    </div>
                </div>

                <div class="pt-8">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-4 group text-[10px] uppercase tracking-[0.2em] text-dark-brown font-semibold hover:text-vintage-gold transition-colors duration-300">
                        <span>{{ $settings->about_cta_label }}</span>
                        <span class="w-12 h-px bg-dark-brown group-hover:bg-vintage-gold group-hover:w-16 transition-all duration-500"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
