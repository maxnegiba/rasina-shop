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
