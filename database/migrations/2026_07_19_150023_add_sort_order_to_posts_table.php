<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('id');
        });

        // Populate sort_order sequentially for existing posts
        // Order by published_at DESC, then id DESC to match existing behavior
        $posts = \Illuminate\Support\Facades\DB::table('posts')
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $order = 1;
        foreach ($posts as $post) {
            \Illuminate\Support\Facades\DB::table('posts')
                ->where('id', $post->id)
                ->update(['sort_order' => $order]);
            $order++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
