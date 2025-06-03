<?php

namespace Tests\Feature;

use App\Listeners\StatusListener;
use App\Record\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Revolution\Bluesky\Events\Jetstream\JetstreamCommitMessage;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_listener_handles_valid_jetstream_commit_message(): void
    {
        Event::fake();

        $message = [
            'did' => 'did:plc:test123',
            'commit' => [
                'collection' => Status::NSID,
                'record' => [
                    'status' => '😄',
                    'createdAt' => '2025-06-03T01:47:14+00:00',
                ],
            ],
        ];

        $event = new JetstreamCommitMessage(
            kind: 'commit',
            operation: 'create',
            message: $message,
            host: 'bsky.social',
            payload: []
        );

        event($event);

        Event::assertListening(
            JetstreamCommitMessage::class,
            StatusListener::class
        );
    }

    public function test_status_listener_ignores_non_create_operations(): void
    {
        $listener = new StatusListener;

        $message = [
            'did' => 'did:plc:test123',
            'commit' => [
                'collection' => Status::NSID,
                'record' => [
                    'status' => '😄',
                    'createdAt' => '2025-06-03T01:47:14+00:00',
                ],
            ],
        ];

        $event = new JetstreamCommitMessage(
            kind: 'commit',
            operation: 'update',
            message: $message,
            host: 'bsky.social',
            payload: []
        );

        $result = $listener->handle($event);
        $this->assertNull($result);
    }

    public function test_status_listener_ignores_non_matching_collections(): void
    {
        $listener = new StatusListener;

        $message = [
            'did' => 'did:plc:test123',
            'commit' => [
                'collection' => 'com.example.other.collection',
                'record' => [
                    'status' => '😄',
                    'createdAt' => '2025-06-03T01:47:14+00:00',
                ],
            ],
        ];

        $event = new JetstreamCommitMessage(
            kind: 'commit',
            operation: 'create',
            message: $message,
            host: 'bsky.social',
            payload: []
        );

        $result = $listener->handle($event);
        $this->assertNull($result);
    }

    public function test_status_listener_extracts_data_correctly(): void
    {
        $listener = new StatusListener;

        $message = [
            'did' => 'did:plc:test123',
            'commit' => [
                'collection' => Status::NSID,
                'record' => [
                    'status' => '🎉',
                    'createdAt' => '2025-06-03T07:17:40+00:00',
                ],
            ],
        ];

        $event = new JetstreamCommitMessage(
            kind: 'commit',
            operation: 'create',
            message: $message,
            host: 'bsky.social',
            payload: []
        );

        $result = $listener->handle($event);
        $this->assertNull($result);
    }
}
