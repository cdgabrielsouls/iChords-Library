@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-5xl px-5 py-10 sm:px-8 lg:px-12">
    <a href="{{ route('leaders.show', $song->leaders()->first()?->slug ?? '') }}" class="back-link">← Back to lineup</a>
    <div class="mt-14"><p class="eyebrow">Add chords / 05</p><h1 class="serif mt-3 text-5xl tracking-tight sm:text-6xl">Place the chords.</h1><p class="mt-4 max-w-2xl text-stone-500">Each box sits above one word. Leave a box empty when there is no chord change there.</p></div>
    @if(session('success'))<div class="success-note">{{ session('success') }}</div>@endif
    <form method="POST" action="{{ route('songs.chords.update', $song->slug) }}" class="mt-12">@csrf @method('PUT')
        <div class="chord-editor">
            @foreach($song->content ?? [] as $lineIndex => $line)
                @php($words = preg_split('/\s+/', trim((string) ($line[1] ?? '')), -1, PREG_SPLIT_NO_EMPTY))
                @if(count($words))
                    <div class="chord-editor-line">
                        @foreach($words as $wordIndex => $word)
                            <label class="word-unit"><input name="chords[{{ $lineIndex }}][{{ $wordIndex }}]" maxlength="20" placeholder="" aria-label="Chord above {{ $word }}"><span>{{ $word }}</span></label>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
        <div class="mt-10 flex items-center justify-between border-t border-stone-200 pt-7 dark:border-stone-700"><a href="{{ route('leaders.show', $song->leaders()->first()?->slug ?? '') }}" class="text-sm font-semibold text-stone-500">Skip for now</a><button class="button-primary" type="submit">Save chords <span>↗</span></button></div>
    </form>
</div>
@endsection
