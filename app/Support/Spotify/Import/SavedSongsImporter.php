<?php

namespace App\Support\Spotify\Import;

use App\Models\Song;
use App\Models\SongUser;
use App\Models\User;
use DateTime;
use Exception;
use InvalidArgumentException;
use SpotifyWebAPI\SpotifyWebAPI;

class SavedSongsImporter extends ImporterAbstract
{
    protected int $limit;
    protected int $offset;
    protected ?DateTime $stopAtAddedAtEarlierThan;
    
    public function __construct(User $user, int $limit, int $offset, ?DateTime $stopAtAddedAtEarlierThan = null)
    {
        if (0 > $limit || $limit > 50)
        {
            throw new InvalidArgumentException("Limit only supports 0 to 50 records. {$limit} given.");
        }
        $this->limit = $limit;
        $this->offset = $offset;
        $this->stopAtAddedAtEarlierThan = $stopAtAddedAtEarlierThan;
        
        parent::__construct($user);
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
    
    public function importAndRecord(SpotifyWebAPI $api): ?string
    {
        $response = $api->getMySavedTracks([
            "limit" => $this->getLimit(),
            "offset" => $this->getOffset()
        ]);
        $songs = $response->items;
        $next = $response->next;
        
        $this->saveSongsList($songs);
        
        if ($songs[0]->added_at < $this->stopAtAddedAtEarlierThan) {
            //Technically we could take all of them and find the max/min and compare that, but I'm pretty sure it's in the correct order and if we get a bonus page then whatever!
            return null; //Null represents 'dont carry on'
        }
        return $next;
    }
    
    protected function saveSongsList(array $songs)
    {
        foreach ($songs as $track)
        {
            $song = Song::updateOrCreate([
                "spotify_id" => $track->track->id
            ], ["name" => $track->track->name]);
            $songUser = SongUser::updateOrCreate([
                "song_id" => $song->id,
                "user_id" => $this->user->id
            ], ["added_at" => $track->added_at]);
        }
    }
}

