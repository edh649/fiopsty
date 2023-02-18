<?php

namespace App\Console\Commands;

use App\Jobs\Spotify\ImportRecentSavedSongsDispatcher;
use Illuminate\Console\Command;

class GetRecentSavedSongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:import:recent-saved-songs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports saved songs for all users up to the time of the last import';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dispatchJob = new ImportRecentSavedSongsDispatcher('low'); //low priority queue
        $dispatchJob->dispatch();
        return Command::SUCCESS;
    }
}
