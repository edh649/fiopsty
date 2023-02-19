<?php

namespace App\Jobs\Spotify;

use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ImportRecentlyPlayedSongsDispatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected ?string $dispatchOnQueue;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $dispatchOnQueue = null)
    {
        $this->dispatchOnQueue = $dispatchOnQueue;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = [];
        $users = User::whereNotNull('listen_all_started_at')->get();
        foreach ($users as $user) {
            $jobs[] = new ImportRecentlyPlayedSongs($user);
        }
        Bus::batch($jobs)->onQueue($this->dispatchOnQueue ?? 'default')->dispatch();
    }
}
