@extends('layouts.app')
@section('content')
<section class="paper-grid border-y border-stone-200 dark:border-stone-700">
    <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-24 lg:px-12">
        <div class="max-w-3xl"><p class="eyebrow">The worship shelf / 01</p><h1 class="serif mt-5 text-5xl leading-[.98] tracking-tight sm:text-7xl">Your songs.<br><em>Your keys.</em><br>Your worship.</h1><p class="mt-7 max-w-lg text-base leading-7 text-stone-600 dark:text-stone-300">A simple, shared library for the songs that bring our church together.</p></div>
        <div class="library-search mt-12"><label class="library-search-box"><span>⌕</span><input data-library-search data-url="{{ route('songs.search') }}" placeholder="Search all songs or artists..." autocomplete="off"></label><span data-library-search-status class="mono text-xs text-stone-400"></span><div data-library-search-results class="library-search-results"></div><div data-library-search-pagination class="library-search-pagination"></div></div>
        <div class="mt-16 flex items-end justify-between"><div><p class="eyebrow">Browse by</p><h2 class="mt-2 text-2xl font-bold tracking-tight">Song leaders</h2></div><span class="mono text-xs text-stone-400">{{ count($leaders) }} leaders / {{ collect($leaders)->sum('songs') }} songs</span></div>
        @if(session('success'))<div class="success-note">{{ session('success') }}</div>@endif
        <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($leaders as $leader)
            <a href="{{ route('leaders.show', $leader['slug']) }}" class="leader-card leader-{{ $leader['color'] }}"><div class="flex items-start justify-between"><span class="avatar">{{ $leader['initials'] }}</span><span class="card-arrow">↗</span></div><div class="mt-12"><h3 class="text-xl font-bold">{{ $leader['name'] }}</h3><p class="mt-1 text-sm opacity-65">{{ $leader['role'] }}</p><p class="mono mt-5 text-xs opacity-75">{{ $leader['songs'] }} songs</p></div></a>
            @endforeach
        </div>
        <form method="POST" action="{{ route('leaders.store') }}" class="add-leader-form mt-10">@csrf<label class="field flex-1"><span>Add your own song leader</span><input name="name" required placeholder="e.g. Sis. Dorothy"></label><button class="button-primary" type="submit">Add leader <span>＋</span></button></form>
    </div>
</section>
@endsection
