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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->json('name'); // RO / EN
        $table->string('slug')->unique();
        $table->json('description')->nullable(); // RO / EN
        $table->decimal('price', 10, 2)->nullable(); // Nullable pentru cele custom
        $table->integer('stock')->default(0); // 0 sau null pentru cele custom
        $table->boolean('is_custom')->default(false); // Magia care separă fluxurile
        $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
        $table->timestamps();
    });
}
};