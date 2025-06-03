<?php

namespace App\Listeners;

use App\Record\Status;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Revolution\Bluesky\Events\Jetstream\JetstreamCommitMessage;

class StatusListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JetstreamCommitMessage $event): void
    {
        if ($event->operation !== 'create') {
            return;
        }

        $collection = data_get($event->message, 'commit.collection');
        if ($collection !== Status::NSID) {
            return;
        }

        $did = data_get($event->message, 'did');
        $status = data_get($event->message, 'commit.record.status');
        $createdAt = data_get($event->message, 'commit.record.createdAt');

    }
}
