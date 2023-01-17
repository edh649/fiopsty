<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
// use App\Jobs\Spotify\RecentlyPlayedSongsDispatcher;
use App\Support\Spotify\Import\RecentlyPlayedSongsImporter;
use Illuminate\Support\Facades\Auth;

class SpotifyPlayerController extends Controller
{
    public function dispatchImportRecentlyPlayed()
    {
        //temp no job
        $importRecentlyPlayedSongs = new RecentlyPlayedSongsImporter(Auth::user(), 50);
        $importRecentlyPlayedSongs->import();
        
        // RecentlyPlayedSongsDispatcher::dispatch(Auth::user());
    }
}
