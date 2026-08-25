<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('youtube_url', 500)->nullable()->after('notes');
            $table->string('spotify_url', 500)->nullable()->after('youtube_url');
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['youtube_url', 'spotify_url']);
        });
    }
};