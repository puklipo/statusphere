<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Response;
use Mockery;
use Revolution\Bluesky\Contracts\Factory;
use Revolution\Bluesky\Facades\Bluesky;
use Tests\TestCase;

class CommandTest extends TestCase
{
    public function test_create_status_command_executes_successfully(): void
    {
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('json')
            ->andReturn(['success' => true]);

        $mockFactory = Mockery::mock(Factory::class);
        $mockFactory->shouldReceive('putRecord')
            ->withArgs(function ($repo, $collection, $rkey, $record, $validate) {
                return is_string($repo) &&
                       is_string($collection) &&
                       is_string($rkey) &&
                       is_object($record) &&
                       is_bool($validate);
            })
            ->andReturn($mockResponse);

        Bluesky::shouldReceive('login')
            ->with(Mockery::any(), Mockery::any())
            ->andReturn($mockFactory);

        Bluesky::shouldReceive('assertDid')
            ->andReturn('did:plc:test123');

        $this->artisan('bsky:create-status')->assertExitCode(0);
    }
}
