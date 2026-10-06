<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'parent_task_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'assigned_to' => null,
            'created_by' => User::factory(),
            'status' => TaskStatus::Todo,
            'priority' => Priority::Medium,
            'start_date' => null,
            'due_date' => null,
            'completed_at' => null,
        ];
    }
}
