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
        Schema::create('lapangan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_unit_id')->constrained('rental_units')->cascadeOnDelete();
            $table->decimal('guest_price', 15, 2);
            $table->decimal('member_price', 15, 2);
            $table->integer('member_quota')->default(4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lapangan_prices');
    }
};
