<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\Spotify\ImportSavedSongsDispatcher;
use App\Models\Song;
use App\Models\SongUser;
use App\Support\Spotify\Import\SavedSongsImporter;
use App\Support\Spotify\Modify\PlaylistModifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        return view('library', [
            "song_count" => SongUser::where('user_id', Auth::id())->whereNotNull('added_at')->count()
        ]);
    }
}
