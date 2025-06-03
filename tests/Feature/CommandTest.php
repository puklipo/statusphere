<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Response;
use Mockery;
use Revolution\Bluesky\Facades\Bluesky;
use Tests\TestCase;

class CommandTest extends TestCase
{
    public function test_create_status_command_executes_successfully(): void
    {
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->expects('json')
            ->andReturn(['success' => true]);

        Bluesky::expects('login->putRecord')
            ->with(Mockery::any(), Mockery::any())
            ->withArgs(function ($repo, $collection, $rkey, $record, $validate) {
                return is_string($repo) &&
                       is_string($collection) &&
                       is_string($rkey) &&
                       is_object($record) &&
                       is_bool($validate);
            })
            ->andReturn($mockResponse);

        Bluesky::expects('assertDid')
            ->andReturn('did:plc:test123');

        $this->artisan('bsky:create-status')->assertExitCode(0);
    }
}
