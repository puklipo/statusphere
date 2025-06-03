<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Response;
use Mockery;
use Revolution\Bluesky\Facades\Bluesky;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed();

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('collect')
            ->with('records')
            ->andReturn(collect([
                [
                    'value' => [
                        'status' => '😄',
                        'createdAt' => '2025-06-03T01:47:14+00:00',
                    ],
                ],
            ]));

        Bluesky::shouldReceive('listRecords')
            ->andReturn($mockResponse);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
