<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTD ART Hero Section</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hero-puzzle-stage {
            --gap: 14px;
            background:
                radial-gradient(circle at 16% 14%, rgba(244, 238, 228, 0.62), transparent 23%),
                radial-gradient(circle at 82% 22%, rgba(215, 179, 111, 0.18), transparent 20%),
                linear-gradient(180deg, #ddd1c3 0%, #d7cbbe 100%);
        }

        .hero-puzzle-piece {
            position: absolute;
            margin: 0;
            filter: drop-shadow(0 22px 28px rgba(76, 49, 28, 0.16));
            transition: transform 320ms ease, filter 320ms ease;
            transform-origin: center;
        }

        .hero-puzzle-piece:hover {
            transform: translateY(-5px) rotate(0deg) !important;
            filter: drop-shadow(0 28px 36px rgba(76, 49, 28, 0.22));
        }

        .hero-puzzle-piece__svg {
            display: block;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .hero-puzzle-piece image {
            transform-origin: center;
        }

        /* NEW PUZZLE PIECE POSITIONS AND SIZES (DESKTOP) */
        /* Each piece is distinct in size and position, creating gaps and some overlaps. Shapes do not interlock. */

        .hero-puzzle-piece--shelf {
            left: 3%;
            top: 6%;
            width: 28%;
            height: auto;
            aspect-ratio: 0.6;
            transform: rotate(-1.5deg);
        }

        .hero-puzzle-piece--tools {
            right: 4%;
            top: 4%;
            width: 38%;
            height: auto;
            aspect-ratio: 1.5;
            transform: rotate(1.2deg);
        }

        .hero-puzzle-piece--prayer {
            right: 48%;
            top: 25%;
            width: 18%;
            height: auto;
            aspect-ratio: 1.0;
            transform: rotate(-1deg);
        }

        .hero-puzzle-piece--red-cross {
            left: 20%;
            top: 55%;
            width: 22%;
            height: auto;
            aspect-ratio: 0.9;
            transform: rotate(-2.5deg);
        }

        .hero-puzzle-piece--cured {
            right: 25%;
            bottom: 6%;
            width: 16%;
            height: auto;
            aspect-ratio: 0.8;
            transform: rotate(1.8deg);
        }

        .hero-puzzle-piece--white-cross {
            left: 6%;
            bottom: 12%;
            width: 14%;
            height: auto;
            aspect-ratio: 1.1;
            transform: rotate(-1.3deg);
        }

        .hero-puzzle-badge {
            left: 45%;
            bottom: 22%;
        }

        /* MOBILE / STACKED COLLAGE POSITIONS (approximate, since shapes changed) */
        @media (max-width: 1023px) {
            .hero-puzzle-piece--shelf {
                left: 6%;
                top: 5%;
                width: 35%;
                height: auto;
                aspect-ratio: 0.6;
            }

            .hero-puzzle-piece--tools {
                left: 50%;
                top: 4%;
                width: 44%;
                height: auto;
                aspect-ratio: 1.5;
            }

            .hero-puzzle-piece--prayer {
                left: 60%;
                top: 32%;
                width: 20%;
                height: auto;
                aspect-ratio: 1.0;
            }

            .hero-puzzle-piece--red-cross {
                left: 8%;
                top: 52%;
                width: 30%;
                height: auto;
                aspect-ratio: 0.9;
            }

            .hero-puzzle-piece--cured {
                left: 45%;
                top: 50%;
                width: 24%;
                height: auto;
                aspect-ratio: 0.8;
            }

            .hero-puzzle-piece--white-cross {
                left: 74%;
                top: 54%;
                width: 21%;
                height: auto;
                aspect-ratio: 1.1;
            }
        }
    }
    </style>
</head>
<body class="bg-[#f4eee4] text-dark-brown antialiased">
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
                            <a href="#"
                               class="inline-flex min-h-11 items-center justify-center gap-3 rounded-md bg-[#344f3f] px-6 py-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:bg-[#2b4335] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#344f3f]/30 focus:ring-offset-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c0-5 2-8 6-10m-6 10c0-5-2-8-6-10" />
                                </svg>
                                Descoperă colecțiile
                            </a>
                            <a href="#"
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

                <div class="relative min-h-[720px] overflow-hidden bg-[#ddd1c3] lg:min-h-0">
                    <div class="hero-puzzle-stage absolute inset-0">
                        {{-- LEFT LARGE / SHELF --}}
                        <figure class="hero-puzzle-piece hero-puzzle-piece--shelf">
                            <svg class="hero-puzzle-piece__svg" viewBox="0 0 110 150" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <clipPath id="clip-shelf-piece">
                                        <path d="M14 8H74C79 8 82 12 82 17V31C82 35 85 37 89 37C96 37 101 42 101 49C101 56 96 61 89 61C85 61 82 63 82 67V101C82 105 85 107 89 107C96 107 101 112 101 119C101 126 96 131 89 131C85 131 82 133 82 137V139C82 144 78 148 73 148H14C9 148 5 144 5 139V17C5 12 9 8 14 8Z"/>
                                    </clipPath>
                                </defs>

                                <image
                                    href="img/hero/atelier-shelf.webp"
                                    width="110"
                                    height="150"
                                    preserveAspectRatio="xMidYMid slice"
                                    clip-path="url(#clip-shelf-piece)" />

                                <path
                                    d="M14 8H74C79 8 82 12 82 17V31C82 35 85 37 89 37C96 37 101 42 101 49C101 56 96 61 89 61C85 61 82 63 82 67V101C82 105 85 107 89 107C96 107 101 112 101 119C101 126 96 131 89 131C85 131 82 133 82 137V139C82 144 78 148 73 148H14C9 148 5 144 5 139V17C5 12 9 8 14 8Z"
                                    fill="none"
                                    stroke="rgba(255,248,240,.96)"
                                    stroke-width="4"
                                    stroke-linejoin="round"/>
                            </svg>
                        </figure>

                        {{-- TOP CENTER / TOOLS --}}
                        <figure class="hero-puzzle-piece hero-puzzle-piece--tools">
                            <svg class="hero-puzzle-piece__svg" viewBox="0 0 160 102" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <clipPath id="clip-tools-piece">
                                        <path d="M18 8H131C136 8 140 12 140 17V34C140 38 143 40 147 40C154 40 159 45 159 51C159 58 154 63 147 63C143 63 140 65 140 69V76C140 81 136 85 131 85H93C89 85 87 87 87 91C87 98 82 103 76 103C69 103 64 98 64 91C64 87 62 85 58 85H18C13 85 9 81 9 76V58C9 54 11 52 15 52C22 52 27 48 27 42C27 35 22 31 15 31C11 31 9 29 9 25V17C9 12 13 8 18 8Z"/>
                                    </clipPath>
                                </defs>

                                <image
                                    href="img/hero/atelier-tools.webp"
                                    width="160"
                                    height="102"
                                    preserveAspectRatio="xMidYMid slice"
                                    clip-path="url(#clip-tools-piece)" />

                                <path
                                    d="M18 8H131C136 8 140 12 140 17V34C140 38 143 40 147 40C154 40 159 45 159 51C159 58 154 63 147 63C143 63 140 65 140 69V76C140 81 136 85 131 85H93C89 85 87 87 87 91C87 98 82 103 76 103C69 103 64 98 64 91C64 87 62 85 58 85H18C13 85 9 81 9 76V58C9 54 11 52 15 52C22 52 27 48 27 42C27 35 22 31 15 31C11 31 9 29 9 25V17C9 12 13 8 18 8Z"
                                    fill="none"
                                    stroke="rgba(255,248,240,.96)"
                                    stroke-width="4"
                                    stroke-linejoin="round"/>
                            </svg>
                        </figure>

                        {{-- TOP RIGHT / PRAYER --}}
                        <figure class="hero-puzzle-piece hero-puzzle-piece--prayer">
                            <svg class="hero-puzzle-piece__svg" viewBox="0 0 92 130" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <clipPath id="clip-prayer-piece">
                                        <path d="M15 8H77C82 8 86 12 86 17V106C86 111 82 115 77 115H58C54 115 52 117 52 121C52 127 47 132 41 132C35 132 30 127 30 121C30 117 28 115 24 115H15C10 115 6 111 6 106V67C6 63 8 61 12 61C19 61 24 56 24 50C24 43 19 38 12 38C8 38 6 36 6 32V17C6 12 10 8 15 8Z"/>
                                    </clipPath>
                                </defs>

                                <image
                                    href="img/hero/atelier-prayer-mold.webp"
                                    width="92"
                                    height="130"
                                    preserveAspectRatio="xMidYMid slice"
                                    clip-path="url(#clip-prayer-piece)" />

                                <path
                                    d="M15 8H77C82 8 86 12 86 17V106C86 111 82 115 77 115H58C54 115 52 117 52 121C52 127 47 132 41 132C35 132 30 127 30 121C30 117 28 115 24 115H15C10 115 6 111 6 106V67C6 63 8 61 12 61C19 61 24 56 24 50C24 43 19 38 12 38C8 38 6 36 6 32V17C6 12 10 8 15 8Z"
                                    fill="none"
                                    stroke="rgba(255,248,240,.96)"
                                    stroke-width="4"
                                    stroke-linejoin="round"/>
                            </svg>
                        </figure>

                        {{-- BOTTOM LEFT / RED CROSS --}}
                        <figure class="hero-puzzle-piece hero-puzzle-piece--red-cross">
                            <svg class="hero-puzzle-piece__svg" viewBox="0 0 120 130" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <clipPath id="clip-red-cross-piece">
                                        <path d="M15 18H40C44 18 46 20 46 24C46 30 51 35 58 35C64 35 69 30 69 24C69 20 71 18 75 18H96C101 18 105 22 105 27V63C105 67 108 69 112 69C118 69 123 74 123 80C123 86 118 91 112 91C108 91 105 93 105 97V114C105 119 101 123 96 123H15C10 123 6 119 6 114V27C6 22 10 18 15 18Z"/>
                                    </clipPath>
                                </defs>

                                <image
                                    href="img/hero/atelier-red-cross.webp"
                                    width="120"
                                    height="130"
                                    preserveAspectRatio="xMidYMid slice"
                                    clip-path="url(#clip-red-cross-piece)" />

                                <path
                                    d="M15 18H40C44 18 46 20 46 24C46 30 51 35 58 35C64 35 69 30 69 24C69 20 71 18 75 18H96C101 18 105 22 105 27V63C105 67 108 69 112 69C118 69 123 74 123 80C123 86 118 91 112 91C108 91 105 93 105 97V114C105 119 101 123 96 123H15C10 123 6 119 6 114V27C6 22 10 18 15 18Z"
                                    fill="none"
                                    stroke="rgba(255,248,240,.96)"
                                    stroke-width="4"
                                    stroke-linejoin="round"/>
                            </svg>
                        </figure>

                        {{-- BOTTOM CENTER / CURED --}}
                        <figure class="hero-puzzle-piece hero-puzzle-piece--cured">
                            <svg class="hero-puzzle-piece__svg" viewBox="0 0 96 122" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <clipPath id="clip-cured-piece">
                                        <path d="M18 8H78C83 8 87 12 87 17V41C87 45 89 47 93 47C99 47 104 52 104 58C104 64 99 69 93 69C89 69 87 71 87 75V104C87 109 83 113 78 113H18C13 113 9 109 9 104V74C9 70 11 68 15 68C21 68 26 63 26 57C26 51 21 46 15 46C11 46 9 44 9 40V17C9 12 13 8 18 8Z"/>
                                    </clipPath>
                                </defs>

                                <image
                                    href="img/hero/atelier-cured-crosses.webp"
                                    width="96"
                                    height="122"
                                    preserveAspectRatio="xMidYMid slice"
                                    clip-path="url(#clip-cured-piece)" />

                                <path
                                    d="M18 8H78C83 8 87 12 87 17V41C87 45 89 47 93 47C99 47 104 52 104 58C104 64 99 69 93 69C89 69 87 71 87 75V104C87 109 83 113 78 113H18C13 113 9 109 9 104V74C9 70 11 68 15 68C21 68 26 63 26 57C26 51 21 46 15 46C11 46 9 44 9 40V17C9 12 13 8 18 8Z"
                                    fill="none"
                                    stroke="rgba(255,248,240,.96)"
                                    stroke-width="4"
                                    stroke-linejoin="round"/>
                            </svg>
                        </figure>

                        {{-- BOTTOM RIGHT / WHITE CROSS --}}
                        <figure class="hero-puzzle-piece hero-puzzle-piece--white-cross">
                            <svg class="hero-puzzle-piece__svg" viewBox="0 0 90 118" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <clipPath id="clip-white-cross-piece">
                                        <path d="M16 18H36C40 18 42 20 42 24C42 30 47 35 53 35C60 35 65 30 65 24C65 20 67 18 71 18H74C79 18 83 22 83 27V103C83 108 79 112 74 112H16C11 112 7 108 7 103V73C7 69 9 67 13 67C19 67 24 62 24 56C24 50 19 45 13 45C9 45 7 43 7 39V27C7 22 11 18 16 18Z"/>
                                    </clipPath>
                                </defs>

                                <image
                                    href="img/hero/atelier-white-cross-mold.webp"
                                    width="90"
                                    height="118"
                                    preserveAspectRatio="xMidYMid slice"
                                    clip-path="url(#clip-white-cross-piece)" />

                                <path
                                    d="M16 18H36C40 18 42 20 42 24C42 30 47 35 53 35C60 35 65 30 65 24C65 20 67 18 71 18H74C79 18 83 22 83 27V103C83 108 79 112 74 112H16C11 112 7 108 7 103V73C7 69 9 67 13 67C19 67 24 62 24 56C24 50 19 45 13 45C9 45 7 43 7 39V27C7 22 11 18 16 18Z"
                                    fill="none"
                                    stroke="rgba(255,248,240,.96)"
                                    stroke-width="4"
                                    stroke-linejoin="round"/>
                            </svg>
                        </figure>

                        <div class="hero-puzzle-badge absolute z-40 hidden h-24 w-24 items-center justify-center rounded-full border border-[#b68b45]/55 bg-[#f7f0e5]/95 text-center shadow-xl backdrop-blur xl:flex">
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
</body>
</html>