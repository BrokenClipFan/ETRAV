<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('package_price', 10, 2)->default(0)->after('description');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('quoted_price', 10, 2)->nullable()->after('total_price');
            $table->boolean('is_custom')->default(false)->after('package_id');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('package_price');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['quoted_price', 'is_custom']);
        });
    }
};
