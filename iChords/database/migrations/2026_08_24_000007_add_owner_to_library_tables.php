<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_leaders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->index('user_id');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('songs', fn (Blueprint $table) => $table->dropConstrainedForeignId('user_id'));
        Schema::table('song_leaders', fn (Blueprint $table) => $table->dropConstrainedForeignId('user_id'));
    }
};
