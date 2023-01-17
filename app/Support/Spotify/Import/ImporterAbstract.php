<?php

namespace App\Support\Spotify\Import;

use App\Models\User;
use Exception;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

abstract class ImporterAbstract
{
    protected User $user;
    protected Session $session;
    
    public function __construct(User $user)
    {
        $this->user = $user;
        
        $this->session = new \SpotifyWebAPI\Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret')
        );
        $this->session->setAccessToken($user->spotify_token);
        $this->session->setRefreshToken($user->spotify_refresh_token);
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
            $next = $this->importAndRecord($api);
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
    
    protected abstract function importAndRecord(SpotifyWebAPI $api): string;
}
