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
        Schema::create('gedung_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pax', 'day']);
            $table->unsignedInteger('pax')->nullable();
            $table->unsignedInteger('day_number')->nullable();
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gedung_prices');
    }
};
