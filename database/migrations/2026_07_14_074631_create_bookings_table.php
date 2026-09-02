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
            $table->foreignId('package_id')->nullable();
            $table->foreignId('vehicle_id')->nullable(); // Matches Transport Vehicle selection

            // Date and Location Inputs
            $table->dateTime('pickup_datetime'); // Perfectly captures Pickup Date + Pickup Time combined
            $table->decimal('latitude', 11, 8);  // Matches the "Choose on Map" target coordinates
            $table->decimal('longitude', 11, 8);
            $table->string('pickup_place_name');
            $table->integer('pax');              // Matches "Number of Heads"
            $table->decimal('distance', 10, 2)->default(0); // Captures the total route distance in meters

            // Financial Ledger Data
            $table->decimal('total_price', 10, 2);   // Increased precision to 10 to handle larger totals securely
            $table->decimal('head_price', 10, 2);    // Changed to decimal for exact per head calculation
            $table->decimal('deposit_amount', 10, 2); // Added this to match your 25% Deposit feature!
            $table->decimal('amount_paid', 10, 2)->default(0); // Tracks how much the user actually paid
            $table->boolean('joiners')->default(false);
            
            // Pipeline Management
            $table->string('status')->default('pending'); // Added this to manage the reservation lifecycle
            $table->boolean('notify')->default(true);
            $table->boolean('admin_notify')->default(false);

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
