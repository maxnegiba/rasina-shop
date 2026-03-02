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
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
        $table->json('name'); // RO / EN
        $table->string('slug')->unique(); // ex: blaturi-rasina
        $table->json('description')->nullable(); // RO / EN
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
