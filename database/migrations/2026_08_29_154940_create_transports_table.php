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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();

            $table->string('brand');
            $table->string('model');
            $table->string('plate_number')->unique();
            $table->integer('capacity');
            $table->string('status');
            $table->string('front_image_path');
            $table->string('side_image_path');
            $table->string('plate_image_path');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
