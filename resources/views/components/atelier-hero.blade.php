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

            <div class="relative min-h-[720px] overflow-hidden bg-[#ddd1c3] lg:min-h-0">
                @php
                    $heroPuzzlePieces = [
                        [
                            'key' => 'shelf',
                            'class' => 'hero-puzzle-piece--shelf',
                            'src' => asset('img/hero/atelier-shelf.webp'),
                            'alt' => 'Raftul atelierului MTD Art cu pigmenți și materiale pentru rășină',
                            'position' => '50% 50%',
                            'loading' => 'eager',
                            'priority' => 'high',
                            'path' => 'M8 7 H36 C36 2 41 1 47 1 C53 1 58 3 58 7 H91 Q97 7 97 13 V36 C89 36 85 41 85 48 C85 55 89 60 97 60 V89 Q97 96 90 96 H61 C61 88 56 84 49 84 C42 84 37 88 37 96 H10 Q3 96 3 89 V63 C11 63 16 58 16 51 C16 44 11 39 3 39 V14 Q3 7 8 7 Z',
                        ],
                        [
                            'key' => 'tools',
                            'class' => 'hero-puzzle-piece--tools',
                            'src' => asset('img/hero/atelier-tools.webp'),
                            'alt' => 'Unelte, recipiente și accesorii folosite la turnarea rășinii',
                            'position' => '50% 50%',
                            'loading' => 'eager',
                            'priority' => 'auto',
                            'path' => 'M10 5 H39 C39 13 44 17 51 17 C58 17 63 13 63 5 H90 Q96 5 96 12 V38 C89 38 85 43 85 50 C85 57 89 62 96 62 V89 Q96 96 89 96 H64 C64 89 59 85 52 85 C45 85 40 89 40 96 H11 Q4 96 4 89 V65 C12 65 17 60 17 53 C17 46 12 41 4 41 V12 Q4 5 10 5 Z',
                        ],
                        [
                            'key' => 'prayer',
                            'class' => 'hero-puzzle-piece--prayer',
                            'src' => asset('img/hero/atelier-prayer-mold.webp'),
                            'alt' => 'Matriță albastră cu motivul mâinilor împreunate',
                            'position' => '50% 48%',
                            'loading' => 'eager',
                            'priority' => 'auto',
                            'path' => 'M9 4 H37 C37 12 42 16 49 16 C56 16 61 12 61 4 H90 Q96 4 96 11 V39 C88 39 84 44 84 51 C84 58 88 63 96 63 V90 Q96 96 89 96 H61 C61 88 56 84 49 84 C42 84 37 88 37 96 H10 Q4 96 4 89 V61 C12 61 16 56 16 49 C16 42 12 37 4 37 V11 Q4 4 9 4 Z',
                        ],
                        [
                            'key' => 'red-cross',
                            'class' => 'hero-puzzle-piece--red-cross',
                            'src' => asset('img/hero/atelier-red-cross.webp'),
                            'alt' => 'Cruce roșie din rășină aflată în procesul de turnare',
                            'position' => '50% 54%',
                            'loading' => 'lazy',
                            'priority' => 'auto',
                            'path' => 'M10 6 H38 C38 14 43 18 50 18 C57 18 62 14 62 6 H89 Q96 6 96 13 V37 C88 37 83 42 83 49 C83 56 88 61 96 61 V89 Q96 96 89 96 H64 C64 88 59 83 52 83 C45 83 40 88 40 96 H11 Q4 96 4 89 V65 C12 65 17 60 17 53 C17 46 12 41 4 41 V13 Q4 6 10 6 Z',
                        ],
                        [
                            'key' => 'cured',
                            'class' => 'hero-puzzle-piece--cured',
                            'src' => asset('img/hero/atelier-cured-crosses.webp'),
                            'alt' => 'Piese în formă de cruce scoase din matrițe și pregătite pentru finisare',
                            'position' => '50% 50%',
                            'loading' => 'lazy',
                            'priority' => 'auto',
                            'path' => 'M10 4 H40 C40 12 45 16 52 16 C59 16 64 12 64 4 H90 Q96 4 96 11 V40 C88 40 84 45 84 52 C84 59 88 64 96 64 V90 Q96 96 89 96 H62 C62 88 57 84 50 84 C43 84 38 88 38 96 H10 Q4 96 4 89 V62 C12 62 16 57 16 50 C16 43 12 38 4 38 V11 Q4 4 10 4 Z',
                        ],
                        [
                            'key' => 'white-cross',
                            'class' => 'hero-puzzle-piece--white-cross',
                            'src' => asset('img/hero/atelier-white-cross-mold.webp'),
                            'alt' => 'Matriță albă în formă de cruce și instrumente de atelier',
                            'position' => '52% 82%',
                            'loading' => 'lazy',
                            'priority' => 'auto',
                            'path' => 'M9 5 H36 C36 13 41 17 48 17 C55 17 60 13 60 5 H89 Q96 5 96 12 V39 C88 39 84 44 84 51 C84 58 88 63 96 63 V89 Q96 96 89 96 H65 C65 88 60 84 53 84 C46 84 41 88 41 96 H10 Q4 96 4 89 V64 C12 64 17 59 17 52 C17 45 12 40 4 40 V12 Q4 5 9 5 Z',
                        ],
                    ];
                @endphp

                <div class="hero-puzzle-stage absolute inset-0" aria-label="Colaj din atelierul MTD Art">
                    <svg class="absolute h-0 w-0" width="0" height="0" aria-hidden="true" focusable="false">
                        <defs>
                            @foreach($heroPuzzlePieces as $piece)
                                <clipPath
                                    id="hero-puzzle-clip-{{ $piece['key'] }}"
                                    clipPathUnits="objectBoundingBox"
                                >
                                    <path d="{{ $piece['path'] }}" transform="scale(0.01)" />
                                </clipPath>
                            @endforeach
                        </defs>
                    </svg>

                    @foreach($heroPuzzlePieces as $piece)
                        <figure class="hero-puzzle-piece {{ $piece['class'] }}">
                            <img
                                src="{{ $piece['src'] }}"
                                alt="{{ $piece['alt'] }}"
                                loading="{{ $piece['loading'] }}"
                                fetchpriority="{{ $piece['priority'] }}"
                                decoding="async"
                                class="hero-puzzle-piece__image"
                                style="object-position: {{ $piece['position'] }}; clip-path: url(#hero-puzzle-clip-{{ $piece['key'] }}); -webkit-clip-path: url(#hero-puzzle-clip-{{ $piece['key'] }});"
                            >

                            <div
                                class="hero-puzzle-piece__shine"
                                style="clip-path: url(#hero-puzzle-clip-{{ $piece['key'] }}); -webkit-clip-path: url(#hero-puzzle-clip-{{ $piece['key'] }});"
                                aria-hidden="true"
                            ></div>

                            <svg
                                class="hero-puzzle-piece__outline-svg"
                                viewBox="0 0 100 100"
                                preserveAspectRatio="none"
                                aria-hidden="true"
                                focusable="false"
                            >
                                <path
                                    d="{{ $piece['path'] }}"
                                    class="hero-puzzle-piece__outline hero-puzzle-piece__outline--outer"
                                />
                                <path
                                    d="{{ $piece['path'] }}"
                                    class="hero-puzzle-piece__outline hero-puzzle-piece__outline--inner"
                                />
                            </svg>
                        </figure>
                    @endforeach

                    <div class="hero-puzzle-seal absolute left-[46%] top-[45%] z-40 hidden h-24 w-24 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-[#b68b45]/55 bg-[#f7f0e5]/95 text-center shadow-xl backdrop-blur xl:flex">
                        <div>
                            <p class="text-[8px] font-semibold uppercase tracking-[0.16em] text-[#a37639]">Din atelierul</p>
                            <p class="mt-1 font-serif text-base tracking-[0.1em] text-dark-brown">MTD ART</p>
                            <span class="mt-0.5 block text-[#bf8179]" aria-hidden="true">♡</span>
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

<style>
    .hero-puzzle-stage {
        isolation: isolate;
        background:
            radial-gradient(circle at 14% 12%, rgba(248, 242, 232, 0.82), transparent 24%),
            radial-gradient(circle at 84% 18%, rgba(215, 179, 111, 0.22), transparent 20%),
            radial-gradient(circle at 56% 84%, rgba(191, 129, 121, 0.12), transparent 22%),
            linear-gradient(145deg, #e5dbcf 0%, #d9cdbf 54%, #d2c3b4 100%);
    }

    .hero-puzzle-stage::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        opacity: 0.28;
        background-image:
            linear-gradient(rgba(77, 53, 37, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(77, 53, 37, 0.05) 1px, transparent 1px);
        background-size: 52px 52px;
        mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.6), transparent 88%);
    }

    .hero-puzzle-piece {
        --piece-transform: rotate(0deg);
        position: absolute;
        z-index: 10;
        margin: 0;
        filter:
            drop-shadow(0 24px 24px rgba(69, 45, 29, 0.14))
            drop-shadow(0 7px 8px rgba(69, 45, 29, 0.10));
        transform: var(--piece-transform);
        transform-origin: center;
        transition:
            transform 550ms cubic-bezier(0.22, 1, 0.36, 1),
            filter 550ms ease;
        will-change: transform;
    }

    .hero-puzzle-piece:hover {
        z-index: 35;
        filter:
            drop-shadow(0 34px 32px rgba(69, 45, 29, 0.20))
            drop-shadow(0 10px 12px rgba(69, 45, 29, 0.12));
        transform: var(--piece-transform) translateY(-8px) scale(1.025);
    }

    .hero-puzzle-piece__image,
    .hero-puzzle-piece__shine,
    .hero-puzzle-piece__outline-svg {
        position: absolute;
        inset: 0;
        display: block;
        height: 100%;
        width: 100%;
    }

    .hero-puzzle-piece__image {
        object-fit: cover;
        background: #f8f2e8;
    }

    .hero-puzzle-piece__shine {
        pointer-events: none;
        background:
            linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent 42%),
            linear-gradient(315deg, rgba(74, 48, 31, 0.09), transparent 36%);
    }

    .hero-puzzle-piece__outline-svg {
        overflow: visible;
        pointer-events: none;
    }

    .hero-puzzle-piece__outline {
        fill: none;
        pointer-events: none;
        vector-effect: non-scaling-stroke;
    }

    .hero-puzzle-piece__outline--outer {
        stroke: rgba(255, 255, 255, 0.96);
        stroke-width: 7px;
        stroke-linejoin: round;
    }

    .hero-puzzle-piece__outline--inner {
        stroke: rgba(89, 60, 40, 0.18);
        stroke-width: 1px;
        stroke-linejoin: round;
    }

    /* Dimensiuni intenționat diferite și mici spații între piese.
       Formele nu sunt aliniate pentru a se îmbina perfect. */
    .hero-puzzle-piece--shelf {
        --piece-transform: rotate(-2.1deg);
        left: 1.8%;
        top: 4.5%;
        height: 91%;
        width: 34%;
        z-index: 8;
    }

    .hero-puzzle-piece--tools {
        --piece-transform: rotate(1.4deg);
        left: 37.5%;
        top: 4%;
        height: 39%;
        width: 37%;
        z-index: 12;
    }

    .hero-puzzle-piece--prayer {
        --piece-transform: rotate(3.2deg);
        right: 2.8%;
        top: 7.5%;
        height: 34%;
        width: 22%;
        z-index: 16;
    }

    .hero-puzzle-piece--red-cross {
        --piece-transform: rotate(-1.6deg);
        bottom: 4.5%;
        left: 38.5%;
        height: 48%;
        width: 30.5%;
        z-index: 14;
    }

    .hero-puzzle-piece--cured {
        --piece-transform: rotate(2.5deg);
        right: 3.8%;
        top: 43%;
        height: 30%;
        width: 24%;
        z-index: 22;
    }

    .hero-puzzle-piece--white-cross {
        --piece-transform: rotate(-3deg);
        bottom: 2.5%;
        right: 10.5%;
        height: 26%;
        width: 19.5%;
        z-index: 18;
    }

    .hero-puzzle-seal {
        transition: transform 400ms ease, box-shadow 400ms ease;
    }

    .hero-puzzle-seal:hover {
        transform: translate(-50%, -50%) rotate(-3deg) scale(1.04);
        box-shadow: 0 22px 42px rgba(76, 49, 28, 0.18);
    }

    @media (max-width: 1279px) {
        .hero-puzzle-piece__outline--outer {
            stroke-width: 5px;
        }

        .hero-puzzle-piece--shelf {
            left: 1%;
            width: 35%;
        }

        .hero-puzzle-piece--tools {
            left: 37%;
            width: 38%;
        }

        .hero-puzzle-piece--prayer {
            right: 1.5%;
            width: 23%;
        }
    }

    @media (max-width: 1023px) {
        .hero-puzzle-stage {
            min-height: 720px;
        }

        .hero-puzzle-piece:hover {
            transform: var(--piece-transform);
        }

        .hero-puzzle-piece--shelf {
            --piece-transform: rotate(-2deg);
            left: 1.5%;
            top: 3%;
            height: 43%;
            width: 45%;
        }

        .hero-puzzle-piece--tools {
            --piece-transform: rotate(1.8deg);
            left: auto;
            right: 1.5%;
            top: 2.5%;
            height: 31%;
            width: 48%;
        }

        .hero-puzzle-piece--prayer {
            --piece-transform: rotate(3deg);
            right: 8%;
            top: 31%;
            height: 27%;
            width: 27%;
        }

        .hero-puzzle-piece--red-cross {
            --piece-transform: rotate(-1.5deg);
            bottom: auto;
            left: 2.5%;
            top: 46%;
            height: 43%;
            width: 42%;
        }

        .hero-puzzle-piece--cured {
            --piece-transform: rotate(2.4deg);
            right: 1.5%;
            top: 58%;
            height: 29%;
            width: 31%;
        }

        .hero-puzzle-piece--white-cross {
            --piece-transform: rotate(-2.8deg);
            bottom: 1%;
            right: 30%;
            height: 25%;
            width: 25%;
        }
    }

    @media (max-width: 639px) {
        .hero-puzzle-stage {
            min-height: 640px;
        }

        .hero-puzzle-piece {
            filter:
                drop-shadow(0 18px 18px rgba(69, 45, 29, 0.13))
                drop-shadow(0 5px 6px rgba(69, 45, 29, 0.09));
        }

        .hero-puzzle-piece__outline--outer {
            stroke-width: 3px;
        }

        .hero-puzzle-piece--shelf {
            height: 41%;
            width: 46%;
        }

        .hero-puzzle-piece--tools {
            height: 30%;
            width: 49%;
        }

        .hero-puzzle-piece--prayer {
            right: 6%;
            top: 30%;
            height: 26%;
            width: 29%;
        }

        .hero-puzzle-piece--red-cross {
            top: 44%;
            height: 44%;
            width: 44%;
        }

        .hero-puzzle-piece--cured {
            top: 57%;
            height: 29%;
            width: 33%;
        }

        .hero-puzzle-piece--white-cross {
            right: 28%;
            height: 24%;
            width: 27%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .hero-puzzle-piece,
        .hero-puzzle-seal {
            transition: none;
        }

        .hero-puzzle-piece:hover {
            transform: var(--piece-transform);
        }
    }
</style>