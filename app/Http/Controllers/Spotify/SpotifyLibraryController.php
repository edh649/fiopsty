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
        ImportSavedSongsDispatcher::dispatch(Auth::user());
        return back();
    }
}
