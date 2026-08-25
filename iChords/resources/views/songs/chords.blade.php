@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-5xl px-5 py-10 sm:px-8 lg:px-12">
    <a href="{{ route('leaders.show', $song->leaders()->first()?->slug ?? '') }}" class="back-link">← Back to lineup</a>
    <div class="mt-14"><p class="eyebrow">Edit song / 05</p><h1 class="serif mt-3 text-5xl tracking-tight sm:text-6xl">{{ $song->title }}</h1><p class="mt-4 max-w-2xl text-stone-500">Edit the chord line above each lyric, then save your arrangement.</p></div>
    @if(session('success'))<div class="success-note">{{ session('success') }}</div>@endif
    <form method="POST" action="{{ route('songs.chords.update', $song->slug) }}" class="mt-12">@csrf @method('PUT')
        <div class="chord-editor">
            @foreach($song->content ?? [] as $lineIndex => $line)
                @if(trim((string) ($line[1] ?? '')) !== '')
                    <div class="chord-editor-line">
                        <label class="chord-row"><textarea name="chords[{{ $lineIndex }}]" maxlength="500" rows="1" wrap="off" aria-label="Chords above lyric line {{ $lineIndex + 1 }}" placeholder="Click here to add chords">{{ $line[0] ?? '' }}</textarea></label>
                        <label class="lyric-row"><textarea name="lyrics[{{ $lineIndex }}]" rows="1" aria-label="Lyrics line {{ $lineIndex + 1 }}">{{ $line[1] ?? '' }}</textarea></label>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="mt-10 flex items-center justify-between border-t border-stone-200 pt-7 dark:border-stone-700"><a href="{{ route('leaders.show', $song->leaders()->first()?->slug ?? '') }}" class="text-sm font-semibold text-stone-500">Skip for now</a><button class="button-primary" type="submit">Save chords <span>↗</span></button></div>
    </form>
</div>
@endsection
