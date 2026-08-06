<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('public_token')->nullable()->after('order_number');
            $table->string('stripe_checkout_session_id')->nullable()->after('stripe_transaction_id');
            $table->timestamp('stock_reserved_at')->nullable();
            $table->timestamp('stock_released_at')->nullable();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->string('terms_version')->nullable();
            $table->timestamp('confirmation_sent_at')->nullable();
        });

        DB::table('orders')
            ->whereNull('public_token')
            ->orderBy('id')
            ->each(function (object $order): void {
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['public_token' => (string) Str::uuid()]);
            });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('public_token');
            $table->unique('stripe_checkout_session_id');
            $table->unique('stripe_transaction_id');
            $table->unique('proforma_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropUnique(['stripe_checkout_session_id']);
            $table->dropUnique(['stripe_transaction_id']);
            $table->dropUnique(['proforma_number']);

            $table->dropColumn([
                'public_token',
                'stripe_checkout_session_id',
                'stock_reserved_at',
                'stock_released_at',
                'terms_accepted_at',
                'terms_version',
                'confirmation_sent_at',
            ]);
        });
    }
};
