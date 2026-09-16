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
        Schema::table('listing_interests', function (Blueprint $table) {
            $table->dropColumn('interested_username');
            $table->dropColumn('interested_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_interests', function (Blueprint $table) {
            $table->string('interested_username')->nullable();
            $table->string('interested_name')->nullable();
        });
    }
};
