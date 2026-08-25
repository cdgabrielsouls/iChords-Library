<?php

use App\Http\Controllers\LibraryController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login')->name('login.store');
        Route::get('/register', 'showRegister')->name('register');
        Route::post('/register', 'register')->name('register.store');
    });
    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});

Route::middleware('auth')->controller(AuthController::class)->group(function () {
    Route::get('/settings', 'settings')->name('settings');
    Route::put('/settings/profile', 'updateProfile')->name('settings.profile');
    Route::put('/settings/password', 'updatePassword')->name('settings.password');
    Route::delete('/settings/account', 'deleteAccount')->name('settings.account.destroy');
});

Route::middleware('auth')->controller(LibraryController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::post('/leaders', 'storeLeader')->name('leaders.store');
    Route::get('/leaders/{slug}', 'leader')->name('leaders.show');
    Route::get('/leaders/{slug}/songs/search', 'search')->name('leaders.search');
    Route::get('/leaders/{leaderSlug}/songs/create', 'createSong')->name('songs.create');
    Route::post('/leaders/{leaderSlug}/songs', 'storeSong')->name('songs.store');
    Route::get('/songs/{slug}/chords/edit', 'editChords')->name('songs.chords.edit');
    Route::put('/songs/{slug}/chords', 'updateChords')->name('songs.chords.update');
    Route::get('/songs/{slug}', 'song')->name('songs.show');
    Route::delete('/songs/{slug}', 'deleteSong')->name('songs.destroy');
    Route::delete('/leaders/{slug}', 'deleteLeader')->name('leaders.destroy');
});
