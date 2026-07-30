<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'job_id' => 1,
            'rated_by' => User::factory(),
            'rated_user_id' => User::factory(),
            'direction' => fake()->randomElement(['client_to_craftsman', 'craftsman_to_client']),
            'score' => fake()->numberBetween(1, 5),
            'behavior_score' => fake()->numberBetween(1, 10),
            'comment' => fake()->sentence(),
        ];
    }
}
