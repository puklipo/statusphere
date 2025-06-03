<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

        Bluesky::expects('listRecords->collect')
            ->with('records')
            ->andReturn(collect([
                [
                    'value' => [
                        'status' => '😄',
                        'createdAt' => '2025-06-03T01:47:14+00:00',
                    ],
                ],
            ]));

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
