<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->index(['telegram_chat_id', 'status']);
            $table->index(['status', 'expires_at']);
        });

        Schema::table('listing_interests', function (Blueprint $table) {
            $table->index(['interested_chat_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['telegram_chat_id', 'status']);
            $table->dropIndex(['status', 'expires_at']);
        });

        Schema::table('listing_interests', function (Blueprint $table) {
            $table->dropIndex(['interested_chat_id', 'status']);
        });
    }

};
