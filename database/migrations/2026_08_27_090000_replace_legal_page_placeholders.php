<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use RuntimeException;

return new class extends Migration
{
    /**
     * Replace only the original placeholder tokens that may have been persisted
     * when the legal pages were first seeded. Existing editorial changes remain
     * untouched.
     */
    public function up(): void
    {
        $replacements = $this->legalReplacements();

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
     * This migration repairs production data and is intentionally irreversible:
     * rolling it back must not reintroduce invalid public placeholders.
     */
    public function down(): void
    {
        // No-op by design.
    }

    private function legalReplacements(): array
    {
        $values = [
            '[DENUMIREA LEGALĂ A OPERATORULUI]' => trim((string) config('shop.legal.business_name')),
            '[CUI/CIF]' => trim((string) config('shop.legal.tax_id')),
            '[NR. REGISTRUL COMERȚULUI]' => trim((string) config('shop.legal.trade_register')),
            '[ADRESA SEDIULUI SOCIAL]' => trim((string) config('shop.legal.address')),
            '[TELEFON]' => trim((string) config('shop.legal.phone')),
        ];

        foreach ($values as $placeholder => $value) {
            if ($value === '' || $value === $placeholder || str_starts_with($value, '[')) {
                throw new RuntimeException(
                    "Cannot repair legal pages: configure a real value for {$placeholder} before running migrations.",
                );
            }
        }

        return $values;
    }
};
