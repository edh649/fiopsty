<?php

namespace App\Support\Spotify\Modify;

use App\Support\Spotify\SpotifyAbstract;
use SpotifyWebAPI\SpotifyWebAPI;

class PlaylistModifier extends SpotifyAbstract
{
    
    public function createPlaylist(string $name): string
    {
        $playlistId = $this->useApiWithUserTokenRefresh(function (SpotifyWebAPI $api) use ($name) {
            $resp = $api->createPlaylist([
                "name" => $name,
                "public" => false
            ]);
            
            return $resp->id;
        });
        
        return $playlistId;
    }
    
    public function addSongsToPlaylist(string $playlist_id, array $song_ids)
    {
        $this->useApiWithUserTokenRefresh(function (SpotifyWebAPI $api) use ($playlist_id, $song_ids) {
            $resp = $api->addPlaylistTracks(
                $playlist_id, $song_ids
            );
        });
    }
}
