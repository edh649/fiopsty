<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
use App\Jobs\Spotify\ImportSavedSongsDispatcher;
use App\Support\Spotify\Import\SavedSongsImporter;
use Illuminate\Support\Facades\Auth;

class SpotifyLibraryController extends Controller
{
    public function dispatchImportSavedSongs()
    {
        //temp no job
        $importSavedSongs = new SavedSongsImporter(Auth::user(), 50, 0);
        $importSavedSongs->import();
        
        // ImportSavedSongsDispatcher::dispatch(Auth::user());
    }
}
