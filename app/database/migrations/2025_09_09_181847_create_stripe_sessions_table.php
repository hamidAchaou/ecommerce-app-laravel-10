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
        Schema::create('stripe_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('cart_items');
            $table->json('client_data')->nullable();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_sessions');
    }
};