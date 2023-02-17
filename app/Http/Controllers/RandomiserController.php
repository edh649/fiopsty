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

class RandomiserController extends Controller
{
    public function index()
    {
        return view('randomiser');
    }
    
    public function submit(Request $request)
    {
        $request->validate([
            "length" => "numeric|min:1|max:100"
        ]);
        $songUser = SongUser::where('user_id', Auth::user()->id)->whereNotNull('added_at')->inRandomOrder()->limit($request->input('length', 50))->select('song_id')->get();
        $songs = Song::whereIn('id', $songUser->pluck('song_id'))->get();
        
        $playlistModifier = new PlaylistModifier();
        $playlistId = $playlistModifier->createPlaylist("Fiopsty Random Playlist");
        $playlistModifier->addSongsToPlaylist($playlistId, $songs->pluck('spotify_id')->toArray());
        
        return view('randomiser', [
            "alert" => [
                "success" => "Random playlist created!"
            ]
        ]);
    }
}
