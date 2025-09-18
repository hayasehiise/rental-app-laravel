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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // Relasi tabel
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rental_unit_id')->constrained('rental_units')->cascadeOnDelete();
            $table->foreignId('booking_type_id')->constrained('booking_types')->cascadeOnDelete();
            // Jadwal Booking
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            // Harga dan Diskon
            $table->unsignedBigInteger('price');
            $table->integer('discount')->default(0);
            $table->unsignedBigInteger('final_price');
            // Status booking
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
