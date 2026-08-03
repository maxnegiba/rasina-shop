@php
    /*
     * The coordinates are percentages of the collage stage.
     * Every image has its own footprint so the composition stays intentionally irregular.
     */
    $puzzlePaths = [
        'a' => 'M8 4H34C38 4 40 6 40 10C40 17 45 22 52 22C59 22 64 17 64 10C64 6 66 4 70 4H92C96 4 98 6 98 10V35C98 39 96 41 92 41C85 41 80 46 80 53C80 60 85 65 92 65C96 65 98 67 98 71V90C98 94 96 96 92 96H65C61 96 59 94 59 90C59 83 54 78 47 78C40 78 35 83 35 90C35 94 33 96 29 96H8C4 96 2 94 2 90V67C2 63 4 61 8 61C15 61 20 56 20 49C20 42 15 37 8 37C4 37 2 35 2 31V10C2 6 4 4 8 4Z',
        'b' => 'M8 14H32C36 14 38 12 38 8C38 4 42 2 48 2C54 2 58 4 58 8C58 12 60 14 64 14H92C96 14 98 16 98 20V38C98 42 96 44 92 44C85 44 80 49 80 56C80 63 85 68 92 68C96 68 98 70 98 74V86H70C66 86 64 88 64 92C64 96 60 98 54 98C48 98 44 96 44 92C44 88 42 86 38 86H8C4 86 2 84 2 80V58C2 54 4 52 8 52C15 52 20 47 20 40C20 33 15 28 8 28C4 28 2 26 2 22V20C2 16 4 14 8 14Z',
        'c' => 'M8 4H30C34 4 36 6 36 10C36 17 41 22 48 22C55 22 60 17 60 10C60 6 62 4 66 4H92C96 4 98 6 98 10V90C98 94 96 96 92 96H72C68 96 66 94 66 90C66 83 61 78 54 78C47 78 42 83 42 90C42 94 40 96 36 96H8C4 96 2 94 2 90V70C2 66 4 64 8 64C15 64 20 59 20 52C20 45 15 40 8 40C4 40 2 38 2 34V10C2 6 4 4 8 4Z',
        'd' => 'M8 4H92C96 4 98 6 98 10V34C98 38 96 40 92 40C85 40 80 45 80 52C80 59 85 64 92 64C96 64 98 66 98 70V90C98 94 96 96 92 96H68C64 96 62 94 62 90C62 83 57 78 50 78C43 78 38 83 38 90C38 94 36 96 32 96H8C4 96 2 94 2 90V66C2 62 4 60 8 60C15 60 20 55 20 48C20 41 15 36 8 36C4 36 2 34 2 30V10C2 6 4 4 8 4Z',
        'e' => 'M8 14H34C38 14 40 12 40 8C40 4 44 2 50 2C56 2 60 4 60 8C60 12 62 14 66 14H92C96 14 98 16 98 20V90C98 94 96 96 92 96H68C64 96 62 94 62 90C62 83 57 78 50 78C43 78 38 83 38 90C38 94 36 96 32 96H8C4 96 2 94 2 90V70C2 66 4 64 8 64C15 64 20 59 20 52C20 45 15 40 8 40C4 40 2 38 2 34V20C2 16 4 14 8 14Z',
        'f' => 'M8 4H32C36 4 38 6 38 10C38 17 43 22 50 22C57 22 62 17 62 10C62 6 64 4 68 4H92C96 4 98 6 98 10V32C98 36 96 38 92 38C85 38 80 43 80 50C80 57 85 62 92 62C96 62 98 64 98 68V90C98 94 96 96 92 96H8C4 96 2 94 2 90V68C2 64 4 62 8 62C15 62 20 57 20 50C20 43 15 38 8 38C4 38 2 36 2 32V10C2 6 4 4 8 4Z',
    ];

    $componentId = 'atelier-puzzle-' . uniqid();

    $pieces = [
        [
            'key' => 'pigments',
            'file' => 'pigments-closeup.webp',
            'alt' => 'Pigmenți sidefați și fulgi decorativi pentru rășină',
            'shape' => 'b',
            'style' => '--x:1%;--y:1%;--w:25%;--h:29%;--r:-1.1deg;--mc:2;--mr:7;',
        ],
        [
            'key' => 'red-cross',
            'file' => 'red-cross.webp',
            'alt' => 'Cruce roșie din rășină în curs de realizare',
            'shape' => 'c',
            'style' => '--x:25.5%;--y:0.5%;--w:18.5%;--h:40%;--r:.8deg;--mc:1;--mr:9;',
        ],
        [
            'key' => 'red-mold',
            'file' => 'red-resin-mold.webp',
            'alt' => 'Rășină roșie turnată într-o matriță rotundă',
            'shape' => 'e',
            'style' => '--x:43.5%;--y:1.5%;--w:20.5%;--h:29%;--r:-.7deg;--mc:1;--mr:6;',
        ],
        [
            'key' => 'shelf',
            'file' => 'atelier-shelf.webp',
            'alt' => 'Raftul cu materiale din atelierul MTD ART',
            'shape' => 'a',
            'style' => '--x:64%;--y:0%;--w:20.5%;--h:42%;--r:.6deg;--mc:1;--mr:10;',
        ],
        [
            'key' => 'prayer',
            'file' => 'prayer-mold.webp',
            'alt' => 'Matriță albastră în formă de mâini împreunate',
            'shape' => 'd',
            'style' => '--x:84%;--y:1%;--w:15.5%;--h:31%;--r:-.8deg;--mc:1;--mr:8;',
        ],
        [
            'key' => 'cured-crosses',
            'file' => 'cured-crosses.webp',
            'alt' => 'Cruci din rășină scoase din matrițe',
            'shape' => 'f',
            'style' => '--x:1%;--y:30%;--w:22%;--h:34%;--r:1deg;--mc:1;--mr:7;',
        ],
        [
            'key' => 'tools-wide',
            'file' => 'tools-overview.webp',
            'alt' => 'Instrumente și recipiente pentru turnarea rășinii',
            'shape' => 'a',
            'style' => '--x:22.5%;--y:40.5%;--w:29%;--h:24.5%;--r:-.6deg;--mc:2;--mr:6;',
        ],
        [
            'key' => 'natural-materials',
            'file' => 'natural-materials.webp',
            'alt' => 'Flori și materiale naturale pregătite pentru incluziuni',
            'shape' => 'd',
            'style' => '--x:50.8%;--y:30.5%;--w:28.5%;--h:28.5%;--r:.5deg;--mc:1;--mr:7;',
        ],
        [
            'key' => 'glitters',
            'file' => 'glitters.webp',
            'alt' => 'Recipiente cu glitter în nuanțe pastelate',
            'shape' => 'c',
            'style' => '--x:79%;--y:31%;--w:20.5%;--h:28.5%;--r:-.9deg;--mc:1;--mr:6;',
        ],
        [
            'key' => 'wood-rounds',
            'file' => 'wood-rounds.webp',
            'alt' => 'Felii rotunde de lemn pregătite pentru lucru',
            'shape' => 'e',
            'style' => '--x:1%;--y:64%;--w:19.5%;--h:35%;--r:-.8deg;--mc:1;--mr:8;',
        ],
        [
            'key' => 'bottles',
            'file' => 'pigment-bottles.webp',
            'alt' => 'Colecție de pigmenți colorați din atelier',
            'shape' => 'b',
            'style' => '--x:20%;--y:64.2%;--w:27.5%;--h:34.5%;--r:.7deg;--mc:2;--mr:7;',
        ],
        [
            'key' => 'resin-tools',
            'file' => 'resin-tools.webp',
            'alt' => 'Unelte și matrițe folosite pentru piesele din rășină',
            'shape' => 'f',
            'style' => '--x:47%;--y:58.5%;--w:20.5%;--h:40%;--r:1.1deg;--mc:1;--mr:9;',
        ],
        [
            'key' => 'wood-slices',
            'file' => 'wood-slices.webp',
            'alt' => 'Felii naturale de lemn de diferite dimensiuni',
            'shape' => 'a',
            'style' => '--x:67%;--y:59.5%;--w:32%;--h:39%;--r:-.5deg;--mc:1;--mr:8;',
        ],
    ];
@endphp

@once
    <style>
        .atelier-puzzle-stage {
            background:
                radial-gradient(circle at 17% 14%, rgba(255, 250, 242, .78), transparent 26%),
                radial-gradient(circle at 82% 19%, rgba(183, 139, 69, .14), transparent 23%),
                linear-gradient(145deg, #e4d9cc 0%, #d8cbbd 53%, #d1c1b2 100%);
            position: relative;
            isolation: isolate;
            overflow: hidden;
        }

        .atelier-puzzle-stage::before {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            content: '';
            background:
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, .38) 0 1px, transparent 2px),
                radial-gradient(circle at 74% 62%, rgba(255, 255, 255, .22) 0 1px, transparent 2px),
                linear-gradient(120deg, rgba(255, 255, 255, .24), transparent 38%, rgba(173, 118, 75, .12) 72%, transparent);
            background-size: 34px 34px, 46px 46px, 100% 100%;
            opacity: .72;
        }

        .atelier-puzzle-stage::after {
            position: absolute;
            inset: 0;
            z-index: 35;
            pointer-events: none;
            content: '';
            background: linear-gradient(90deg, rgba(68, 48, 34, .08), transparent 12%, transparent 88%, rgba(68, 48, 34, .06));
            mix-blend-mode: multiply;
        }

        .atelier-puzzle-piece {
            position: absolute;
            left: var(--x);
            top: var(--y);
            z-index: 10;
            overflow: visible;
            width: var(--w);
            height: var(--h);
            margin: 0;
            transform: rotate(var(--r));
            transform-origin: center;
            filter: drop-shadow(0 12px 14px rgba(69, 45, 27, .17));
            transition: transform 350ms cubic-bezier(.2, .7, .2, 1), filter 350ms ease, z-index 0s 350ms;
            will-change: transform;
        }

        .atelier-puzzle-piece:hover {
            z-index: 25;
            transform: translateY(-5px) rotate(0deg) scale(1.018);
            filter: drop-shadow(0 20px 22px rgba(69, 45, 27, .25));
            transition-delay: 0s;
        }

        .atelier-puzzle-piece svg {
            display: block;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .atelier-puzzle-piece__edge {
            fill: none;
            stroke: rgba(255, 250, 243, .94);
            stroke-width: 2.35;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }

        .atelier-puzzle-piece__inner-edge {
            fill: none;
            stroke: rgba(112, 76, 47, .2);
            stroke-width: .7;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }

        @media (max-width: 1023px) {
            .atelier-puzzle-stage {
                position: relative !important;
                inset: auto !important;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                grid-auto-flow: dense;
                grid-auto-rows: 18px;
                gap: 7px;
                min-height: 0;
                padding: 16px;
                overflow: visible;
            }

            .atelier-puzzle-piece {
                position: relative;
                left: auto;
                top: auto;
                width: auto;
                height: auto;
                min-height: 150px;
                aspect-ratio: 1 / 1;
                grid-column: span var(--mc);
                grid-row: span var(--mr);
            }
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            .atelier-puzzle-stage {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                grid-auto-rows: 22px;
                gap: 9px;
                padding: 22px;
            }

            .atelier-puzzle-piece {
                min-height: 180px;
                grid-column: span var(--mc);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .atelier-puzzle-piece {
                transition: none;
            }
        }
    </style>
@endonce

<section class="relative overflow-hidden border-b border-black/5 bg-[#f4eee4] lg:h-[calc(100svh-6rem)] lg:min-h-[680px] lg:max-h-[860px]">
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
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c0-5 2-8 6-10m-6 10c0-5-2-8-6-10" />
                            </svg>
                            Descoperă colecțiile
                        </a>
                        <a href="#atelier"
                           class="inline-flex min-h-11 items-center justify-center gap-3 rounded-md border border-[#b68b45]/70 bg-white/35 px-6 py-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-[#9a7037] transition duration-300 hover:-translate-y-0.5 hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-[#b68b45]/25 focus:ring-offset-2">
                            Intră în atelier <span aria-hidden="true">→</span>
                        </a>
                    </div>

                    <div class="mt-6 grid gap-4 border-t border-dark-brown/10 pt-4 sm:grid-cols-3 xl:mt-7">
                        <div class="flex gap-2.5">
                            <span class="text-lg leading-none text-[#bf8179]" aria-hidden="true">♡</span>
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.12em]">Unicat</p>
                                <p class="mt-1 text-[10px] leading-4 text-dark-brown/60">Fiecare piesă este diferită</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="text-lg leading-none text-[#57735f]" aria-hidden="true">⌁</span>
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.12em]">Lucrat manual</p>
                                <p class="mt-1 text-[10px] leading-4 text-dark-brown/60">Cu grijă și pasiune</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="text-lg leading-none text-[#b68b45]" aria-hidden="true">✧</span>
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.12em]">Materiale premium</p>
                                <p class="mt-1 text-[10px] leading-4 text-dark-brown/60">Rășină și pigmenți aleși</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="atelier" class="relative bg-[#ddd1c3] lg:min-h-0 lg:overflow-hidden">
                <div class="atelier-puzzle-stage relative lg:absolute lg:inset-0">
                    @foreach ($pieces as $piece)
                        @php($clipId = $componentId . '-' . $piece['key'])

                        <figure
                            class="atelier-puzzle-piece"
                            style="{{ $piece['style'] }}"
                            role="img"
                            aria-label="{{ $piece['alt'] }}"
                        >
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                <defs>
                                    <clipPath id="{{ $clipId }}" clipPathUnits="userSpaceOnUse">
                                        <path d="{{ $puzzlePaths[$piece['shape']] }}" />
                                    </clipPath>
                                </defs>

                                <image
                                    href="{{ asset('img/hero/' . $piece['file']) }}"
                                    x="0"
                                    y="0"
                                    width="100"
                                    height="100"
                                    preserveAspectRatio="xMidYMid slice"
                                    loading="lazy"
                                    clip-path="url(#{{ $clipId }})"
                                />

                                <path class="atelier-puzzle-piece__edge" d="{{ $puzzlePaths[$piece['shape']] }}" />
                                <path class="atelier-puzzle-piece__inner-edge" d="{{ $puzzlePaths[$piece['shape']] }}" />
                            </svg>
                        </figure>
                    @endforeach

                    <div class="absolute bottom-[4.5%] right-[3.5%] z-40 hidden h-24 w-24 items-center justify-center rounded-full border border-[#b68b45]/55 bg-[#f7f0e5]/95 text-center shadow-xl backdrop-blur xl:flex">
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
                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">Pigmenți de calitate</p>
                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">Nuanțe intense și rezistente</p>
                </div>
                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">Elemente naturale</p>
                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">Flori, lemn și minerale</p>
                </div>
                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">Turnat manual</p>
                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">Fiecare detaliu contează</p>
                </div>
                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">Cadouri cu semnificație</p>
                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">Pentru momente speciale</p>
                </div>
            </div>
        </div>
    </div>
</section>