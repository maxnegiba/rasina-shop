<section class="relative overflow-hidden border-b border-black/5 bg-[#f5efe6]">
    <div class="pointer-events-none absolute inset-0 opacity-50" aria-hidden="true">
        <div class="absolute -left-24 top-12 h-72 w-72 rounded-full bg-[#d9b36c]/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-[#cb8d82]/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto grid min-h-[calc(100vh-6rem)] max-w-[1600px] lg:grid-cols-[0.78fr_1.22fr]">
        <div class="flex items-center px-6 py-16 sm:px-10 lg:px-14 xl:px-20 xl:py-20">
            <div class="mx-auto w-full max-w-xl lg:mx-0">
                <div class="mb-9 flex items-center gap-4 text-[#af7f35]">
                    <span class="h-px w-10 bg-current"></span>
                    <span class="text-[11px] font-semibold uppercase tracking-[0.3em] sm:text-xs">MTD Art · Piese lucrate manual</span>
                </div>

                <h1 class="font-serif text-[clamp(3rem,6.2vw,6.5rem)] font-medium leading-[0.92] tracking-[-0.035em] text-dark-brown">
                    Din rășină,<br>
                    lumină și <span class="text-[#bf8179]">natură</span><br>
                    iau naștere<br>
                    obiecte cu <span class="text-[#b68b45]">suflet.</span>
                </h1>

                <div class="my-8 h-px w-12 bg-[#bf8179] sm:my-10"></div>

                <p class="max-w-lg text-base font-light leading-8 text-dark-brown/75 sm:text-lg">
                    Fiecare piesă este turnată, pigmentată și finisată manual, într-un atelier în care culoarea, simbolul și materia naturală se întâlnesc.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('shop.index') }}"
                       class="inline-flex min-h-13 items-center justify-center gap-3 rounded-md bg-[#344f3f] px-7 py-4 text-xs font-semibold uppercase tracking-[0.14em] text-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:bg-[#2b4335] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#344f3f]/30 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c0-5 2-8 6-10m-6 10c0-5-2-8-6-10" /></svg>
                        Descoperă colecțiile
                    </a>
                    <a href="{{ route('about') }}"
                       class="inline-flex min-h-13 items-center justify-center gap-3 rounded-md border border-[#b68b45]/70 bg-white/35 px-7 py-4 text-xs font-semibold uppercase tracking-[0.14em] text-[#9a7037] transition duration-300 hover:-translate-y-0.5 hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-[#b68b45]/25 focus:ring-offset-2">
                        Intră în atelier
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="mt-11 grid gap-5 border-t border-dark-brown/10 pt-7 sm:grid-cols-3">
                    <div class="flex gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#bf8179]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z" /></svg>
                        <div><p class="text-[11px] font-bold uppercase tracking-[0.12em]">Unicat</p><p class="mt-1 text-xs leading-5 text-dark-brown/60">Fiecare piesă este diferită</p></div>
                    </div>
                    <div class="flex gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#57735f]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 4c-7 0-12 4-12 10 0 2 1 4 3 6m-3-6c3 0 7-2 10-6" /></svg>
                        <div><p class="text-[11px] font-bold uppercase tracking-[0.12em]">Lucrat manual</p><p class="mt-1 text-xs leading-5 text-dark-brown/60">Cu grijă și pasiune</p></div>
                    </div>
                    <div class="flex gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#b68b45]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l2.5 6.5L21 11l-6.5 2.5L12 20l-2.5-6.5L3 11l6.5-2.5L12 2z" /></svg>
                        <div><p class="text-[11px] font-bold uppercase tracking-[0.12em]">Materiale premium</p><p class="mt-1 text-xs leading-5 text-dark-brown/60">Rășină și pigmenți aleși</p></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative min-h-[560px] overflow-hidden bg-[#ddd2c5] lg:min-h-full">
            <picture>
                <source srcset="{{ asset('img/hero/atelier-shelf.webp') }}" type="image/webp">
                <img src="{{ asset('img/hero/atelier-shelf.webp') }}"
                     alt="Raftul atelierului MTD Art cu pigmenți și materiale pentru rășină"
                     width="1200" height="1600"
                     fetchpriority="high"
                     decoding="async"
                     class="absolute inset-0 h-full w-full object-cover object-center">
            </picture>
            <div class="absolute inset-0 bg-gradient-to-r from-[#f5efe6]/25 via-transparent to-black/5"></div>

            <figure class="absolute right-[5%] top-[5%] w-[43%] overflow-hidden rounded-[1.6rem] border-[5px] border-white/90 bg-white shadow-2xl sm:w-[39%] lg:w-[42%] xl:w-[38%]">
                <img src="{{ asset('img/hero/atelier-prayer-mold.webp') }}"
                     alt="Matriță albastră cu motivul mâinilor împreunate"
                     width="1000" height="750"
                     loading="eager" decoding="async"
                     class="aspect-[4/5] h-full w-full object-cover">
            </figure>

            <figure class="absolute bottom-[5%] right-[9%] w-[43%] overflow-hidden rounded-[1.6rem] border-[5px] border-white/90 bg-white shadow-2xl sm:w-[38%] lg:w-[44%] xl:w-[39%]">
                <img src="{{ asset('img/hero/atelier-cross-casting.webp') }}"
                     alt="Cruce roșie din rășină aflată în procesul de turnare"
                     width="1000" height="1333"
                     loading="eager" decoding="async"
                     class="aspect-[4/5] h-full w-full object-cover object-center">
            </figure>

            <figure class="absolute bottom-[27%] left-[5%] hidden w-[25%] overflow-hidden rounded-2xl border-4 border-white/90 bg-white shadow-xl sm:block lg:w-[28%] xl:w-[24%]">
                <img src="{{ asset('img/hero/atelier-pigments.webp') }}"
                     alt="Pigmenți perlescenți și glitter folosiți în atelier"
                     width="1000" height="1333"
                     loading="lazy" decoding="async"
                     class="aspect-square h-full w-full object-cover">
            </figure>

            <div class="absolute bottom-[6%] left-[6%] hidden h-28 w-28 items-center justify-center rounded-full border border-[#b68b45]/50 bg-[#f7f0e5]/95 text-center shadow-xl backdrop-blur sm:flex lg:h-32 lg:w-32">
                <div>
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#a37639]">Din atelierul</p>
                    <p class="mt-1 font-serif text-lg tracking-[0.12em] text-dark-brown">MTD ART</p>
                    <span class="mt-1 block text-[#bf8179]" aria-hidden="true">♡</span>
                </div>
            </div>
        </div>
    </div>

    <div class="relative border-t border-black/5 bg-[#f8f2e8]/95">
        <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-dark-brown/10 px-4 py-5 md:grid-cols-4 md:px-8">
            @foreach([
                ['Pigmenți de calitate', 'Nuanțe intense și rezistente'],
                ['Elemente naturale', 'Flori, lemn și minerale'],
                ['Turnat manual', 'Fiecare detaliu contează'],
                ['Cadouri cu semnificație', 'Pentru momente speciale'],
            ] as [$title, $description])
                <div class="px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-xs">{{ $title }}</p>
                    <p class="mt-1 text-[11px] leading-5 text-dark-brown/60 sm:text-xs">{{ $description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
