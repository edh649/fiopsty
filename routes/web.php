<?php

use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ListenAllController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RandomiserController;
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
})->middleware(['auth'])->name('dashboard');

Route::get('/listen-all', [ListenAllController::class, 'index'])->middleware(['auth'])->name('listen-all');
Route::delete('/listen-all', [ListenAllController::class, 'reset'])->middleware(['auth'])->name('listen-all.reset');
Route::post('/listen-all', [ListenAllController::class, 'generate'])->middleware(['auth'])->name('listen-all.generate-playlist');

Route::get('/randomiser', [RandomiserController::class, 'index'])->middleware(['auth'])->name('randomiser');
Route::post('/randomiser', [RandomiserController::class, 'submit'])->middleware(['auth'])->name('randomiser.submit');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/library', [LibraryController::class, 'index'])->middleware(['auth'])->name('library');
Route::post('/spotify/library/import/dispatch/saved-songs', [SpotifyLibraryController::class, 'dispatchImportSavedSongs'])->name('spotify.library.dispatch-import.saved-songs');
Route::post('/spotify/player/import/recently-played', [SpotifyPlayerController::class, 'importRecentlyPlayed'])->name('spotify.player.import.recently-played');

Route::namespace('Auth')->prefix('auth')->group(function () {
    require __DIR__.'/auth.php';
});
