<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('photos_status')->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('phone_verified_at');
            $table->dropColumn('photos_status');
        });
    }

};
