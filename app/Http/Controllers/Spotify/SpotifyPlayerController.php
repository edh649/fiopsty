<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
// use App\Jobs\Spotify\RecentlyPlayedSongsDispatcher;
use App\Support\Spotify\Import\RecentlyPlayedSongsImporter;
use Illuminate\Support\Facades\Auth;

class SpotifyPlayerController extends Controller
{
    public function importRecentlyPlayed()
    {
        //may as well do it live no job!
        $importRecentlyPlayedSongs = new RecentlyPlayedSongsImporter(Auth::user(), 50);
        $importRecentlyPlayedSongs->import();
        
        return back();
    }
}
