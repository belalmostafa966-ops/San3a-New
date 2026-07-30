<?php

namespace Database\Factories;

use App\Models\CraftsmanProfile;
use App\Models\Job;
use App\Models\JobRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'job_request_id' => JobRequest::factory(),
            'craftsman_id' => CraftsmanProfile::factory(),
            'client_id' => User::factory(),
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
        ];
    }
}
