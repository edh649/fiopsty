<?php

namespace App\Jobs\Spotify;

use App\Models\User;
use App\Support\Spotify\Import\SavedSongsImporter;
use DateTime;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class ImportSavedSongs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected SavedSongsImporter $importer;
    protected bool $continueImportInBatch;
    protected ?DateTime $stopAtAddedAtEarlierThan;
    
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
    public function __construct(User $user, int $limit, int $offset, bool $continueImportInBatch = false, ?DateTime $stopAtAddedAtEarlierThan = null)
    {
        $this->importer = new SavedSongsImporter($user, $limit, $offset, $stopAtAddedAtEarlierThan);
        $this->continueImportInBatch = $continueImportInBatch;
        $this->stopAtAddedAtEarlierThan = $stopAtAddedAtEarlierThan;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $more = $this->importer->import();
        if ($more && $this->continueImportInBatch)
        {
            if (!$this->batch()) {
                throw new Exception("Requested continuing import however job wasn't provided in a batch!");
            }
            $this->batch()->add(new ImportSavedSongs(
                user: $this->importer->getUser(), 
                limit: $this->importer->getLimit(), 
                offset: $this->importer->getOffset() + $this->importer->getLimit(), 
                continueImportInBatch: $this->continueImportInBatch,
                stopAtAddedAtEarlierThan: $this->stopAtAddedAtEarlierThan
            ));
        }
    }
}
