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
        Schema::create('product_items', function (Blueprint $table) {
            $table->id('product_item_id');
            $table->unsignedBigInteger('product_id');
            $table->string('batch_number');
            $table->date('expiration_date');
            $table->integer('quantity');
            $table->string('status');
            $table->timestamp('added_at');
            $table->timestamps();
    
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
