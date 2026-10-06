<?php

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company_name' => fake()->company(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('9#########'),
            'source' => fake()->randomElement(LeadSource::cases()),
            'service_id' => null,
            'requirement' => fake()->sentence(),
            'status' => LeadStatus::New,
            'assigned_to' => null,
            'expected_value' => fake()->randomFloat(2, 5000, 100000),
            'next_followup_at' => null,
            'notes' => null,
        ];
    }
}
