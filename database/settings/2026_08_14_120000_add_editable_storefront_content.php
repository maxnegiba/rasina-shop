<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.contact_eyebrow', 'Contact');
        $this->migrator->add('general.contact_title', 'Ne Găsiți Aici');
        $this->migrator->add('general.contact_intro', 'Suntem deschiși la discuții, idei noi și, bineînțeles, cereri personalizate. Deoarece piesele noastre necesită un proces meticulos, fiecare proiect începe cu o conversație.');
        $this->migrator->add('general.contact_address_label', 'Adresă Atelier');
        $this->migrator->add('general.contact_address_note', 'Vizite doar cu programare prealabilă');
        $this->migrator->add('general.contact_communication_label', 'Comunicare');
        $this->migrator->add('general.contact_hours_label', 'Program Atelier');
        $this->migrator->add('general.contact_custom_card_title', 'Comenzi Unicat');
        $this->migrator->add('general.contact_custom_card_text', 'Ai o idee specifică? Dorești un produs din rășină cu dimensiuni sau culori particulare?');
        $this->migrator->add('general.contact_custom_card_cta', 'Spre Formular Unicat');
        $this->migrator->add('general.contact_form_title', 'Lăsați-ne un mesaj');
        $this->migrator->add('general.contact_form_response_note', 'Răspundem de obicei în 24-48 de ore lucrătoare.');
        $this->migrator->add('general.contact_custom_eyebrow', 'Proiecte Speciale');
        $this->migrator->add('general.contact_custom_title', 'Comenzi Personalizate');
        $this->migrator->add('general.contact_custom_intro', 'Vă rugăm să ne oferiți detalii despre viziunea dumneavoastră. Tipul de lemn dorit, nuanța rășinii, dimensiunile aproximative și scopul final al piesei de artă.');

        $this->migrator->add('general.about_eyebrow', 'Povestea Noastră');
        $this->migrator->add('general.about_title', 'Arta Născută din Pasiune');
        $this->migrator->add('general.about_image', '');
        $this->migrator->add('general.about_image_alt', 'Atelierul MTD Art');
        $this->migrator->add('general.about_quote', 'MTD Art a pornit de la fascinația pentru frumusețea imperfectă a naturii și dorința de a o păstra vie pentru totdeauna.');
        $this->migrator->add('general.about_paragraph_one', 'Fiecare piesă creată în atelierul nostru spune o poveste unică. Folosim esențe de lemn nobil, atent selecționate, adesea cu imperfecțiuni naturale care, sub magia rășinii epoxidice, devin adevărate capodopere vizuale. Nu credem în producția de masă, ci în timpul, atenția și sufletul pus în fiecare creație.');
        $this->migrator->add('general.about_paragraph_two', 'De la blaturi impresionante de masă, până la obiecte de cult delicate și piese comemorative emoționante, procesul nostru este unul meticulos, pur manual. Transluciditatea rășinii, combinată cu inserții de pigmenți, foiță de aur sau elemente naturale, creează o fuziune atemporală între clasic și modern.');
        $this->migrator->add('general.about_value_one_title', 'Măiestrie');
        $this->migrator->add('general.about_value_one_text', 'Finisaje executate la cele mai înalte standarde, cu atenție la fiecare micron și detaliu tehnic.');
        $this->migrator->add('general.about_value_two_title', 'Unicitate');
        $this->migrator->add('general.about_value_two_text', 'Nicio piesă nu este identică cu alta. Lemnul și rășina dictează designul și forma finală.');
        $this->migrator->add('general.about_cta_label', 'Descoperă Galeria');
    }
};
