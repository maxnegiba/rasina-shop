@foreach($customSections as $section)
    @continue(($section['placement'] ?? '') !== $position)

    @php
        $eyebrow = trim((string) ($section['eyebrow'] ?? ''));
        $title = trim((string) ($section['title'] ?? ''));
        $body = trim((string) ($section['body'] ?? ''));
        $buttonLabel = trim((string) ($section['button_label'] ?? ''));
        $buttonUrl = trim((string) ($section['button_url'] ?? ''));
        $highlighted = (bool) ($section['highlighted'] ?? false);
    @endphp

    @if($title !== '' || $body !== '')
        <section class="mt-16 sm:mt-20">
            <div class="max-w-4xl mx-auto {{ $highlighted ? 'bg-warm-beige/20 border border-vintage-gold/20 shadow-sm p-8 sm:p-12' : 'text-center px-2 sm:px-8' }}">
                @if($eyebrow !== '')
                    <div class="inline-block px-4 py-1.5 bg-warm-beige/20 text-[10px] font-sans tracking-[0.2em] font-medium text-vintage-gold uppercase mb-6 border border-vintage-gold/20 shadow-sm">
                        {{ $eyebrow }}
                    </div>
                @endif

                @if($title !== '')
                    <h2 class="font-serif text-3xl sm:text-4xl text-dark-brown leading-tight {{ $body !== '' ? 'mb-6' : '' }}">
                        {{ $title }}
                    </h2>
                @endif

                @if($body !== '')
                    <p class="text-dark-brown/70 font-light leading-loose whitespace-pre-line {{ $highlighted ? '' : 'max-w-3xl mx-auto' }}">
                        {{ $body }}
                    </p>
                @endif

                @if($buttonLabel !== '' && $buttonUrl !== '')
                    <div class="mt-8">
                        <a href="{{ $buttonUrl }}" class="inline-flex items-center gap-3 group text-[10px] uppercase tracking-[0.2em] text-dark-brown font-semibold hover:text-vintage-gold transition-colors duration-300">
                            <span>{{ $buttonLabel }}</span>
                            <span class="w-10 h-px bg-dark-brown group-hover:bg-vintage-gold group-hover:w-14 transition-all duration-300"></span>
                        </a>
                    </div>
                @endif
            </div>
        </section>
    @endif
@endforeach
