<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceRomanianContent([
            '<strong>Ultima actualizare: 5 august 2026</strong>' => '<strong>Ultima actualizare: 6 august 2026</strong>',
            'Totalul datorat este afișat înainte de continuarea către Stripe.' => 'Totalul datorat este afișat înainte de confirmarea plății în formularul securizat Stripe integrat în site.',
            'Clientul verifică produsele, acceptă termenii, apoi este redirecționat către pagina securizată Stripe.' => 'Clientul verifică produsele, acceptă termenii, apoi finalizează plata în formularul Stripe securizat integrat în site.',
            'Plata online se efectuează cu cardul prin Stripe.' => 'Plata online se efectuează prin Stripe, folosind cardul sau portofele digitale compatibile, atunci când acestea sunt disponibile pe dispozitivul clientului și sunt active în Stripe.',
            'Livrarea se face la adresa introdusă în Stripe, prin curier' => 'Livrarea se face la adresa introdusă în formularul securizat de checkout, prin curier',
            'Comenzile sunt livrate în România la adresa introdusă în pagina Stripe.' => 'Comenzile sunt livrate în România la adresa introdusă în formularul securizat de checkout.',
        ]);
    }

    public function down(): void
    {
        $this->replaceRomanianContent([
            '<strong>Ultima actualizare: 6 august 2026</strong>' => '<strong>Ultima actualizare: 5 august 2026</strong>',
            'Totalul datorat este afișat înainte de confirmarea plății în formularul securizat Stripe integrat în site.' => 'Totalul datorat este afișat înainte de continuarea către Stripe.',
            'Clientul verifică produsele, acceptă termenii, apoi finalizează plata în formularul Stripe securizat integrat în site.' => 'Clientul verifică produsele, acceptă termenii, apoi este redirecționat către pagina securizată Stripe.',
            'Plata online se efectuează prin Stripe, folosind cardul sau portofele digitale compatibile, atunci când acestea sunt disponibile pe dispozitivul clientului și sunt active în Stripe.' => 'Plata online se efectuează cu cardul prin Stripe.',
            'Livrarea se face la adresa introdusă în formularul securizat de checkout, prin curier' => 'Livrarea se face la adresa introdusă în Stripe, prin curier',
            'Comenzile sunt livrate în România la adresa introdusă în formularul securizat de checkout.' => 'Comenzile sunt livrate în România la adresa introdusă în pagina Stripe.',
        ]);
    }

    private function replaceRomanianContent(array $replacements): void
    {
        DB::table('pages')
            ->whereIn('slug', [
                'termeni-si-conditii',
                'politica-de-retur',
            ])
            ->orderBy('id')
            ->each(function (object $page) use ($replacements): void {
                $translations = json_decode((string) $page->content, true);

                if (! is_array($translations) || ! isset($translations['ro']) || ! is_string($translations['ro'])) {
                    return;
                }

                $updated = str_replace(
                    array_keys($replacements),
                    array_values($replacements),
                    $translations['ro'],
                );

                if ($updated === $translations['ro']) {
                    return;
                }

                $translations['ro'] = $updated;

                DB::table('pages')
                    ->where('id', $page->id)
                    ->update([
                        'content' => json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at' => now(),
                    ]);
            });
    }
};
