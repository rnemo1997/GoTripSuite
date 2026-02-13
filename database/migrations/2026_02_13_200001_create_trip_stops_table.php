<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);

            // Place info
            $table->string('place_id');
            $table->string('place_name');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Dates
            $table->date('checkin')->nullable();
            $table->date('checkout')->nullable();
            $table->unsignedTinyInteger('nights')->default(1);

            // Hotel selection
            $table->string('hotel_id')->nullable();
            $table->string('hotel_name')->nullable();
            $table->string('hotel_photo')->nullable();
            $table->string('offer_id')->nullable();
            $table->decimal('hotel_price', 10, 2)->nullable();
            $table->string('hotel_currency', 3)->nullable();

            // Booking
            $table->string('prebook_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('booking_id')->nullable();
            $table->string('booking_status')->nullable();
            $table->json('pricing')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_stops');
    }
};
