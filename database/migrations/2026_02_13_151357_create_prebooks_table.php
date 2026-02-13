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
        Schema::create('prebooks', function (Blueprint $table) {
            $table->id();
            $table->string('prebook_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('hotel_id');
            $table->string('offer_id');
            $table->date('checkin');
            $table->date('checkout');
            $table->integer('guests_adults')->default(2);
            $table->string('holder_first_name')->nullable();
            $table->string('holder_last_name')->nullable();
            $table->string('holder_email')->nullable();
            $table->string('status')->default('initiated');
            $table->json('pricing')->nullable();
            $table->string('booking_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prebooks');
    }
};
