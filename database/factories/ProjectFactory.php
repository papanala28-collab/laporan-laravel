<?php

namespace Database\Factories;

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
            'kode_proyek' => strtoupper(fake()->bothify('PRJ-###')),
            'nama_proyek' => 'Proyek '.fake()->city(),
            'lokasi' => fake()->city(),
            'pic' => fake()->name(),
            'klien' => fake()->company(),
            'status_aktif' => true,
            'keterangan' => fake()->sentence(),
        ];
    }
}
