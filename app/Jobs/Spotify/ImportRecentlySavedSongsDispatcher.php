<?php

namespace App\Jobs\Spotify;

use App\Models\SongUser;
use App\Models\User;
use DateTime;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ImportRecentlySavedSongsDispatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?string $dispatchOnQueue;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(?string $dispatchOnQueue = null)
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
        foreach (User::whereNotNull('spotify_token') as $user)
        {
            $latestAddedAt = SongUser::where('user_id', $user->id)->max('added_at');
            $jobs = new ImportSavedSongs($user, 50, 0, true, $latestAddedAt);
        }
        Bus::batch($jobs)
            ->onQueue($this->dispatchOnQueue ?? 'default')
            ->dispatch();
    }
}
