<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('public_code', 8)->nullable();
        });

        DB::table('listings')
            ->orderBy('id')
            ->select('id')
            ->chunkById(100, function ($listings) {
                foreach ($listings as $listing) {
                    do {
                        $code = Str::random(8);
                    } while (
                        DB::table('listings')->where('public_code', $code)->exists()
                    );

                    DB::table('listings')
                        ->where('id', $listing->id)
                        ->update(['public_code' => $code]);
                }
            });

        Schema::table('listings', function (Blueprint $table) {
            $table->string('public_code', 8)->nullable(false)->unique()->change();
            $table->dropColumn('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->uuid('uuid')->nullable();
        });

        DB::table('listings')
            ->orderBy('id')
            ->select('id')
            ->chunkById(100, function ($listings) {
                foreach ($listings as $listing) {
                    DB::table('listings')
                        ->where('id', $listing->id)
                        ->update(['uuid' => (string)Str::uuid7()]);
                }
            });

        Schema::table('listings', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
            $table->dropUnique(['public_code']);
            $table->dropColumn('public_code');
        });
    }

};
