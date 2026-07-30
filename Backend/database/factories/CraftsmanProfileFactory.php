<?php

namespace Database\Factories;

use App\Models\CraftsmanProfile;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CraftsmanProfile>
 */
class CraftsmanProfileFactory extends Factory
{
    protected $model = CraftsmanProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'profession_id' => 1,
            'years_experience' => fake()->numberBetween(1, 15),
            'bio' => fake()->sentence(),
            'jobs_completed_count' => 0,
            'verification_tier' => 'basic',
            'rating_avg' => 0.00,
            'behavior_score' => 10,
        ];
    }
}
