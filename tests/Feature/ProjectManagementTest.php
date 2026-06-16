<?php

namespace Tests\Feature;

use App\Livewire\Projects\ProjectForm;
use App\Livewire\Projects\ProjectShow;
use App\Models\DailyReport;
use App\Models\Project;
use App\Models\ProjectWorker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_users_are_redirected_from_project_pages(): void
    {
        $this->get('/projects')->assertRedirect('/login');
        $this->get('/projects/create')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_create_a_project(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('pic');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $pic = User::factory()->create();
        $pic->assignRole('pic');

        $this->actingAs($admin);

        Livewire::test(ProjectForm::class)
            ->set('kode_proyek', 'PRJ-001')
            ->set('nama_proyek', 'Pembangunan Gudang')
            ->set('lokasi', 'Jakarta')
            ->set('pic_user_ids', [(string) $admin->id, (string) $pic->id])
            ->set('klien', 'PT Maju Jaya')
            ->set('status_aktif', true)
            ->set('keterangan', 'Proyek prioritas')
            ->set('workers', [
                ['id' => null, 'name' => 'Budi', 'job_title' => 'Tukang'],
                ['id' => null, 'name' => 'Joko', 'job_title' => 'Helper'],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('projects.index', absolute: false));

        $this->assertDatabaseHas('projects', [
            'kode_proyek' => 'PRJ-001',
            'nama_proyek' => 'Pembangunan Gudang',
            'status_aktif' => true,
        ]);

        $this->assertDatabaseHas('project_user', [
            'user_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('project_user', [
            'user_id' => $pic->id,
        ]);

        $this->assertDatabaseHas('project_workers', [
            'name' => 'Budi',
            'job_title' => 'Tukang',
        ]);
    }

    public function test_authenticated_users_can_update_a_project(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('pic');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $pic = User::factory()->create();
        $pic->assignRole('pic');

        $this->actingAs($admin);

        $project = Project::factory()->create([
            'kode_proyek' => 'PRJ-OLD',
            'nama_proyek' => 'Nama Lama',
        ]);
        $project->pics()->attach($pic);

        Livewire::test(ProjectForm::class, ['project' => $project])
            ->set('nama_proyek', 'Nama Baru')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('projects.index', absolute: false));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'nama_proyek' => 'Nama Baru',
        ]);
    }

    public function test_project_detail_shows_reported_work_and_attendance_recap_by_date_range(): void
    {
        Role::findOrCreate('pic');

        $pic = User::factory()->create();
        $pic->assignRole('pic');
        $this->actingAs($pic);

        $project = Project::factory()->create(['nama_proyek' => 'Proyek Rekap']);
        $project->pics()->attach($pic);
        $worker = ProjectWorker::factory()->create([
            'project_id' => $project->id,
            'name' => 'Budi',
            'job_title' => 'Tukang',
        ]);

        $includedReport = DailyReport::factory()->create([
            'project_id' => $project->id,
            'tanggal' => '2026-06-10',
            'uraian_pekerjaan' => 'Pemasangan batu',
        ]);
        $includedReport->workerAttendances()->create([
            'project_worker_id' => $worker->id,
            'worker_name' => 'Budi',
            'job_title' => 'Tukang',
            'status' => 'setengah_hari',
        ]);

        $outsideReport = DailyReport::factory()->create([
            'project_id' => $project->id,
            'tanggal' => '2026-05-20',
            'uraian_pekerjaan' => 'Pekerjaan lama',
        ]);
        $outsideReport->workerAttendances()->create([
            'project_worker_id' => $worker->id,
            'worker_name' => 'Budi',
            'job_title' => 'Tukang',
            'status' => 'tidak_hadir',
        ]);

        Livewire::test(ProjectShow::class, ['project' => $project])
            ->set('start_date', '2026-06-01')
            ->set('end_date', '2026-06-30')
            ->assertSee('Pemasangan batu')
            ->assertDontSee('Pekerjaan lama')
            ->assertSee('Budi')
            ->assertSee('10/06')
            ->assertSee('Total HK')
            ->assertSee('0.5')
            ->assertSee('1/2');
    }

    public function test_project_attendance_recap_can_be_downloaded_as_pdf(): void
    {
        Role::findOrCreate('pic');

        $pic = User::factory()->create();
        $pic->assignRole('pic');
        $this->actingAs($pic);

        $project = Project::factory()->create(['kode_proyek' => 'PRJ-PDF']);
        $project->pics()->attach($pic);
        $worker = ProjectWorker::factory()->create([
            'project_id' => $project->id,
            'name' => 'Budi',
        ]);

        $report = DailyReport::factory()->create([
            'project_id' => $project->id,
            'tanggal' => '2026-06-10',
        ]);
        $report->workerAttendances()->create([
            'project_worker_id' => $worker->id,
            'worker_name' => 'Budi',
            'status' => 'hadir',
        ]);

        $response = $this->get(route('projects.attendance-pdf', [
            'project' => $project,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
        ]));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename=rekap-absensi-prj-pdf-2026-06-01-2026-06-30.pdf');
    }
}
