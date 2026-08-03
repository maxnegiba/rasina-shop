@php
    /*
     * Each image uses an individual size and position.
     * The puzzle pieces remain close together without interlocking perfectly.
     *
     * The visible spaces between pieces are filled visually by the
     * translucent epoxy-resin layer underneath the collage.
     *
     * Expected image location:
     * public/img/hero/
     */

    $componentId = 'atelier-puzzle-' . uniqid();

    $puzzlePaths = [
        'a' => 'M8 4H34C38 4 40 6 40 10C40 17 45 22 52 22C59 22 64 17 64 10C64 6 66 4 70 4H92C96 4 98 6 98 10V35C98 39 96 41 92 41C85 41 80 46 80 53C80 60 85 65 92 65C96 65 98 67 98 71V90C98 94 96 96 92 96H65C61 96 59 94 59 90C59 83 54 78 47 78C40 78 35 83 35 90C35 94 33 96 29 96H8C4 96 2 94 2 90V67C2 63 4 61 8 61C15 61 20 56 20 49C20 42 15 37 8 37C4 37 2 35 2 31V10C2 6 4 4 8 4Z',

        'b' => 'M8 14H32C36 14 38 12 38 8C38 4 42 2 48 2C54 2 58 4 58 8C58 12 60 14 64 14H92C96 14 98 16 98 20V38C98 42 96 44 92 44C85 44 80 49 80 56C80 63 85 68 92 68C96 68 98 70 98 74V86H70C66 86 64 88 64 92C64 96 60 98 54 98C48 98 44 96 44 92C44 88 42 86 38 86H8C4 86 2 84 2 80V58C2 54 4 52 8 52C15 52 20 47 20 40C20 33 15 28 8 28C4 28 2 26 2 22V20C2 16 4 14 8 14Z',

        'c' => 'M8 4H30C34 4 36 6 36 10C36 17 41 22 48 22C55 22 60 17 60 10C60 6 62 4 66 4H92C96 4 98 6 98 10V90C98 94 96 96 92 96H72C68 96 66 94 66 90C66 83 61 78 54 78C47 78 42 83 42 90C42 94 40 96 36 96H8C4 96 2 94 2 90V70C2 66 4 64 8 64C15 64 20 59 20 52C20 45 15 40 8 40C4 40 2 38 2 34V10C2 6 4 4 8 4Z',

        'd' => 'M8 4H92C96 4 98 6 98 10V34C98 38 96 40 92 40C85 40 80 45 80 52C80 59 85 64 92 64C96 64 98 66 98 70V90C98 94 96 96 92 96H68C64 96 62 94 62 90C62 83 57 78 50 78C43 78 38 83 38 90C38 94 36 96 32 96H8C4 96 2 94 2 90V66C2 62 4 60 8 60C15 60 20 55 20 48C20 41 15 36 8 36C4 36 2 34 2 30V10C2 6 4 4 8 4Z',

        'e' => 'M8 14H34C38 14 40 12 40 8C40 4 44 2 50 2C56 2 60 4 60 8C60 12 62 14 66 14H92C96 14 98 16 98 20V90C98 94 96 96 92 96H68C64 96 62 94 62 90C62 83 57 78 50 78C43 78 38 83 38 90C38 94 36 96 32 96H8C4 96 2 94 2 90V70C2 66 4 64 8 64C15 64 20 59 20 52C20 45 15 40 8 40C4 40 2 38 2 34V20C2 16 4 14 8 14Z',

        'f' => 'M8 4H32C36 4 38 6 38 10C38 17 43 22 50 22C57 22 62 17 62 10C62 6 64 4 68 4H92C96 4 98 6 98 10V32C98 36 96 38 92 38C85 38 80 43 80 50C80 57 85 62 92 62C96 62 98 64 98 68V90C98 94 96 96 92 96H8C4 96 2 94 2 90V68C2 64 4 62 8 62C15 62 20 57 20 50C20 43 15 38 8 38C4 38 2 36 2 32V10C2 6 4 4 8 4Z',
    ];

    $pieces = [
        [
            'key' => 'pigments',
            'file' => 'pigments-closeup.webp',
            'alt' => 'Pigmenți sidefați și fulgi decorativi pentru rășină',
            'shape' => 'b',
            'position' => '--x:1%;--y:1%;--w:24.5%;--h:28.5%;--r:-1.1deg;',
            'mobile' => '--mc:2;--mr:9;',
        ],
        [
            'key' => 'red-cross',
            'file' => 'red-cross.webp',
            'alt' => 'Cruce roșie din rășină în curs de realizare',
            'shape' => 'c',
            'position' => '--x:25.7%;--y:.5%;--w:18%;--h:39.5%;--r:.8deg;',
            'mobile' => '--mc:1;--mr:12;',
        ],
        [
            'key' => 'red-mold',
            'file' => 'red-resin-mold.webp',
            'alt' => 'Rășină roșie turnată într-o matriță rotundă',
            'shape' => 'e',
            'position' => '--x:44%;--y:1.5%;--w:20%;--h:28.5%;--r:-.7deg;',
            'mobile' => '--mc:1;--mr:9;',
        ],
        [
            'key' => 'shelf',
            'file' => 'atelier-shelf.webp',
            'alt' => 'Raftul cu materiale din atelierul MTD ART',
            'shape' => 'a',
            'position' => '--x:64.3%;--y:.2%;--w:20%;--h:41.5%;--r:.6deg;',
            'mobile' => '--mc:1;--mr:12;',
        ],
        [
            'key' => 'prayer',
            'file' => 'prayer-mold.webp',
            'alt' => 'Matriță albastră în formă de mâini împreunate',
            'shape' => 'd',
            'position' => '--x:84.4%;--y:1%;--w:15%;--h:30.5%;--r:-.8deg;',
            'mobile' => '--mc:1;--mr:10;',
        ],
        [
            'key' => 'cured-crosses',
            'file' => 'cured-crosses.webp',
            'alt' => 'Cruci din rășină scoase din matrițe',
            'shape' => 'f',
            'position' => '--x:1%;--y:30.5%;--w:21.5%;--h:33%;--r:1deg;',
            'mobile' => '--mc:1;--mr:10;',
        ],
        [
            'key' => 'tools-wide',
            'file' => 'tools-overview.webp',
            'alt' => 'Instrumente și recipiente pentru turnarea rășinii',
            'shape' => 'a',
            'position' => '--x:22.8%;--y:40.8%;--w:28.5%;--h:23.5%;--r:-.6deg;',
            'mobile' => '--mc:2;--mr:8;',
        ],
        [
            'key' => 'natural-materials',
            'file' => 'natural-materials.webp',
            'alt' => 'Flori și materiale naturale pregătite pentru incluziuni',
            'shape' => 'd',
            'position' => '--x:51.2%;--y:30.8%;--w:28%;--h:27.8%;--r:.5deg;',
            'mobile' => '--mc:1;--mr:9;',
        ],
        [
            'key' => 'glitters',
            'file' => 'glitters.webp',
            'alt' => 'Recipiente cu glitter în nuanțe pastelate',
            'shape' => 'c',
            'position' => '--x:79.4%;--y:31.5%;--w:20%;--h:27.8%;--r:-.9deg;',
            'mobile' => '--mc:1;--mr:9;',
        ],
        [
            'key' => 'wood-rounds',
            'file' => 'wood-rounds.webp',
            'alt' => 'Felii rotunde de lemn pregătite pentru lucru',
            'shape' => 'e',
            'position' => '--x:1%;--y:64.5%;--w:19%;--h:34%;--r:-.8deg;',
            'mobile' => '--mc:1;--mr:11;',
        ],
        [
            'key' => 'bottles',
            'file' => 'pigment-bottles.webp',
            'alt' => 'Colecție de pigmenți colorați din atelier',
            'shape' => 'b',
            'position' => '--x:20.2%;--y:64.7%;--w:27%;--h:33.5%;--r:.7deg;',
            'mobile' => '--mc:2;--mr:10;',
        ],
        [
            'key' => 'resin-tools',
            'file' => 'resin-tools.webp',
            'alt' => 'Unelte și matrițe folosite pentru piesele din rășină',
            'shape' => 'f',
            'position' => '--x:47.5%;--y:59%;--w:20%;--h:39%;--r:1.1deg;',
            'mobile' => '--mc:1;--mr:12;',
        ],
        [
            'key' => 'wood-slices',
            'file' => 'wood-slices.webp',
            'alt' => 'Felii naturale de lemn de diferite dimensiuni',
            'shape' => 'a',
            'position' => '--x:67.5%;--y:60%;--w:31.5%;--h:38%;--r:-.5deg;',
            'mobile' => '--mc:1;--mr:12;',
        ],
    ];
@endphp

@once
    <style>
        .atelier-puzzle-stage {
            position: absolute;
            inset: 0;
            isolation: isolate;
            overflow: hidden;
            background:
                radial-gradient(
                    circle at 17% 14%,
                    rgba(255, 250, 242, 0.74),
                    transparent 26%
                ),
                radial-gradient(
                    circle at 82% 19%,
                    rgba(183, 139, 69, 0.18),
                    transparent 23%
                ),
                linear-gradient(
                    145deg,
                    #e3d5c7 0%,
                    #d6c5b7 52%,
                    #ccb8a8 100%
                );
        }

        /*
         * Main epoxy-resin layer.
         * It stays underneath all puzzle images and remains visible in the gaps.
         */
        .atelier-resin-layer {
            position: absolute;
            inset: -8%;
            z-index: 2;
            pointer-events: none;
            overflow: hidden;
            opacity: 0.98;
            transform: translateZ(0);
            background:
                radial-gradient(
                    ellipse at 7% 12%,
                    rgba(255, 255, 255, 0.92) 0%,
                    rgba(255, 246, 229, 0.52) 10%,
                    transparent 26%
                ),
                radial-gradient(
                    ellipse at 34% 8%,
                    rgba(246, 190, 175, 0.48) 0%,
                    rgba(224, 146, 133, 0.22) 17%,
                    transparent 35%
                ),
                radial-gradient(
                    ellipse at 63% 16%,
                    rgba(255, 234, 187, 0.64) 0%,
                    rgba(197, 142, 73, 0.25) 20%,
                    transparent 40%
                ),
                radial-gradient(
                    ellipse at 89% 35%,
                    rgba(249, 205, 194, 0.56) 0%,
                    rgba(212, 143, 132, 0.23) 19%,
                    transparent 41%
                ),
                radial-gradient(
                    ellipse at 22% 55%,
                    rgba(255, 236, 204, 0.7) 0%,
                    rgba(188, 134, 67, 0.24) 17%,
                    transparent 39%
                ),
                radial-gradient(
                    ellipse at 55% 52%,
                    rgba(255, 255, 255, 0.82) 0%,
                    rgba(242, 211, 179, 0.38) 12%,
                    transparent 31%
                ),
                radial-gradient(
                    ellipse at 83% 75%,
                    rgba(213, 154, 142, 0.45) 0%,
                    rgba(180, 118, 109, 0.18) 18%,
                    transparent 39%
                ),
                radial-gradient(
                    ellipse at 35% 91%,
                    rgba(254, 225, 173, 0.58) 0%,
                    rgba(190, 135, 63, 0.22) 20%,
                    transparent 44%
                ),
                linear-gradient(
                    128deg,
                    rgba(255, 248, 235, 0.88) 0%,
                    rgba(222, 187, 149, 0.74) 24%,
                    rgba(239, 193, 181, 0.75) 48%,
                    rgba(201, 154, 103, 0.72) 72%,
                    rgba(250, 231, 205, 0.86) 100%
                );
            background-size:
                42% 42%,
                46% 40%,
                52% 42%,
                48% 45%,
                50% 46%,
                46% 42%,
                48% 46%,
                54% 45%,
                100% 100%;
            background-repeat: no-repeat;
            filter: saturate(1.08) contrast(1.02);
        }

        /*
         * Fluid pearlescent streaks inside the resin.
         */
        .atelier-resin-layer::before {
            position: absolute;
            inset: -20%;
            content: "";
            opacity: 0.76;
            background:
                repeating-radial-gradient(
                    ellipse at 35% 45%,
                    rgba(255, 255, 255, 0.46) 0,
                    rgba(255, 255, 255, 0.18) 1.5%,
                    transparent 3.5%,
                    transparent 8%
                ),
                repeating-linear-gradient(
                    118deg,
                    transparent 0,
                    transparent 8%,
                    rgba(255, 255, 255, 0.22) 9%,
                    rgba(255, 255, 255, 0.04) 11%,
                    transparent 15%,
                    transparent 26%
                );
            background-size:
                130% 120%,
                180% 180%;
            filter: blur(9px);
            transform: rotate(-8deg) scale(1.1);
            mix-blend-mode: screen;
            animation: atelier-resin-flow 18s ease-in-out infinite alternate;
        }

        /*
         * Gold particles and small air bubbles.
         */
        .atelier-resin-layer::after {
            position: absolute;
            inset: 0;
            content: "";
            opacity: 0.72;
            background-image:
                radial-gradient(
                    circle at 4% 18%,
                    rgba(255, 255, 255, 0.85) 0 1px,
                    transparent 2px
                ),
                radial-gradient(
                    circle at 14% 47%,
                    rgba(255, 236, 190, 0.9) 0 1.2px,
                    transparent 2.4px
                ),
                radial-gradient(
                    circle at 25% 72%,
                    rgba(177, 121, 49, 0.74) 0 1px,
                    transparent 2.3px
                ),
                radial-gradient(
                    circle at 37% 22%,
                    rgba(255, 255, 255, 0.82) 0 1.3px,
                    transparent 2.6px
                ),
                radial-gradient(
                    circle at 48% 61%,
                    rgba(193, 139, 72, 0.85) 0 1.1px,
                    transparent 2.2px
                ),
                radial-gradient(
                    circle at 58% 35%,
                    rgba(255, 240, 206, 0.92) 0 1px,
                    transparent 2.2px
                ),
                radial-gradient(
                    circle at 69% 83%,
                    rgba(255, 255, 255, 0.82) 0 1.2px,
                    transparent 2.5px
                ),
                radial-gradient(
                    circle at 78% 13%,
                    rgba(183, 127, 60, 0.8) 0 1px,
                    transparent 2.3px
                ),
                radial-gradient(
                    circle at 89% 54%,
                    rgba(255, 233, 183, 0.92) 0 1.2px,
                    transparent 2.5px
                ),
                radial-gradient(
                    circle at 96% 89%,
                    rgba(255, 255, 255, 0.84) 0 1px,
                    transparent 2.3px
                );
            background-size:
                73px 79px,
                101px 107px,
                89px 97px,
                127px 121px,
                83px 91px,
                109px 103px,
                97px 113px,
                131px 127px,
                79px 89px,
                119px 109px;
            mix-blend-mode: screen;
        }

        /*
         * Glass-like highlight over the resin surface.
         */
        .atelier-resin-gloss {
            position: absolute;
            inset: 0;
            z-index: 3;
            pointer-events: none;
            opacity: 0.82;
            background:
                linear-gradient(
                    112deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.04) 19%,
                    rgba(255, 255, 255, 0.5) 22%,
                    rgba(255, 255, 255, 0.09) 26%,
                    transparent 34%
                ),
                linear-gradient(
                    155deg,
                    transparent 0%,
                    transparent 52%,
                    rgba(255, 250, 239, 0.28) 56%,
                    transparent 62%
                ),
                radial-gradient(
                    ellipse at 48% -8%,
                    rgba(255, 255, 255, 0.62),
                    transparent 45%
                );
            mix-blend-mode: screen;
        }

        /*
         * Subtle shadow channels that give the impression
         * that puzzle pieces are embedded in wet resin.
         */
        .atelier-resin-depth {
            position: absolute;
            inset: 0;
            z-index: 4;
            pointer-events: none;
            background:
                radial-gradient(
                    ellipse at center,
                    transparent 40%,
                    rgba(77, 48, 31, 0.1) 100%
                ),
                linear-gradient(
                    90deg,
                    rgba(66, 42, 27, 0.08),
                    transparent 9%,
                    transparent 91%,
                    rgba(66, 42, 27, 0.08)
                );
            mix-blend-mode: multiply;
        }

        .atelier-puzzle-stage > .atelier-stage-texture {
            position: absolute;
            inset: 0;
            z-index: 5;
            pointer-events: none;
            opacity: 0.18;
            background-image:
                repeating-linear-gradient(
                    25deg,
                    rgba(255, 255, 255, 0.12) 0,
                    rgba(255, 255, 255, 0.12) 1px,
                    transparent 1px,
                    transparent 9px
                );
        }

        .atelier-puzzle-stage > .atelier-stage-vignette {
            position: absolute;
            inset: 0;
            z-index: 30;
            pointer-events: none;
            background:
                linear-gradient(
                    90deg,
                    rgba(68, 48, 34, 0.09),
                    transparent 12%,
                    transparent 88%,
                    rgba(68, 48, 34, 0.07)
                ),
                linear-gradient(
                    180deg,
                    rgba(255, 255, 255, 0.12),
                    transparent 15%,
                    transparent 86%,
                    rgba(72, 47, 31, 0.08)
                );
            mix-blend-mode: multiply;
        }

        .atelier-puzzle-piece {
            position: absolute;
            left: var(--x);
            top: var(--y);
            z-index: 10;
            display: block;
            width: var(--w);
            height: var(--h);
            margin: 0;
            transform: rotate(var(--r));
            transform-origin: center;
            filter:
                drop-shadow(0 1px 1px rgba(255, 255, 255, 0.82))
                drop-shadow(0 5px 4px rgba(76, 49, 30, 0.28))
                drop-shadow(0 14px 17px rgba(69, 45, 27, 0.2));
            transition:
                transform 350ms cubic-bezier(0.2, 0.7, 0.2, 1),
                filter 350ms ease;
            will-change: transform;
        }

        .atelier-puzzle-piece:hover {
            z-index: 25;
            transform: translateY(-5px) rotate(0deg) scale(1.018);
            filter:
                drop-shadow(0 2px 1px rgba(255, 255, 255, 0.9))
                drop-shadow(0 7px 6px rgba(76, 49, 30, 0.3))
                drop-shadow(0 22px 25px rgba(69, 45, 27, 0.3));
        }

        .atelier-puzzle-piece:focus-visible {
            z-index: 25;
            outline: 3px solid rgba(182, 139, 69, 0.8);
            outline-offset: 4px;
            transform: translateY(-4px) rotate(0deg) scale(1.015);
        }

        .atelier-puzzle-piece svg {
            display: block;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        /*
         * Bright outer line simulates the resin gathering
         * around the border of every embedded puzzle piece.
         */
        .atelier-puzzle-piece__resin-rim {
            fill: none;
            stroke: rgba(255, 238, 211, 0.85);
            stroke-width: 5.2;
            stroke-linejoin: round;
            opacity: 0.5;
            filter: blur(1.4px);
            vector-effect: non-scaling-stroke;
        }

        .atelier-puzzle-piece__edge {
            fill: none;
            stroke: rgba(255, 250, 243, 0.96);
            stroke-width: 2.35;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }

        .atelier-puzzle-piece__inner-edge {
            fill: none;
            stroke: rgba(91, 57, 35, 0.28);
            stroke-width: 0.8;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }

        .atelier-puzzle-piece__shine {
            fill: none;
            stroke: rgba(255, 255, 255, 0.45);
            stroke-width: 1.1;
            stroke-dasharray: 18 82;
            stroke-linecap: round;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }

        @keyframes atelier-resin-flow {
            0% {
                transform: translate3d(-2%, -1%, 0) rotate(-8deg) scale(1.1);
            }

            50% {
                transform: translate3d(2%, 1%, 0) rotate(-5deg) scale(1.14);
            }

            100% {
                transform: translate3d(-1%, 2%, 0) rotate(-10deg) scale(1.12);
            }
        }

        /*
         * Mobile and tablet:
         * coordinates are replaced with a masonry-like grid.
         */
        @media (max-width: 1023px) {
            .atelier-puzzle-stage {
                position: relative;
                inset: auto;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                grid-auto-flow: dense;
                grid-auto-rows: 18px;
                gap: 9px;
                width: 100%;
                min-height: 0;
                padding: 14px;
            }

            .atelier-resin-layer,
            .atelier-resin-gloss,
            .atelier-resin-depth,
            .atelier-stage-texture,
            .atelier-stage-vignette {
                position: absolute;
            }

            .atelier-puzzle-piece {
                position: relative;
                left: auto;
                top: auto;
                width: auto;
                height: auto;
                min-width: 0;
                grid-column: span min(var(--mc), 2);
                grid-row: span var(--mr);
                transform: rotate(var(--r));
            }

            .atelier-puzzle-piece svg {
                position: absolute;
                inset: 0;
            }
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            .atelier-puzzle-stage {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                grid-auto-rows: 21px;
                gap: 11px;
                padding: 20px;
            }

            .atelier-puzzle-piece {
                grid-column: span var(--mc);
            }
        }

        @media (hover: none) {
            .atelier-puzzle-piece:hover {
                z-index: 10;
                transform: rotate(var(--r));
                filter:
                    drop-shadow(0 1px 1px rgba(255, 255, 255, 0.82))
                    drop-shadow(0 5px 4px rgba(76, 49, 30, 0.28))
                    drop-shadow(0 14px 17px rgba(69, 45, 27, 0.2));
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .atelier-resin-layer::before {
                animation: none;
            }

            .atelier-puzzle-piece {
                transition: none;
            }

            .atelier-puzzle-piece:hover,
            .atelier-puzzle-piece:focus-visible {
                transform: rotate(var(--r));
            }
        }
    </style>
@endonce

<section
    class="relative overflow-hidden border-b border-black/5 bg-[#f4eee4] lg:h-[calc(100svh-6rem)] lg:min-h-[680px] lg:max-h-[860px]"
>
    <div
        class="pointer-events-none absolute inset-0"
        aria-hidden="true"
    >
        <div class="absolute -left-24 top-16 h-72 w-72 rounded-full bg-[#d7b36f]/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-[#c98f84]/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto flex h-full max-w-[1700px] flex-col lg:grid lg:grid-rows-[minmax(0,1fr)_86px]">
        <div class="grid min-h-0 lg:grid-cols-[38%_62%]">
            {{-- Text content --}}
            <div class="flex items-center px-6 py-12 sm:px-10 lg:px-12 lg:py-7 xl:px-16 2xl:px-20">
                <div class="mx-auto w-full max-w-[540px] lg:mx-0">
                    <div class="mb-5">
                        <div class="flex items-center gap-3 text-[#aa7a36]">
                            <svg
                                class="h-7 w-7"
                                viewBox="0 0 48 48"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M24 40V16M24 24c-7-1-11-5-12-11 7 0 11 4 12 11Zm0 5c7-1 11-5 12-11-7 0-11 4-12 11Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />

                                <path
                                    d="M14 39h20"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <div>
                                <p class="font-serif text-[1.35rem] font-semibold leading-none tracking-[0.14em] text-dark-brown">
                                    MTD ART
                                </p>

                                <p class="mt-1 text-[9px] font-semibold uppercase tracking-[0.27em] text-[#aa7a36]">
                                    Piese lucrate manual
                                </p>
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
                        lumină și
                        <span class="text-[#bf8179]">natură</span><br>
                        iau naștere<br>
                        obiecte cu
                        <span class="text-[#b68b45]">suflet.</span>
                    </h1>

                    <div class="my-5 h-px w-12 bg-[#bf8179] xl:my-6"></div>

                    <p class="max-w-md text-sm font-light leading-6 text-dark-brown/75 xl:text-[15px] xl:leading-7">
                        Fiecare piesă este turnată, pigmentată și finisată manual,
                        într-un atelier în care culoarea, simbolul și materia
                        naturală se întâlnesc.
                    </p>

                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center xl:mt-6">
                        <a
                            href="{{ route('shop.index') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-3 rounded-md bg-[#344f3f] px-6 py-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:bg-[#2b4335] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#344f3f]/30 focus:ring-offset-2"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c0-5 2-8 6-10m-6 10c0-5-2-8-6-10"
                                />
                            </svg>

                            Descoperă colecțiile
                        </a>

                        <a
                            href="#atelier"
                            class="inline-flex min-h-11 items-center justify-center gap-3 rounded-md border border-[#b68b45]/70 bg-white/35 px-6 py-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-[#9a7037] transition duration-300 hover:-translate-y-0.5 hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-[#b68b45]/25 focus:ring-offset-2"
                        >
                            Intră în atelier
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>

                    <div class="mt-6 grid gap-4 border-t border-dark-brown/10 pt-4 sm:grid-cols-3 xl:mt-7">
                        <div class="flex gap-2.5">
                            <span
                                class="text-lg leading-none text-[#bf8179]"
                                aria-hidden="true"
                            >
                                ♡
                            </span>

                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.12em]">
                                    Unicat
                                </p>

                                <p class="mt-1 text-[10px] leading-4 text-dark-brown/60">
                                    Fiecare piesă este diferită
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2.5">
                            <span
                                class="text-lg leading-none text-[#57735f]"
                                aria-hidden="true"
                            >
                                ⌁
                            </span>

                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.12em]">
                                    Lucrat manual
                                </p>

                                <p class="mt-1 text-[10px] leading-4 text-dark-brown/60">
                                    Cu grijă și pasiune
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2.5">
                            <span
                                class="text-lg leading-none text-[#b68b45]"
                                aria-hidden="true"
                            >
                                ✧
                            </span>

                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.12em]">
                                    Materiale premium
                                </p>

                                <p class="mt-1 text-[10px] leading-4 text-dark-brown/60">
                                    Rășină și pigmenți aleși
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Puzzle collage --}}
            <div
                id="atelier"
                class="relative overflow-hidden bg-[#d7c7b8] scroll-mt-24 lg:min-h-0"
            >
                <div class="atelier-puzzle-stage">
                    {{-- Resin visible between the puzzle pieces --}}
                    <div
                        class="atelier-resin-layer"
                        aria-hidden="true"
                    ></div>

                    <div
                        class="atelier-resin-gloss"
                        aria-hidden="true"
                    ></div>

                    <div
                        class="atelier-resin-depth"
                        aria-hidden="true"
                    ></div>

                    <div
                        class="atelier-stage-texture"
                        aria-hidden="true"
                    ></div>

                    @foreach ($pieces as $piece)
                        @php
                            $clipId = $componentId . '-' . $piece['key'];
                            $pieceStyle = $piece['position'] . $piece['mobile'];
                        @endphp

                        <figure
                            class="atelier-puzzle-piece"
                            style="{{ $pieceStyle }}"
                            role="img"
                            aria-label="{{ $piece['alt'] }}"
                            tabindex="0"
                        >
                            <svg
                                viewBox="0 0 100 100"
                                xmlns="http://www.w3.org/2000/svg"
                                role="presentation"
                                focusable="false"
                            >
                                <defs>
                                    <clipPath
                                        id="{{ $clipId }}"
                                        clipPathUnits="userSpaceOnUse"
                                    >
                                        <path d="{{ $puzzlePaths[$piece['shape']] }}"></path>
                                    </clipPath>

                                    <linearGradient
                                        id="{{ $clipId }}-shine"
                                        x1="0"
                                        y1="0"
                                        x2="1"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#ffffff"
                                            stop-opacity="0.42"
                                        />

                                        <stop
                                            offset="38%"
                                            stop-color="#ffffff"
                                            stop-opacity="0"
                                        />

                                        <stop
                                            offset="100%"
                                            stop-color="#ffffff"
                                            stop-opacity="0.12"
                                        />
                                    </linearGradient>
                                </defs>

                                <path
                                    class="atelier-puzzle-piece__resin-rim"
                                    d="{{ $puzzlePaths[$piece['shape']] }}"
                                ></path>

                                <image
                                    href="{{ asset('img/hero/' . $piece['file']) }}"
                                    x="0"
                                    y="0"
                                    width="100"
                                    height="100"
                                    preserveAspectRatio="xMidYMid slice"
                                    clip-path="url(#{{ $clipId }})"
                                ></image>

                                <path
                                    d="{{ $puzzlePaths[$piece['shape']] }}"
                                    fill="url(#{{ $clipId }}-shine)"
                                    clip-path="url(#{{ $clipId }})"
                                    opacity="0.34"
                                ></path>

                                <path
                                    class="atelier-puzzle-piece__edge"
                                    d="{{ $puzzlePaths[$piece['shape']] }}"
                                ></path>

                                <path
                                    class="atelier-puzzle-piece__inner-edge"
                                    d="{{ $puzzlePaths[$piece['shape']] }}"
                                ></path>

                                <path
                                    class="atelier-puzzle-piece__shine"
                                    d="{{ $puzzlePaths[$piece['shape']] }}"
                                ></path>
                            </svg>
                        </figure>
                    @endforeach

                    <div
                        class="atelier-stage-vignette"
                        aria-hidden="true"
                    ></div>

                    <div
                        class="absolute bottom-[4.5%] right-[3.5%] z-40 hidden h-24 w-24 items-center justify-center rounded-full border border-[#b68b45]/55 bg-[#f7f0e5]/95 text-center shadow-xl backdrop-blur xl:flex"
                        aria-hidden="true"
                    >
                        <div>
                            <p class="text-[8px] font-semibold uppercase tracking-[0.16em] text-[#a37639]">
                                Din atelierul
                            </p>

                            <p class="mt-1 font-serif text-base tracking-[0.1em] text-dark-brown">
                                MTD ART
                            </p>

                            <span class="mt-0.5 block text-[#bf8179]">
                                ♡
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom features --}}
        <div class="relative border-t border-black/5 bg-[#f8f2e8]/96">
            <div class="mx-auto grid h-full max-w-7xl grid-cols-2 divide-x divide-y divide-dark-brown/10 px-4 md:grid-cols-4 md:divide-y-0 md:px-8">
                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">
                        Pigmenți de calitate
                    </p>

                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">
                        Nuanțe intense și rezistente
                    </p>
                </div>

                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">
                        Elemente naturale
                    </p>

                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">
                        Flori, lemn și minerale
                    </p>
                </div>

                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">
                        Turnat manual
                    </p>

                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">
                        Fiecare detaliu contează
                    </p>
                </div>

                <div class="flex flex-col justify-center px-4 py-3 sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-dark-brown sm:text-[11px]">
                        Cadouri cu semnificație
                    </p>

                    <p class="mt-1 text-[10px] leading-4 text-dark-brown/60 sm:text-[11px]">
                        Pentru momente speciale
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>