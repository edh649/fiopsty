<?php

namespace App\Support\Spotify\Import;

use App\Models\Song;
use App\Models\User;
use Exception;
use InvalidArgumentException;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

//This class is messy and needs splitting out!
class SavedSongsImporter
{
    protected User $user;
    protected int $limit;
    protected int $offset;
    protected Session $session;
    
    public function __construct(User $user, int $limit, int $offset)
    {
        $this->user = $user;
        if (0 > $limit || $limit > 50)
        {
            throw new InvalidArgumentException("Limit only supports 0 to 50 records. {$limit} given.");
        }
        $this->limit = $limit;
        $this->offset = $offset;
        
        $this->session = new \SpotifyWebAPI\Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret')
        );
        $this->session->setAccessToken($user->spotify_token);
        $this->session->setRefreshToken($user->spotify_refresh_token);
    }
    
    public function getUser(): User
    {
        return $this->user;
    }
    
    public function getLimit(): int
    {
        return $this->limit;
    }
    
    public function getOffset(): int
    {
        return $this->offset;
    }
    
    /**
     * Import and save saved songs
     * 
     * @return bool Whether there is more to import or not
     */
    public function import(): bool
    {
        $api = new SpotifyWebAPI(["auto_refresh" => true], $this->session);
        $next = null;
        try
        {
            $response = $api->getMySavedTracks([
                "limit" => $this->getLimit(),
                "offset" => $this->getOffset()
            ]);
            $songs = $response->items;
            $next = $response->next;
            
            $this->saveSongsList($songs);
        }
        catch (Exception $e)
        {
            throw $e;
        }
        finally
        {
            $this->user->update([
                "spotify_token" => $this->session->getAccessToken(),
                "spotify_refresh_token" =>$this->session->getRefreshToken()
            ]);
        }
        return (bool)$next;
    }
    
    protected function saveSongsList(array $songs)
    {
        foreach ($songs as $song)
        {
            $song = Song::updateOrCreate([
                "spotify_id" => $song->track->id
            ], ["name" => $song->track->name]);
            $this->user->songs()->attach($song->id, ["added_at" => $song->added_at]);
        }
    }
}

