<?php

namespace App\Jobs\Spotify;

use App\Models\User;
use App\Support\Spotify\Import\RecentlyPlayedSongsImporter;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class ImportRecentlyPlayedSongs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected RecentlyPlayedSongsImporter $importer;
    
    /**
     * Create a new job instance.
     * 
     * @param User $user
     * @param int $limit How many records to get (0-50)
     * @param int $offset How many records to offset
     * @param bool $continueImportInBatch Whether to dispatch a job to continue importing if available
     *
     * @return void
     */
    public function __construct(User $user, int $limit = null)
    {
        $this->importer = new RecentlyPlayedSongsImporter($user, $limit ?? 50);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->importer->import();
    }
}
