<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'product_code')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->string('product_code', 32)->nullable()->unique()->after('id');
            });
        }

        if (! Schema::hasColumn('products', 'product_type')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->string('product_type', 64)->nullable()->index()->after('product_code');
            });
        }

        if (! Schema::hasColumn('products', 'related_post_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->foreignId('related_post_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('posts')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('products', 'seo_translations')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->json('seo_translations')->nullable()->after('description');
            });
        }

        if (! Schema::hasColumn('posts', 'seo_translations')) {
            Schema::table('posts', function (Blueprint $table): void {
                $table->json('seo_translations')->nullable()->after('content');
            });
        }

        if (! Schema::hasColumn('product_images', 'sort_order')) {
            Schema::table('product_images', function (Blueprint $table): void {
                $table->unsignedInteger('sort_order')->default(0)->index()->after('is_featured');
            });
        }

        if (! Schema::hasColumn('product_images', 'alt_text')) {
            Schema::table('product_images', function (Blueprint $table): void {
                $table->json('alt_text')->nullable()->after('sort_order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'related_post_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('related_post_id');
            });
        }

        foreach ([
            ['products', 'seo_translations'],
            ['products', 'product_type'],
            ['products', 'product_code'],
            ['posts', 'seo_translations'],
            ['product_images', 'alt_text'],
            ['product_images', 'sort_order'],
        ] as [$tableName, $column]) {
            if (Schema::hasColumn($tableName, $column)) {
                Schema::table($tableName, function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
