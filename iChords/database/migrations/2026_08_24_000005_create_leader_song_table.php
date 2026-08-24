<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leader_song', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_leader_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['song_leader_id', 'song_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leader_song');
    }
};
