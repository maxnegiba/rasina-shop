@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
    
    <div class="mb-16">
        <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-4">Informații Comerciale</span>
        <h1 class="font-serif text-4xl md:text-5xl text-smoked-black mb-6">Politica de Livrare și Retur</h1>
    </div>

    <div class="prose prose-espresso prose-headings:font-serif prose-headings:text-smoked-black prose-p:text-smoked-black/70 prose-p:font-light prose-p:leading-relaxed max-w-none">
        
        <h2 class="text-2xl mt-12 mb-4">1. Politica de Livrare</h2>
        <p>
            Livrăm pe întreg teritoriul României prin intermediul firmelor de curierat rapid (ex: Fan Courier, Cargus, Sameday).
        </p>
        <ul class="list-disc pl-5 text-smoked-black/70 font-light space-y-2 mb-6">
            <li><strong>Produsele aflate în stoc:</strong> Vor fi procesate și predate curierului în termen de 1-3 zile lucrătoare de la confirmarea plății.</li>
            <li><strong>Produsele realizate la comandă:</strong> Timpul de execuție depinde de complexitatea piesei și timpul necesar pentru uscarea completă a rășinii epoxidice. Vă vom comunica un termen de livrare estimativ (de obicei între 7 și 21 de zile) în momentul confirmării detaliilor.</li>
        </ul>
        <p>
            Toate creațiile noastre sunt ambalate cu extremă grijă pentru a preveni orice deteriorare în timpul transportului. Vă rugăm să verificați integritatea coletului la primire.
        </p>

        <h2 class="text-2xl mt-12 mb-4">2. Dreptul de Retur (Produse Standard)</h2>
        <p>
            Ne dorim să fiți complet mulțumit de achiziția făcută. Dacă, din orice motiv, doriți să returnați un <strong>produs standard (aflat în stoc la momentul comenzii)</strong>, o puteți face în termen de <strong>14 zile calendaristice</strong> de la primirea coletului, conform OUG 34/2014.
        </p>
        <p>
            <strong>Pași pentru retur:</strong>
        </p>
        <ol class="list-decimal pl-5 text-smoked-black/70 font-light space-y-2 mb-6">
            <li>Trimiteți un email la <strong>contact@mtdart.ro</strong> cu numărul comenzii și intenția de retur.</li>
            <li>Ambalați produsul foarte bine (folosind, pe cât posibil, materialele de protecție originale). Fiind obiecte de artă, integritatea lor la transportul de retur este responsabilitatea dumneavoastră.</li>
            <li>Expediați coletul, fără ramburs, către adresa pe care v-o vom comunica pe email. <strong>Costul transportului de retur este suportat de client.</strong></li>
        </ol>

        <h2 class="text-2xl mt-12 mb-4 text-red-800/80">3. Excepții de la Retur (Produse Unicat / La comandă)</h2>
        <p class="p-6 bg-red-50/50 border-l-2 border-red-800/50 italic">
            Din cauza naturii procesului nostru de creație, <strong>obiectele realizate la comandă, personalizate cu anumite culori, dimensiuni sau inserții specifice solicitate de dumneavoastră, NU pot fi returnate</strong> (conform art. 16, lit. c din OUG 34/2014). Vă vom ține mereu la curent cu procesul de fabricație și vă vom trimite poze înainte de livrarea finală pentru a ne asigura că piesa corespunde viziunii dumneavoastră.
        </p>

        <h2 class="text-2xl mt-12 mb-4">4. Rambursarea Banilor</h2>
        <p>
            După ce primim și verificăm integritatea produsului returnat, vom iniția procesul de rambursare. Suma aferentă produsului va fi returnată în maximum 14 zile, direct în contul bancar asociat cardului cu care ați efectuat plata pe site.
        </p>

    </div>
</div>
@endsection