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
        Schema::create('rental_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_unit_id')->constrained('rental_units')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('price', 15, 2);
            $table->timestamps();

            $table->unique(['rental_unit_id', 'type']); // Tipa Unit hanya bisa punya 1 tipe harga
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_unit_prices');
    }
};
