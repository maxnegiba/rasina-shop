<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Repair legal-page content that may have persisted the original seed
     * placeholders. The company identity below is public legal information and
     * matches the production template committed with the application.
     *
     * Only the exact placeholder tokens are replaced, so later editorial edits
     * made from Filament remain untouched.
     */
    public function up(): void
    {
        $replacements = [
            '[DENUMIREA LEGALĂ A OPERATORULUI]' => 'MTD STUDIO PROFESSIONAL SRL',
            '[CUI/CIF]' => '52534613',
            '[NR. REGISTRUL COMERȚULUI]' => 'J2025071368008',
            '[ADRESA SEDIULUI SOCIAL]' => 'Str. Piscului 14, Loc. Vulcan, Jud. Hunedoara, Cod 336200, România',
            '[TELEFON]' => '+40 771 768 582',
        ];

        DB::table('pages')
            ->whereIn('slug', [
                'termeni-si-conditii',
                'politica-de-confidentialitate',
                'politica-de-retur',
            ])
            ->orderBy('id')
            ->each(function (object $page) use ($replacements): void {
                $translations = json_decode((string) $page->content, true);

                if (! is_array($translations)) {
                    return;
                }

                $changed = false;

                foreach ($translations as $locale => $content) {
                    if (! is_string($content)) {
                        continue;
                    }

                    $updated = str_replace(
                        array_keys($replacements),
                        array_values($replacements),
                        $content,
                    );

                    if ($updated === $content) {
                        continue;
                    }

                    $translations[$locale] = $updated;
                    $changed = true;
                }

                if (! $changed) {
                    return;
                }

                DB::table('pages')
                    ->where('id', $page->id)
                    ->update([
                        'content' => json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at' => now(),
                    ]);
            });
    }

    /**
     * This is a production-data repair. Rolling back must not reintroduce
     * invalid public placeholders.
     */
    public function down(): void
    {
        // No-op by design.
    }
};
