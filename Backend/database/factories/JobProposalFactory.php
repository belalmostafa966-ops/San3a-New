<?php

namespace Database\Factories;

use App\Models\CraftsmanProfile;
use App\Models\JobProposal;
use App\Models\JobRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobProposal>
 */
class JobProposalFactory extends Factory
{
    protected $model = JobProposal::class;

    public function definition(): array
    {
        return [
            'job_request_id' => JobRequest::factory(),
            'craftsman_id' => CraftsmanProfile::factory(),
            'price_quote' => fake()->randomFloat(2, 50, 500),
            'message' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
