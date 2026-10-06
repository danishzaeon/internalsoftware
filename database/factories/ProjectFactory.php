<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'service_id' => null,
            'name' => fake()->catchPhrase(),
            'description' => fake()->sentence(),
            'status' => ProjectStatus::Active,
            'priority' => Priority::Medium,
            'start_date' => null,
            'due_date' => null,
            'manager_id' => null,
        ];
    }
}
