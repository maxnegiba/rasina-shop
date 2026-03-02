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
    Schema::create('custom_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // Dacă cererea pleacă de la un produs de portofoliu
        $table->string('customer_name');
        $table->string('customer_email');
        $table->string('customer_phone')->nullable();
        $table->string('dimensions_requested')->nullable();
        $table->string('color_preferences')->nullable();
        $table->text('special_message')->nullable(); // Pentru plăcile comemorative
        $table->string('reference_image_path')->nullable(); // Poza încărcată de client
        $table->enum('status', ['new', 'in_discussion', 'quote_sent', 'accepted', 'rejected'])->default('new');
        $table->decimal('quoted_price', 10, 2)->nullable(); // Prețul oferit de tine
        $table->timestamps();
    });
}
};
