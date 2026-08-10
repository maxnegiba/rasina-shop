<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('custom_requests')
            ->whereNotNull('reference_image_path')
            ->orderBy('id')
            ->chunkById(100, function ($requests): void {
                foreach ($requests as $request) {
                    $path = (string) $request->reference_image_path;

                    if ($path === '' || Storage::disk('local')->exists($path)) {
                        continue;
                    }

                    if (! Storage::disk('public')->exists($path)) {
                        continue;
                    }

                    $contents = Storage::disk('public')->get($path);

                    if (Storage::disk('local')->put($path, $contents)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            });
    }

    public function down(): void
    {
        // Intentionally irreversible: moving customer uploads back to public storage would reintroduce a privacy issue.
    }
};
