<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Activity;

class SyncGoogleCalendarEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $activity;
    protected $isUpdate;

    /**
     * Create a new job instance.
     */
    public function __construct(Activity $activity, $isUpdate = false)
    {
        $this->activity = $activity;
        $this->isUpdate = $isUpdate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->isUpdate) {
            \App\Services\GoogleCalendarService::updateEvent($this->activity);
        } else {
            \App\Services\GoogleCalendarService::createEvent($this->activity);
        }
    }
}
