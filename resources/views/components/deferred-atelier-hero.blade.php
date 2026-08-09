@php
    // Keep the original atelier component as the single source of truth for design.
    // Only defer the external SVG <image> requests until the collage is near the viewport.
    $atelierHeroHtml = view('components.atelier-hero')->render();
    $atelierHeroHtml = str_replace(
        '<div class="atelier-puzzle-stage">',
        '<div class="atelier-puzzle-stage" data-deferred-svg-root>',
        $atelierHeroHtml,
    );
    $atelierHeroHtml = preg_replace(
        '/<image(\s+)href="/m',
        '<image$1data-deferred-href="',
        $atelierHeroHtml,
    );
@endphp

{!! $atelierHeroHtml !!}

<noscript>
    <style>
        #atelier .atelier-puzzle-stage { display: none !important; }
        #atelier .atelier-noscript-fallback { display: block !important; }
    </style>
    <div id="atelier" class="atelier-noscript-fallback hidden overflow-hidden bg-[#d7c7b8] p-2">
        <img
            src="{{ asset('img/hero/natural-materials.webp') }}"
            alt="Materiale naturale și flori pregătite în atelierul MTD ART"
            class="h-auto w-full object-cover"
        >
    </div>
</noscript>
