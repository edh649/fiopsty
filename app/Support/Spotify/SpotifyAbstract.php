<?php

namespace App\Support\Spotify;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

abstract class SpotifyAbstract
{
    protected User $user;
    protected Session $session;
    
    public function __construct(User $user = null)
    {
        $this->user = $user ?? Auth::user();
        
        $this->session = new \SpotifyWebAPI\Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret')
        );
        $this->session->setAccessToken($this->user->spotify_token);
        $this->session->setRefreshToken($this->user->spotify_refresh_token);
    }
    
    protected function useApiWithUserTokenRefresh(Closure $func)
    {
        $api = new SpotifyWebAPI(["auto_refresh" => true], $this->session);
        try
        {
            $response = $func($api);
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
        return $response;
    }
    
}
