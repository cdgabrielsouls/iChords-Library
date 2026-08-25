@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-5xl px-5 py-10 sm:px-8 lg:px-12">
    <a href="{{ route('home') }}" class="back-link">← Back to library</a>
    <div class="mt-12"><p class="eyebrow">Your library / settings</p><h1 class="serif mt-3 text-5xl tracking-tight sm:text-6xl">Make it yours.</h1><p class="mt-4 max-w-xl text-stone-500">Update your account, choose a library mood, or tidy up your song leaders.</p></div>
    @if(session('success'))<div class="success-note">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="error-note">Please check the highlighted details below.</div>@endif
    <div class="settings-grid mt-12">
        <section class="settings-section">
            <p class="eyebrow">Profile</p><h2 class="settings-title">Church details</h2>
            <form method="POST" action="{{ route('settings.profile') }}" class="mt-7 space-y-5">@csrf @method('PUT')
                <label class="field"><span>Your name</span><input name="name" required value="{{ old('name', $user->name) }}">@error('name')<small>{{ $message }}</small>@enderror</label>
                <label class="field"><span>Church name</span><input name="church_name" required value="{{ old('church_name', $user->church_name) }}">@error('church_name')<small>{{ $message }}</small>@enderror</label>
                <label class="field"><span>Username</span><input name="username" required value="{{ old('username', $user->username) }}">@error('username')<small>{{ $message }}</small>@enderror</label>
                <button class="button-primary" type="submit">Save details <span>↗</span></button>
            </form>
        </section>
        <section class="settings-section">
            <p class="eyebrow">Password</p><h2 class="settings-title">Keep it secure.</h2>
            <form method="POST" action="{{ route('settings.password') }}" class="mt-7 space-y-5">@csrf @method('PUT')
                <label class="field"><span>Current password</span><input type="password" name="current_password" required>@error('current_password')<small>{{ $message }}</small>@enderror</label>
                <label class="field"><span>New password</span><input type="password" name="password" required>@error('password')<small>{{ $message }}</small>@enderror</label>
                <label class="field"><span>Confirm new password</span><input type="password" name="password_confirmation" required></label>
                <button class="button-primary" type="submit">Change password <span>↗</span></button>
            </form>
        </section>
    </div>
    <section class="settings-section mt-5">
        <p class="eyebrow">Appearance</p><h2 class="settings-title">Choose a color mood.</h2>
        <div class="theme-options mt-7" data-theme-options>
            <button type="button" class="theme-choice theme-choice-meadow" data-theme-choice="meadow"><span></span><strong>Meadow</strong><small>Warm and grounded</small></button>
            <button type="button" class="theme-choice theme-choice-sunset" data-theme-choice="sunset"><span></span><strong>Sunset</strong><small>Soft and bright</small></button>
            <button type="button" class="theme-choice theme-choice-ocean" data-theme-choice="ocean"><span></span><strong>Ocean</strong><small>Clear and calm</small></button>
            <button type="button" class="theme-choice theme-choice-rose" data-theme-choice="rose"><span></span><strong>Rose</strong><small>Gentle and lively</small></button>
        </div>
    </section>
    <section class="settings-section mt-5">
        <p class="eyebrow">Song leaders</p><h2 class="settings-title">Manage your lineup.</h2>
        <div class="settings-list mt-7">
            @forelse($leaders as $leader)
                <div class="settings-row"><span><strong>{{ $leader->name }}</strong><small>{{ $leader->songs_count }} songs</small></span><form method="POST" action="{{ route('leaders.destroy', $leader->slug) }}" onsubmit="return confirm('Remove {{ addslashes($leader->name) }} from your song leaders?')">@csrf @method('DELETE')<button class="delete-button" type="submit">Remove</button></form></div>
            @empty
                <p class="empty-state">No song leaders yet.</p>
            @endforelse
        </div>
    </section>
    <section class="danger-zone mt-5">
        <p class="eyebrow">Danger zone</p><h2 class="settings-title">Delete this library.</h2><p class="mt-3 text-sm text-stone-500">This permanently deletes your account, leaders, and songs. Type your current church name to confirm.</p>
        <form method="POST" action="{{ route('settings.account.destroy') }}" class="mt-7 flex flex-col gap-4 sm:flex-row sm:items-end">@csrf @method('DELETE')<label class="field flex-1"><span>Current church name</span><input name="church_name_confirmation" required></label><button class="delete-account-button" type="submit" onclick="return confirm('Delete your entire iChords library?')">Delete account</button></form>
    </section>
</div>
@endsection
