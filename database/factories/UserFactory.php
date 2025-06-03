<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'did' => 'did:plc:'.fake()->regexify('[a-z0-9]{24}'),
            'handle' => fake()->userName().'.bsky.social',
            'issuer' => 'https://bsky.social',
            'avatar' => fake()->imageUrl(200, 200, 'people'),
            'refresh_token' => fake()->sha256(),
            'remember_token' => Str::random(10),
        ];
    }
}
