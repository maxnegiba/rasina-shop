<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('utm_source', 120)->nullable()->index();
            $table->string('utm_medium', 120)->nullable()->index();
            $table->string('utm_campaign', 180)->nullable()->index();
            $table->string('utm_content', 180)->nullable();
            $table->string('utm_term', 180)->nullable();
            $table->json('marketing_attribution')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['utm_source']);
            $table->dropIndex(['utm_medium']);
            $table->dropIndex(['utm_campaign']);
            $table->dropColumn([
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_content',
                'utm_term',
                'marketing_attribution',
            ]);
        });
    }
};
