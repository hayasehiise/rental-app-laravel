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
            // Jadwal Booking
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            // Harga dan Diskon
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('final_price');
            // Status booking
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending')->comment('Status Dari Booking');
            // parent booking (Kalau ada)
            $table->unsignedInteger('parent_booking_id')->nullable()->comment('ID booking induk, jika ini adalah booking berulang');
            // relasi ke table all price
            $table->unsignedBigInteger('price_id')->nullable()->comment('ID dari tabel harga (lapangan, kendaraan, gedung, dll)');
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
