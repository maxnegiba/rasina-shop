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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Dacă faci sistem de conturi pe viitor
        $table->string('order_number')->unique(); // ex: IVORY-1001
        $table->decimal('total_amount', 10, 2);
        $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
        $table->enum('shipping_status', ['processing', 'shipped', 'delivered'])->default('processing');
        $table->json('customer_details'); // Nume, Adresă, Email, Telefon salvate la momentul comenzii
        $table->string('stripe_transaction_id')->nullable();
        
        // Pregătite pentru Oblio
        $table->string('invoice_series')->nullable();
        $table->string('invoice_number')->nullable();
        
        $table->timestamps();
    });
}
};
