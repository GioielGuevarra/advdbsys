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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('password');
            $table->enum('role', ['customer', 'admin', 'staff']);
            $table->boolean('is_customer')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_staff')->default(false);
            $table->boolean('is_banned')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
