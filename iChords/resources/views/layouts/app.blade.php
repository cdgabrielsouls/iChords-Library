<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'iChords Library' }}</title>
    <meta name="theme-color" content="#e5b82e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="iChords">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=IBM+Plex+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased transition-colors duration-300">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-6 sm:px-8 lg:px-12">
        <a href="{{ route('home') }}" class="flex items-center gap-3 font-bold tracking-tight"><span class="brand-mark">i</span><span>iChords <span class="font-normal text-stone-500 dark:text-stone-400">Library</span></span></a>
        <div class="flex items-center gap-3"><span class="hidden text-xs font-semibold uppercase tracking-[.2em] text-stone-400 sm:inline">{{ auth()->check() ? auth()->user()->church_name : 'Music ministry' }}</span>@auth<a class="nav-link" href="{{ route('settings') }}">Settings</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-link" type="submit">Sign out</button></form>@endauth<button data-install-app class="install-button" type="button" hidden>Install app</button><button data-theme-toggle class="theme-toggle" aria-label="Toggle dark mode">☼ <span> / </span>☾</button></div>
    </nav>
    <main>@yield('content')</main>
    <footer class="mx-auto mt-20 max-w-7xl px-5 pb-8 sm:px-8 lg:px-12"><div class="border-t border-stone-200 pt-5 text-xs text-stone-400 dark:border-stone-700">A quiet place for the songs we carry together.</div></footer>
</body>
</html>
