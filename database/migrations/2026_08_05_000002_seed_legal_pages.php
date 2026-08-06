<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $business = e((string) config('shop.legal.business_name'));
        $taxId = e((string) config('shop.legal.tax_id'));
        $tradeRegister = e((string) config('shop.legal.trade_register'));
        $address = e((string) config('shop.legal.address'));
        $email = e((string) config('shop.legal.email'));
        $phone = e((string) config('shop.legal.phone'));
        $brand = e((string) config('shop.brand_name'));
        $updatedAt = now();

        $pages = [
            'termeni-si-conditii' => [
                'title' => 'Termeni și Condiții',
                'content' => <<<HTML
<p><strong>Ultima actualizare: 5 august 2026</strong></p>
<h2>1. Identitatea operatorului</h2>
<p>Magazinul online {$brand}, disponibil la adresa mtdart.ro, este operat de <strong>{$business}</strong>, CUI/CIF {$taxId}, înregistrată la Registrul Comerțului sub nr. {$tradeRegister}, cu sediul în {$address}.</p>
<p>Contact: <a href="mailto:{$email}">{$email}</a>, telefon {$phone}. Aceste date trebuie folosite pentru întrebări, reclamații, retrageri și solicitări privind comenzile.</p>
<h2>2. Domeniul de aplicare</h2>
<p>Prezenții termeni se aplică vizitării site-ului și contractelor la distanță încheiate prin magazin. Prin plasarea comenzii, clientul confirmă că a citit și acceptat versiunea termenilor afișată înaintea plății. Pentru consumatori se aplică drepturile obligatorii prevăzute de legislația română și europeană, care prevalează asupra oricărei clauze contrare.</p>
<h2>3. Produsele</h2>
<p>Produsele sunt realizate manual, adesea în serii mici sau ca piese unicat. Diferențele minore de culoare, fibră, transparență, distribuție a elementelor naturale ori dimensiuni, inerente lucrului manual și materialelor naturale, nu constituie automat lipsă de conformitate. Fotografiile sunt orientative; descrierea și caracteristicile publicate pentru fiecare produs fac parte din ofertă.</p>
<h2>4. Prețuri și disponibilitate</h2>
<p>Prețurile sunt exprimate în RON și includ taxele aplicabile, dacă nu este indicat clar altfel. Totalul datorat este afișat înainte de continuarea către Stripe. Pentru comenzile achitate prin site, orice cost de livrare aplicabil trebuie inclus sau comunicat și acceptat expres înainte de plată. Stocul este verificat și rezervat la inițierea plății; o sesiune abandonată eliberează automat produsele după expirare.</p>
<h2>5. Încheierea contractului</h2>
<p>Adăugarea unui produs în coș nu rezervă produsul și nu reprezintă acceptarea comenzii. Clientul verifică produsele, acceptă termenii, apoi este redirecționat către pagina securizată Stripe. Contractul se consideră încheiat după confirmarea plății și transmiterea confirmării comenzii pe email. Dacă executarea este imposibilă dintr-o eroare evidentă de preț/stoc sau dintr-o cauză obiectivă, clientul va fi informat fără întârziere și suma încasată va fi restituită integral.</p>
<h2>6. Plata și documentul proforma</h2>
<p>Plata online se efectuează cu cardul prin Stripe. Datele complete ale cardului sunt introduse direct în infrastructura Stripe și nu sunt stocate de {$brand}. După plata confirmată, sistemul trimite confirmarea comenzii și un <strong>document proforma nefiscal</strong>. Proforma are rol comercial/informativ, nu este factură fiscală, nu justifică deducerea TVA și nu înlocuiește documentele fiscale pe care operatorul trebuie să le emită conform legii.</p>
<h2>7. Livrarea</h2>
<p>Livrarea se face la adresa introdusă în Stripe, prin curier, în termenul estimat comunicat pentru comandă. Dacă nu a fost convenit alt termen, livrarea se face fără întârziere nejustificată și cel târziu în termenul legal. Riscul de pierdere sau deteriorare trece la consumator când acesta sau o persoană desemnată de el intră în posesia fizică a produsului.</p>
<h2>8. Retragere și retur</h2>
<p>Consumatorii beneficiază, de regulă, de dreptul de retragere în 14 zile de la primirea produsului. Condițiile, procedura, formularul orientativ și excepțiile sunt descrise în pagina <a href="/info/politica-de-retur">Livrare și Retur</a>. Excepția pentru bunurile realizate după specificațiile clientului sau clar personalizate se aplică numai acelor produse; simplul fapt că o piesă este unicat ori lucrată manual nu elimină automat dreptul de retragere.</p>
<h2>9. Conformitate și garanții legale</h2>
<p>Produsele beneficiază de garanția legală de conformitate și de măsurile corective prevăzute de OUG nr. 140/2021. Drepturile privind conformitatea se aplică și produselor personalizate. Pentru o sesizare, clientul trebuie să transmită numărul comenzii, descrierea problemei și, dacă este util, fotografii. Soluționarea se face potrivit legii, fără limitarea drepturilor consumatorului.</p>
<h2>10. Proprietate intelectuală</h2>
<p>Textele, fotografiile, elementele grafice, modelele și identitatea vizuală publicate pe site sunt protejate de legislația proprietății intelectuale. Copierea, republicarea ori exploatarea comercială fără acord scris este interzisă, cu excepțiile permise de lege.</p>
<h2>11. Reclamații și litigii</h2>
<p>Recomandăm soluționarea amiabilă prin contactarea operatorului la {$email}. Consumatorii pot folosi și mecanismul de Soluționare Alternativă a Litigiilor al ANPC: <a href="https://reclamatiisal.anpc.ro/" target="_blank" rel="noopener">reclamatiisal.anpc.ro</a>, respectiv portalul ANPC pentru cereri: <a href="https://eservicii.anpc.ro/Depune-Cerere" target="_blank" rel="noopener">eservicii.anpc.ro</a>. Platforma europeană ODR/SOL nu mai este indicată deoarece a fost închisă în 2025.</p>
<h2>12. Legea aplicabilă</h2>
<p>Contractele sunt guvernate de legea română, fără a priva consumatorul de protecția obligatorie acordată de legea statului său de reședință, atunci când aceasta este aplicabilă. Litigiile nesoluționate amiabil se adresează instanțelor competente potrivit legii.</p>
HTML,
            ],
            'politica-de-confidentialitate' => [
                'title' => 'Politica de Confidențialitate',
                'content' => <<<HTML
<p><strong>Ultima actualizare: 5 august 2026</strong></p>
<h2>1. Operatorul datelor</h2>
<p>Operatorul datelor cu caracter personal este <strong>{$business}</strong>, CUI/CIF {$taxId}, nr. Registrul Comerțului {$tradeRegister}, sediul {$address}, email <a href="mailto:{$email}">{$email}</a>, telefon {$phone}.</p>
<h2>2. Datele pe care le prelucrăm</h2>
<p>Putem prelucra: numele, emailul, telefonul, adresa de facturare și livrare, produsele comandate, valoarea și starea plății, identificatorii Stripe ai tranzacției, mesajele trimise prin formulare, istoricul comunicărilor și date tehnice necesare securității site-ului. Nu primim și nu stocăm numărul complet al cardului, codul CVC sau datele complete de autentificare; acestea sunt prelucrate de Stripe.</p>
<h2>3. Scopuri și temeiuri</h2>
<ul>
<li><strong>Executarea contractului:</strong> coș, plată, confirmare, livrare, retur, garanție și asistență pentru comandă;</li>
<li><strong>Obligații legale:</strong> evidențe comerciale, contabile, fiscale, protecția consumatorului și răspunsuri către autorități;</li>
<li><strong>Interes legitim:</strong> securitatea site-ului, prevenirea fraudei, apărarea drepturilor și îmbunătățirea serviciului, după evaluarea intereselor persoanelor vizate;</li>
<li><strong>Consimțământ:</strong> numai pentru comunicări comerciale ori tehnologii neesențiale, dacă vor fi activate; consimțământul poate fi retras oricând.</li>
</ul>
<h2>4. Furnizarea datelor</h2>
<p>Datele marcate ca obligatorii sunt necesare pentru procesarea comenzii. Fără ele nu putem încasa plata, forma contractul ori livra produsele. Datele opționale pot fi omise.</p>
<h2>5. Destinatari și furnizori</h2>
<p>Datele pot fi comunicate, strict cât este necesar, către Stripe pentru plăți, furnizorii de găzduire și mentenanță, serviciile de email, curieri, contabilitate și consultanți, precum și autorităților atunci când legea o impune. Furnizorii acționează în baza propriilor obligații legale și/sau a contractelor de protecție a datelor.</p>
<h2>6. Transferuri internaționale</h2>
<p>Unii furnizori tehnologici pot prelucra date în afara Spațiului Economic European. În aceste situații se folosesc mecanisme legale adecvate, precum decizii de adecvare sau clauze contractuale standard, împreună cu măsuri suplimentare când sunt necesare. Informații despre mecanismele Stripe sunt disponibile în documentația sa de confidențialitate.</p>
<h2>7. Durata păstrării</h2>
<p>Datele comenzilor și documentele asociate se păstrează pe durata cerută de legislația contabilă, fiscală și de protecție a consumatorilor. Comunicările fără comandă se păstrează, de regulă, cel mult 3 ani de la ultima interacțiune, dacă nu există un litigiu sau o obligație legală care justifică o perioadă mai lungă. Jurnalele tehnice se păstrează pentru perioade limitate, proporționale cu scopul de securitate.</p>
<h2>8. Drepturile persoanelor vizate</h2>
<p>În condițiile GDPR, puteți solicita accesul, rectificarea, ștergerea, restricționarea, portabilitatea și opoziția la prelucrare, precum și retragerea consimțământului fără afectarea prelucrării anterioare. De asemenea, puteți solicita informații despre garanțiile folosite pentru transferuri internaționale. Cererile se trimit la {$email}; putem solicita informații rezonabile pentru verificarea identității.</p>
<p>Aveți dreptul să depuneți plângere la Autoritatea Națională de Supraveghere a Prelucrării Datelor cu Caracter Personal: <a href="https://www.dataprotection.ro/" target="_blank" rel="noopener">dataprotection.ro</a>.</p>
<h2>9. Cookie-uri și stocare locală</h2>
<p>Site-ul folosește tehnologii strict necesare pentru sesiune, securitate, coș și funcționarea formularelor. Acestea nu necesită consimțământ atunci când sunt indispensabile serviciului solicitat. Dacă vor fi activate instrumente de analiză, publicitate sau alte tehnologii neesențiale, ele trebuie blocate până la exprimarea unei opțiuni valide și descrise într-un mecanism de consimțământ dedicat.</p>
<h2>10. Securitate și decizii automate</h2>
<p>Aplicăm măsuri tehnice și organizatorice adecvate riscului, inclusiv controlul accesului, conexiuni criptate și limitarea datelor. Nu folosim datele pentru decizii exclusiv automatizate care produc efecte juridice sau similare asupra clienților. Stripe poate efectua verificări automate antifraudă potrivit propriei politici.</p>
<h2>11. Actualizări</h2>
<p>Politica poate fi actualizată când se schimbă serviciile sau cerințele legale. Versiunea curentă și data actualizării sunt publicate pe această pagină; modificările importante vor fi comunicate prin mijloace adecvate.</p>
HTML,
            ],
            'politica-de-retur' => [
                'title' => 'Livrare și Retur',
                'content' => <<<HTML
<p><strong>Ultima actualizare: 5 august 2026</strong></p>
<h2>1. Livrarea</h2>
<p>Comenzile sunt livrate în România la adresa introdusă în pagina Stripe. Termenul estimat se comunică în pagina produsului, la checkout sau în confirmarea comenzii. În lipsa unui termen convenit, livrarea are loc fără întârziere nejustificată și cel târziu în termenul legal. Clientul trebuie să furnizeze o adresă și un număr de telefon corecte.</p>
<p>La primire, recomandăm verificarea ambalajului. Deteriorările vizibile trebuie fotografiate și semnalate curierului și operatorului cât mai curând; această recomandare ajută la dovedirea incidentului, dar nu înlătură drepturile legale ale consumatorului.</p>
<h2>2. Dreptul de retragere în 14 zile</h2>
<p>Dacă sunteți consumator, vă puteți retrage din contract fără motiv în termen de 14 zile de la ziua în care dumneavoastră sau persoana indicată de dumneavoastră, alta decât transportatorul, intră în posesia fizică a produsului. Pentru o comandă cu produse livrate separat, termenul curge de la primirea ultimului produs.</p>
<h2>3. Cum exercitați retragerea</h2>
<p>Înainte de expirarea termenului, transmiteți o declarație neechivocă la <a href="mailto:{$email}">{$email}</a>. Includeți numele, numărul comenzii, produsele returnate, data primirii și datele de contact. Puteți folosi textul orientativ:</p>
<blockquote><p>„Vă informez prin prezenta cu privire la retragerea mea din contractul referitor la vânzarea următoarelor produse: [produse], comandate la [data], primite la [data], număr comandă [număr]. Nume, adresă, data.”</p></blockquote>
<p>Vom confirma primirea solicitării pe email. Este suficient să trimiteți declarația înainte de expirarea celor 14 zile.</p>
<h2>4. Trimiterea produselor înapoi</h2>
<p>Produsele trebuie expediate fără întârziere nejustificată și în cel mult 14 zile de la comunicarea retragerii, la adresa indicată în răspunsul nostru. Costul direct al returului este suportat de consumator, cu excepția cazului în care am acceptat expres să îl suportăm sau nu am informat anterior consumatorul despre acest cost. Recomandăm ambalarea adecvată și folosirea unui serviciu cu urmărire.</p>
<h2>5. Rambursarea</h2>
<p>Vom rambursa sumele datorate, inclusiv costul livrării standard inițiale dacă acesta a fost încasat, în cel mult 14 zile de la informarea privind retragerea. Putem amâna rambursarea până la primirea produselor sau până la furnizarea dovezii expedierii, luându-se în considerare data cea mai apropiată. Rambursarea se face prin aceeași metodă de plată, dacă nu convenim expres altfel și fără costuri nejustificate pentru consumator.</p>
<h2>6. Diminuarea valorii</h2>
<p>Consumatorul răspunde numai pentru diminuarea valorii rezultată din manipulări care depășesc ceea ce este necesar pentru a stabili natura, caracteristicile și funcționarea produsului. Produsul poate fi verificat cu grijă, similar unei examinări într-un magazin; folosirea, deteriorarea, murdărirea ori ambalarea necorespunzătoare pot conduce la o diminuare justificată a sumei rambursate.</p>
<h2>7. Produse personalizate</h2>
<p>Dreptul de retragere nu se aplică bunurilor confecționate după specificațiile prezentate de consumator sau personalizate în mod clar. Excepția va fi comunicată înainte de comandă și se aplică numai produsului personalizat. <strong>O piesă unicat, lucrată manual sau cu variații naturale, dar realizată anterior și vândută din stoc, nu devine automat produs personalizat.</strong></p>
<h2>8. Produse neconforme sau deteriorate</h2>
<p>Retragerea nu trebuie confundată cu garanția legală de conformitate. Dacă produsul este neconform, greșit ori ajunge deteriorat, contactați-ne la {$email} cu numărul comenzii și dovezi utile. Se aplică măsurile corective și termenele prevăzute de OUG nr. 140/2021; nu vi se vor impune costurile unui retur justificat de neconformitate.</p>
<h2>9. Anularea înainte de expediere</h2>
<p>Dacă doriți anularea imediat după plată, contactați-ne cât mai repede. Dacă produsul nu a fost expediat și comanda poate fi oprită, vom confirma anularea și iniția rambursarea. Pentru produsele personalizate a căror execuție a început la cererea clientului se aplică acordul și regulile comunicate înainte de comandă.</p>
<h2>10. Contact și soluționare alternativă</h2>
<p>Operator: {$business}, {$address}; email <a href="mailto:{$email}">{$email}</a>; telefon {$phone}. Dacă o reclamație nu este soluționată amiabil, consumatorii pot accesa mecanismul SAL al ANPC la <a href="https://reclamatiisal.anpc.ro/" target="_blank" rel="noopener">reclamatiisal.anpc.ro</a>.</p>
HTML,
            ],
        ];

        foreach ($pages as $slug => $page) {
            DB::table('pages')->updateOrInsert(
                ['slug' => $slug],
                [
                    'title' => json_encode(['ro' => $page['title']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'content' => json_encode(['ro' => $page['content']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at' => $updatedAt,
                    'updated_at' => $updatedAt,
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('pages')->whereIn('slug', [
            'termeni-si-conditii',
            'politica-de-confidentialitate',
            'politica-de-retur',
        ])->delete();
    }
};
