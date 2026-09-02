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
        Schema::create('booking_places', function (Blueprint $table) {
            $table->id();

            // Foreign keys linking your parent booking and the master spot
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('place_id')->nullable()->constrained()->onDelete('cascade');
            
            // Custom payload data for one-off dragged map pins
            $table->string('custom_name')->nullable();
            $table->decimal('custom_latitude', 11, 8)->nullable();
            $table->decimal('custom_longitude', 11, 8)->nullable();
            $table->string('custom_category')->nullable();

            // Custom payload data from your UI inputs
            $table->integer('duration_minutes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_places');
    }
};
