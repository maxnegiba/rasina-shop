<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'related_post_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->unsignedBigInteger('related_post_id')->nullable()->after('category_id');
                $table->foreign('related_post_id')->references('id')->on('posts')->nullOnDelete();
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

        Schema::table('product_images', function (Blueprint $table): void {
            if (! Schema::hasColumn('product_images', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_featured');
            }
            if (! Schema::hasColumn('product_images', 'alt_text')) {
                $table->json('alt_text')->nullable()->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'related_post_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropForeign(['related_post_id']);
                $table->dropColumn('related_post_id');
            });
        }

        if (Schema::hasColumn('products', 'seo_translations')) {
            Schema::table('products', fn (Blueprint $table) => $table->dropColumn('seo_translations'));
        }

        if (Schema::hasColumn('posts', 'seo_translations')) {
            Schema::table('posts', fn (Blueprint $table) => $table->dropColumn('seo_translations'));
        }

        Schema::table('product_images', function (Blueprint $table): void {
            $columns = [];
            if (Schema::hasColumn('product_images', 'sort_order')) {
                $columns[] = 'sort_order';
            }
            if (Schema::hasColumn('product_images', 'alt_text')) {
                $columns[] = 'alt_text';
            }
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
