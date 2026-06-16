<?php

namespace Database\Factories;

use App\Models\DailyReport;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyReport>
 */
class DailyReportFactory extends Factory
{
    protected $model = DailyReport::class;

    public function definition(): array
    {
        return [
            'tanggal' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'project_id' => Project::factory(),
            'mandor_pelapor' => fake()->name(),
            'cuaca' => fake()->randomElement(['Cerah', 'Mendung', 'Hujan Ringan']),
            'tenaga_kerja' => fake()->numberBetween(3, 25),
            'uraian_pekerjaan' => fake()->paragraph(2),
            'material' => fake()->sentence(),
            'kendala' => fake()->sentence(),
            'catatan' => fake()->optional()->sentence(),
            'photos' => [],
        ];
    }
}
