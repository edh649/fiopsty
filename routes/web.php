<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Spotify\SpotifyLibraryController;
use App\Http\Controllers\Spotify\SpotifyPlayerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // dd(\App\Models\SongUser::all()->toArray());
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/spotify/library/dispatch-import/saved-songs', [SpotifyLibraryController::class, 'dispatchImportSavedSongs'])->name('spotify.library.dispatch-import.saved-songs');
Route::post('/spotify/player/dispatch-import/recently-played', [SpotifyPlayerController::class, 'dispatchImportRecentlyPlayed'])->name('spotify.player.dispatch-import.recently-played');

Route::namespace('Auth')->prefix('auth')->group(function () {
    require __DIR__.'/auth.php';
});
