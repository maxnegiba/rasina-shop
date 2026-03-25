@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
    
    <div class="mb-16">
        <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-4">Informații Legale</span>
        <h1 class="font-serif text-4xl md:text-5xl text-smoked-black mb-6">Termeni și Condiții</h1>
        <p class="text-sm font-light text-smoked-black/60 tracking-wide">Ultima actualizare: Martie 2026</p>
    </div>

    <div class="prose prose-espresso prose-headings:font-serif prose-headings:text-smoked-black prose-p:text-smoked-black/70 prose-p:font-light prose-p:leading-relaxed max-w-none">
        
        <h2 class="text-2xl mt-12 mb-4">1. Informații Generale</h2>
        <p>
            Acest site web este operat de către <strong>[NUME FIRMĂ SRL]</strong>, cu sediul social în [Adresa completă], înregistrată la Registrul Comerțului sub nr. [J...], CUI [RO...]. Utilizarea magazinului online MTD ART (navigarea și cumpărarea produselor) implică acceptarea acestor termeni și condiții.
        </p>

        <h2 class="text-2xl mt-12 mb-4">2. Produsele Noastre: Standard vs. Unicat</h2>
        <p>
            MTD ART comercializează obiecte de design și artă din rășină epoxidică și lemn. Deoarece o mare parte din munca noastră este manuală (hand-made), pot exista mici variații de culoare, textură sau bule fine de aer între fotografia de prezentare și produsul final. Acestea nu sunt defecte, ci semnătura unicității fiecărei piese.
        </p>
        <p>
            <strong>Produsele la comandă / personalizate:</strong> Pentru piesele unicat realizate conform specificațiilor clientului, se va percepe un avans stabilit de comun acord, iar termenul de execuție va fi comunicat individual.
        </p>

        <h2 class="text-2xl mt-12 mb-4">3. Prețuri și Plăți</h2>
        <p>
            Toate prețurile afișate pe site sunt exprimate în RON. Ne rezervăm dreptul de a modifica prețurile fără o notificare prealabilă, însă comenzile deja plasate și confirmate vor păstra prețul de la momentul achiziției.
        </p>
        <p>
            Plata online este procesată în siguranță prin intermediul platformei <strong>Stripe</strong>. Nu stocăm și nu avem acces la datele cardului dumneavoastră bancar. Factura fiscală va fi emisă automat și trimisă pe email (și în e-Factura/SPV) conform legislației în vigoare.
        </p>

        <h2 class="text-2xl mt-12 mb-4">4. Politica de Retur și Excepții (OUG 34/2014)</h2>
        <p>
            Conform legislației din România, beneficiați de o perioadă de 14 zile calendaristice pentru a returna un produs standard, fără a fi nevoit să justificați decizia de retragere. Costurile de transport pentru retur vor fi suportate de către client.
        </p>
        <p class="p-4 bg-smoked-black/5 border-l-2 border-vintage-gold italic">
            <strong>Atenție: Exceptări de la dreptul de retragere.</strong> Conform OUG 34/2014, art. 16, lit. c, <strong>produsele confecționate după specificațiile prezentate de consumator sau personalizate în mod clar (produsele la comandă, dimensiuni atipice, culori specifice de rășină solicitate de client) NU pot fi returnate.</strong>
        </p>

        <h2 class="text-2xl mt-12 mb-4">5. Soluționarea Litigiilor (ANPC)</h2>
        <p>
            Orice nemulțumire legată de produsele noastre va fi rezolvată pe cale amiabilă. Dacă acest lucru nu este posibil, vă puteți adresa Autorității Naționale pentru Protecția Consumatorilor (ANPC).
        </p>
        <p>
            Link-uri utile: <br>
            <a href="https://anpc.ro/" target="_blank" rel="noopener noreferrer" class="text-vintage-gold hover:underline">Site-ul oficial ANPC</a> <br>
            <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer" class="text-vintage-gold hover:underline">Platforma SOL (Soluționarea Online a Litigiilor)</a>
        </p>

    </div>
</div>
@endsection