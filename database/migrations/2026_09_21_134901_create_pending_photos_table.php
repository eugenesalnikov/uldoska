<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_photos', function (Blueprint $table) {
            $table->id();
            $table->uuid('owner_token');
            $table->string('disk');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime');
            $table->unsignedInteger('size');
            $table->string('status');
            $table->foreignId('listing_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['owner_token', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_photos');
    }
};
