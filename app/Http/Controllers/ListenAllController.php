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
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class ListenAllController extends Controller
{
    public function index()
    {
        $start_date = Auth::user()->listen_all_started_at;
        if (!$start_date) { return view('listen-all'); }
        
        $unique_library_listened_songs = SongUser::where('user_id', Auth::id())
            ->whereNotNull('added_at')
            ->where('last_played_at', '>', $start_date)->count();
        $library_songs_count = SongUser::where('user_id', Auth::id())->whereNotNull('added_at')->count();
        
        $time_gone = Carbon::now()->diffInRealHours($start_date);
        $projected_finish = null;
        if ($unique_library_listened_songs > 0)
        {
            $time_per_track = $time_gone/$unique_library_listened_songs;
            $time_tracks_left = ($library_songs_count-$unique_library_listened_songs)*$time_per_track;
            $projected_finish = Carbon::now()->addRealHours($time_tracks_left);
        }
        
        $playlistModifier = new PlaylistModifier();
        $name = $playlistModifier->getPlaylistName(Auth::user()->spotify_listen_all_playlist_id);
        
        return view('listen-all', [
            "listen_all_start_date" => $start_date,
            "unique_library_listened_songs" => $unique_library_listened_songs,
            "library_songs_count" => $library_songs_count,
            "percentage_complete" => round(100*$unique_library_listened_songs/$library_songs_count, 1),
            "projected_finish" => $projected_finish,
            "spotify_playlist_name" => $name
        ]);
        
    }
    
    public function reset(Request $request)
    {
        Auth::user()->update(["listen_all_started_at" => now()]);
        return redirect()->route('listen-all');
    }
    
    public function generate(Request $request)
    {
        $request->validate([
            "playlist_option" => [
                "required",
                Rule::in([
                    "random",
                    "alphabetical_song",
                    // "alphabetical_album",
                    // "alphabetical_artist",
                    "recently_added",
                    "earliest_added",
                ])
            ]
        ]);
        
        $user = Auth::user();
        
        $playlistModifier = new PlaylistModifier();
        if (!$user->spotify_listen_all_playlist_id || $request->has('create')) {
            $user->update([
                "spotify_listen_all_playlist_id" => $playlistModifier->createPlaylist("Fiopsty Listen All")
            ]);
        }
        
        
        $query = SongUser::where('user_id', $user->id)
            ->whereNotNull('added_at')
            ->where(function ($query) use ($user) {
                $query->where('last_played_at', '<', $user->listen_all_started_at)
                    ->orWhereNull('last_played_at');
            })
            ->join('songs', 'songs.id', '=', 'song_user.song_id');
        switch ($request->input('playlist_option'))
        {
            case "random":
                $query->inRandomOrder();
                break;
            case "alphabetical_song":
                $query->orderBy('name');
                break;
            case "recently_added":
                $query->orderBy('added_at');
                break;
            case "earliest_added":
                $query->orderBy('added_at', 'desc');
                break;
            default:
                throw new InvalidArgumentException("Unexpected playlist option provided.");
        }
        $songs_spotify_ids = $query->select('songs.spotify_id')->limit(100)->get();
        
        $playlistModifier->removeAllTracksFromPlaylist($user->spotify_listen_all_playlist_id);
        $playlistModifier->addSongsToPlaylist($user->spotify_listen_all_playlist_id, $songs_spotify_ids->pluck('spotify_id')->toArray());
        
        Session::flash("alert", ["success" => "Created/updated playlist with ".count($songs_spotify_ids)." unplayed songs!"]);
        
        return redirect()->route('listen-all');
    }
}
