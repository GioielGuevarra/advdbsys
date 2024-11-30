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
            $table->id('order_id');
            $table->string('order_number')->unique();  // Added for receipt tracking
            $table->string('customer_name');
            $table->enum('status', ['pending', 'preparing', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->unsignedBigInteger('account_id')->nullable();  // Made optional for staff assignment
            $table->timestamps();
    
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
