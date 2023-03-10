<?php

namespace App\Jobs\Spotify;

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

class ImportSavedSongsDispatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected ?string $dispatchOnQueue;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, ?string $dispatchOnQueue = null)
    {
        $this->user = $user;
        $this->dispatchOnQueue = $dispatchOnQueue;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Bus::batch(new ImportSavedSongs($this->user, 50, 0, true))
            ->onQueue($this->dispatchOnQueue ?? 'default')
            ->dispatch();
    }
}
