<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('name');
            $table->unsignedTinyInteger('adults')->default(2);
            $table->string('status')->default('planning'); // planning, prebooked, booked
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
