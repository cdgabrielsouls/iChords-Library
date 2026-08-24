<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'slug', 'artist', 'original_key', 'content', 'notes', 'created_by', 'user_id'])]
class Song extends Model
{
    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    public function leaders(): BelongsToMany
    {
        return $this->belongsToMany(SongLeader::class, 'leader_song')->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
