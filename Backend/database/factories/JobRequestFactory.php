<?php

namespace Database\Factories;

use App\Models\JobRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobRequest>
 */
class JobRequestFactory extends Factory
{
    protected $model = JobRequest::class;

    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'profession_id' => 1,
            'description' => fake()->paragraph(),
            'zone_id' => 1,
            'address' => fake()->address(),
            'preferred_time' => now()->addDays(1),
            'status' => 'open',
            'visit_fee_status' => 'unpaid',
        ];
    }
}
