<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_photos', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        DB::table('pending_photos')
            ->whereNull('uuid')
            ->orderBy('id')
            ->each(function (object $photo): void {
                DB::table('pending_photos')
                    ->where('id', $photo->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            });

        Schema::table('pending_photos', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pending_photos', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }

};
