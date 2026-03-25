@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
    
    <div class="mb-16">
        <span class="block text-vintage-gold tracking-[0.3em] text-xs font-semibold uppercase mb-4">Informații Legale</span>
        <h1 class="font-serif text-4xl md:text-5xl text-smoked-black mb-6">Politica de Confidențialitate (GDPR)</h1>
        <p class="text-sm font-light text-smoked-black/60 tracking-wide">Ultima actualizare: Martie 2026</p>
    </div>

    <div class="prose prose-espresso prose-headings:font-serif prose-headings:text-smoked-black prose-p:text-smoked-black/70 prose-p:font-light prose-p:leading-relaxed max-w-none">
        
        <h2 class="text-2xl mt-12 mb-4">1. Cine suntem și cum ne puteți contacta</h2>
        <p>
            Platforma MTD ART respectă cu strictețe confidențialitatea datelor dumneavoastră. Conform Regulamentului (UE) 2016/679 (GDPR), compania <strong>[NUME FIRMĂ SRL]</strong> este operatorul datelor cu caracter personal pe care ni le furnizați. Ne puteți contacta oricând la adresa de email: <strong>contact@mtdart.ro</strong>.
        </p>

        <h2 class="text-2xl mt-12 mb-4">2. Ce date colectăm</h2>
        <p>Pentru a procesa o comandă, avem nevoie de următoarele informații:</p>
        <ul class="list-disc pl-5 text-smoked-black/70 font-light space-y-2 mb-6">
            <li>Nume și prenume;</li>
            <li>Adresă de email și număr de telefon;</li>
            <li>Adresă de livrare și de facturare;</li>
            <li>Date fiscale (dacă doriți factură pe firmă).</li>
        </ul>
        <p>
            <strong>Notă despre plăți:</strong> Plățile cu cardul sunt procesate integral și în siguranță de către <strong>Stripe</strong>. MTD ART nu stochează și nu are acces la detaliile cardului dumneavoastră bancar (număr card, CVV, dată expirare).
        </p>

        <h2 class="text-2xl mt-12 mb-4">3. Scopul colectării și timpul de păstrare</h2>
        <p>
            Colectăm aceste date exclusiv pentru: procesarea și livrarea comenzilor, emiterea facturilor fiscale (Oblio/e-Factura) și comunicarea cu dumneavoastră referitoare la statusul comenzii (sau detalii pentru piesele realizate la comandă).
        </p>
        <p>
            Conform legislației fiscale din România, documentele financiar-contabile (facturile) care conțin datele dumneavoastră trebuie arhivate pe o perioadă de 10 ani.
        </p>

        <h2 class="text-2xl mt-12 mb-4">4. Drepturile dumneavoastră</h2>
        <p>
            În calitate de persoană vizată, aveți următoarele drepturi: dreptul de acces la date, dreptul la rectificare, dreptul la ștergere („dreptul de a fi uitat” – în limitele legii contabilității), dreptul la restricționarea prelucrării și dreptul la portabilitatea datelor. Pentru a exercita oricare dintre aceste drepturi, vă rugăm să ne trimiteți un email la adresa menționată mai sus.
        </p>

    </div>
</div>
@endsection