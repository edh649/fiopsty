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
    
    public function removeAllTracksFromPlaylist(string $playlist_id)
    {
        $this->useApiWithUserTokenRefresh(function (SpotifyWebAPI $api) use ($playlist_id) {
            $next = true;
            while ($next)
            {
                $resp = $api->getPlaylistTracks($playlist_id, [
                    "limit" => 50, 
                    "fields" => "next,items(track(id))",
                ]);
                $next = $resp->next;
                
                $songs = [];
                foreach (array_column(array_column($resp->items, 'track'), 'id') as $id)
                {
                    $songs[] = ["uri" => $id];
                }
                $resp = $api->deletePlaylistTracks($playlist_id, ["tracks" => $songs]);
            }
        });
    }
    
    public function getPlaylistName(string $playlist_id)
    {
        return $this->useApiWithUserTokenRefresh(function (SpotifyWebAPI $api) use ($playlist_id) {
            $playlist = $api->getPlaylist($playlist_id, [
                // "fields" => "name,followers"
            ]);
            return $playlist->name;
        });
    }
}
