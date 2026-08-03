<section class="relative overflow-hidden border-b border-black/5 bg-[#f4eee4] lg:h-[calc(100svh-6rem)] lg:min-h-[650px] lg:max-h-[820px]">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-24 top-16 h-72 w-72 rounded-full bg-[#d7b36f]/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-[#c98f84]/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto flex h-full max-w-[1700px] flex-col lg:grid lg:grid-rows-[minmax(0,1fr)_86px]">
        <div class="grid min-h-0 lg:grid-cols-[38%_62%]">
            <div class="flex items-center px-6 py-12 sm:px-10 lg:px-12 lg:py-7 xl:px-16 2xl:px-20">
                <div class="mx-auto w-full max-w-[540px] lg:mx-0">
                    <div class="mb-5">
                        <div class="flex items-center gap-3 text-[#aa7a36]">
                            <svg class="h-7 w-7" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                <path d="M24 40V16M24 24c-7-1-11-5-12-11 7 0 11 4 12 11Zm0 5c7-1 11-5 12-11-7 0-11 4-12 11Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 39h20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <div>
                                <p class="font-serif text-[1.35rem] font-semibold leading-none tracking-[0.14em] text-dark-brown">MTD ART</p>
                                <p class="mt-1 text-[9px] font-semibold uppercase tracking-[0.27em] text-[#aa7a36]">Piese lucrate manual</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-3 text-[#b68b45]">
                            <span class="h-px w-10 bg-current"></span>
                            <span class="h-1 w-1 rotate-45 bg-current"></span>
                            <span class="h-px w-10 bg-current"></span>
                        </div>
                    </div>

                    <h1 class="font-serif text-[clamp(3.05rem,4.35vw,5rem)] font-medium leading-[0.91] tracking-[-0.035em] text-dark-brown">
                        Din rășină,<br>
                        lumină și <span class="text-[#bf8179]">natură</span><br>
                        iau naștere<br>
                        obiecte cu <span class="text-[#b68b45]">suflet.</span>
                    </h1>

                    <div class="my-5 h-px w-12 bg-[#bf8179] xl:my-6"></div>

                    <p class="max-w-md text-sm font-light leading-6 text-dark-brown/75 xl:text-[15px] xl:leading-7">
                        Fiecare piesă este turnată, pigmentată și finisată manual, într-un atelier în care culoarea, simbolul și materia naturală se întâlnesc.
                    </p>

                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center xl:mt-6">
                        <a href="{{ route('shop.index') }}"
                           class="inline-flex min-h-11 items-center justify-center gap-3 rounded-md bg-[#344f3f] px-6 py-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:bg-[#2b4335] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#344f3f]/30 focus:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c0-5 2-8 6-10m-6 10c0-5-2-8-6-10" /></svg>
                            Descoperă colecțiile
                        </a>
                        <a href="{{ route('about') }}"
                           class="inline-flex min-h-11 items-center justify-center gap-3 rounded-md border border-[#b68b45]/70 bg-white/35 px-6 py-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-[#9a7037] transition duration-300 hover:-translate-y-0.5 hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-[#b68b45]/25 focus:ring-offset-2">
                            Intră în atelier <span aria-hidden="true">→</span>
                        </a>
                    </div>

                    <div class="mt-6 grid gap-4 border-t border-dark-brown/10 pt-4 sm:grid-cols-3 xl:mt-7">
                        <div class="flex gap-2.5">
                            <span class="text-lg leading-none text-[#bf8179]" aria-hidden="true">♡</span>
                            <div><p class="text-[9px] font-bold uppercase tracking-[0.12em]">Unicat</p><p class="mt-1 text-[10px] leading-4 text-dark-brown/60">Fiecare piesă este diferită</p></div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="text-lg leading-none text-[#57735f]" aria-hidden="true">⌁</span>
                            <div><p class="text-[9px] font-bold uppercase tracking-[0.12em]">Lucrat manual</p><p class="mt-1 text-[10px] leading-4 text-dark-brown/60">Cu grijă și pasiune</p></div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="text-lg leading-none text-[#b68b45]" aria-hidden="true">✧</span>
                            <div><p class="text-[9px] font-bold uppercase tracking-[0.12em]">Materiale premium</p><p class="mt-1 text-[10px] leading-4 text-dark-brown/60">Rășină și pigmenți aleși</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative min-h-[640px] overflow-hidden bg-[#dfd4c7] lg:min-h-0">
                <div class="absolute inset-0 grid grid-cols-[43%_57%]">
                    <figure class="relative overflow-hidden border-l border-white/45 bg-[#ded3c5]">
                        <img src="{{ asset('img/hero/atelier-shelf.webp') }}"
                             alt="Raftul atelierului MTD Art cu pigmenți și materiale pentru rășină"
                             width="1200" height="1600"
                             fetchpriority="high" decoding="async"
                             class="h-full w-full object-cover object-center">
                        <div class="absolute inset-0 bg-gradient-to-r from-[#f4eee4]/10 via-transparent to-black/5"></div>
                    </figure>

                    <div class="relative overflow-hidden bg-[#d9cec1]">
                        <img src="{{ asset('img/hero/atelier-tools.webp') }}"
                             alt="Unelte, recipiente și accesorii folosite la turnarea rășinii"
                             width="1200" height="900"
                             loading="eager" decoding="async"
                             class="absolute inset-0 h-full w-full object-cover object-center">
                        <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-transparent to-[#36271e]/10"></div>

                        <figure class="absolute left-[5%] top-[4%] z-20 w-[58%] overflow-hidden rounded-[1.45rem] border-[5px] border-white/90 bg-white shadow-2xl">
                            <img src="{{ asset('img/hero/atelier-prayer-mold.webp') }}"
                                 alt="Matriță albastră cu motivul mâinilor împreunate"
                                 width="900" height="1200"
                                 loading="eager" decoding="async"
                                 class="aspect-[4/3] w-full object-cover object-[50%_48%]">
                        </figure>

                        <figure class="absolute bottom-[3%] left-[-1%] z-10 w-[62%] overflow-hidden rounded-[1.45rem] border-[5px] border-white/90 bg-white shadow-2xl">
                            <img src="{{ asset('img/hero/atelier-red-cross.webp') }}"
                                 alt="Cruce roșie din rășină aflată în procesul de turnare"
                                 width="900" height="1200"
                                 loading="eager" decoding="async"
                                 class="aspect-[4/3] w-full object-cover object-[50%_54%]">
                        </figure>

                        <figure class="absolute right-[4%] top-[38%] z-30 w-[34%] overflow-hidden rounded-[1.2rem] border-4 border-white/90 bg-white shadow-xl">
                            <img src="{{ asset('img/hero/atelier-cured-crosses.webp') }}"
                                 alt="Piese în formă de cruce scoase din matrițe și pregătite pentru finisare"
                                 width="900" height="1200"
                                 loading="lazy" decoding="async"
                                 class="aspect-[4/5] w-full object-cover object-center">
                        </figure>

                        <figure class="absolute bottom-[-7%] right-[-3%] z-20 w-[31%] overflow-hidden rounded-tl-[1.2rem] border-[5px] border-white/90 bg-white shadow-2xl">
                            <img src="{{ asset('img/hero/atelier-white-cross-mold.webp') }}"
                                 alt="Matriță albă în formă de cruce și instrumente de atelier"
                                 width="900" height="1200"
                                 loading="lazy" decoding="async"
                                 class="aspect-[3/4] w-full object-cover object-[52%_82%]">
                        </figure>

                        <div class="absolute bottom-[5%] left-[4%] z-40 hidden h-24 w-24 items-center justify-center rounded-full border border-[#b68b45]/50 bg-[#f7f0e5]/95 text-center shadow-xl backdrop-blur xl:flex">
                            <div>
                                <p class="text-[8px] font-semibold uppercase tracking-[0.16em] text-[#a37639]">Din atelierul</p>
                                <p class="mt-1 font-serif text-base tracking-[0.1em] text-dark-brown">MTD ART</p>
                                <span class="mt-0.5 block text-[#bf8179]" aria-hidden="true">♡</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative border-t border-black/5 bg-[#f8f2e8]/96">
            <div class="mx-auto grid h-full max-w-7xl grid-cols-2 divide-x divide-dark-brown/10 px-4 md:grid-cols-4 md:px-8">
                @foreach([
                    ['Pigmenți de calitate', 'Nuanțe intense și rezistente'],
                    ['Elemente naturale', 'Flori, lemn și minerale'],
                    ['Turnat manual', 'Fiecare detaliu contează'],
                    ['Cadouri cu semnificație', 'Pentru momente speciale'],
                ] as [$title, $description])
                    <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">{{ $title }}</p>
                        <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">{{ $description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
