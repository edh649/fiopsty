<?php

namespace App\Support\Spotify\Import;

use App\Support\Spotify\SpotifyAbstract;
use Exception;
use SpotifyWebAPI\SpotifyWebAPI;

abstract class ImporterAbstract extends SpotifyAbstract
{
    /**
     * Import and save saved songs
     * 
     * @return bool Whether there is more to import or not
     */
    public function import(): bool
    {
        $next = $this->useApiWithUserTokenRefresh(function (SpotifyWebApi $api) {
            $this->importAndRecord($api);
        });
        return (bool)$next;
    }
    
    protected abstract function importAndRecord(SpotifyWebAPI $api): string;
}
