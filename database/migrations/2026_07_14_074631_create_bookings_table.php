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

            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade'); // Added this!

            // Date and Location Inputs
            $table->dateTime('pickup_datetime'); // Perfectly captures Pickup Date + Pickup Time combined
            $table->decimal('latitude', 11, 8);  // Matches the "Choose on Map" target coordinates
            $table->decimal('longitude', 11, 8);
            $table->integer('pax');              // Matches "Number of Heads"

            // Financial Ledger Data
            $table->decimal('total_price', 10, 2);   // Increased precision to 10 to handle larger totals securely
            $table->decimal('deposit_amount', 10, 2); // Added this to match your 25% Deposit feature!
            
            // Pipeline Management
            $table->string('status')->default('pending'); // Added this to manage the reservation lifecycle

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
