<?php

namespace App\Console\Commands;

use App\Jobs\Spotify\ImportRecentlyPlayedSongsDispatcher;
use Illuminate\Console\Command;

class GetRecentlyPlayedSongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:import:recently-played-songs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches the import to get all recently played songs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dispatchJob = new ImportRecentlyPlayedSongsDispatcher('low'); //low priority queue
        $dispatchJob->dispatch();
        return Command::SUCCESS;
    }
}
