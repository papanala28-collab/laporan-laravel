<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectWorker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectWorker>
 */
class ProjectWorkerFactory extends Factory
{
    protected $model = ProjectWorker::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->name(),
            'job_title' => fake()->randomElement(['Tukang', 'Helper', 'Mandor', 'Operator']),
            'active' => true,
        ];
    }
}
