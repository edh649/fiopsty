<?php

namespace App\Console\Commands;

use App\Jobs\Spotify\ImportRecentlySavedSongsDispatcher;
use Illuminate\Console\Command;

class GetRecentlySavedSongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:import:recently-saved-songs';

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
        $dispatchJob = new ImportRecentlySavedSongsDispatcher('low'); //low priority queue
        $dispatchJob->dispatch();
        return Command::SUCCESS;
    }
}
