<?php

use App\Models\Listing;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique();
        });

        Listing::query()->whereNull('uuid')->each(function (Listing $listing) {
            $listing->uuid = (string)Str::uuid();
            $listing->save();
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            //
        });
    }
};
