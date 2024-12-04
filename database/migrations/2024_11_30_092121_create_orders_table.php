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
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('account_id');
            $table->decimal('total_amount', 10, 2);
            $table->dateTime('pickup_time');
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'preparing', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
    
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
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
