<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('product_code', 32)->nullable()->after('product_name');
        });

        DB::table('order_items')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->select([
                'order_items.id',
                'products.name',
                'products.product_code',
            ])
            ->orderBy('order_items.id')
            ->each(function (object $item): void {
                $name = json_decode((string) $item->name, true);

                DB::table('order_items')
                    ->where('id', $item->id)
                    ->update([
                        'product_name' => is_array($name)
                            ? ($name['ro'] ?? (reset($name) ?: null))
                            : ($item->name ?: null),
                        'product_code' => $item->product_code,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['product_name', 'product_code']);
        });
    }
};
