<?php

namespace App\Console\Commands;

use App\Record\Status;
use Illuminate\Console\Command;
use Revolution\Bluesky\Core\TID;
use Revolution\Bluesky\Facades\Bluesky;

class CreateStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bsky:create-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $emoji = collect(config('statusphere.status'))->random();

        $status = Status::create(status: $emoji);

        dump($status->toRecord());

        $res = Bluesky::login(config('bluesky.identifier'), config('bluesky.password'))
            ->putRecord(
                repo: Bluesky::assertDid(),
                collection: Status::NSID,
                rkey: TID::nextStr(),
                record: $status,
                validate: false,
            );

        dump($res->json());

        return 0;
    }
}
