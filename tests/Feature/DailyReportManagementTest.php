<?php

namespace Tests\Feature;

use App\Livewire\Reports\ReportForm;
use App\Livewire\Reports\ReportIndex;
use App\Models\DailyReport;
use App\Models\Project;
use App\Models\ProjectWorker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DailyReportManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_users_are_redirected_from_report_pages(): void
    {
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/reports/create')->assertRedirect('/login');
    }

    public function test_active_projects_are_available_when_creating_reports(): void
    {
        Role::findOrCreate('pic');

        $user = User::factory()->create();
        $user->assignRole('pic');
        $this->actingAs($user);

        $activeProject = Project::factory()->create(['nama_proyek' => 'Proyek Aktif', 'status_aktif' => true]);
        $activeProject->pics()->attach($user);
        $inactiveProject = Project::factory()->create(['nama_proyek' => 'Proyek Nonaktif', 'status_aktif' => false]);
        $inactiveProject->pics()->attach($user);

        Livewire::test(ReportForm::class)
            ->assertSee($activeProject->nama_proyek)
            ->assertDontSee($inactiveProject->nama_proyek);
    }

    public function test_authenticated_users_can_create_a_daily_report(): void
    {
        Storage::fake('public');
        Role::findOrCreate('pic');

        $user = User::factory()->create(['name' => 'Slamet']);
        $user->assignRole('pic');
        $this->actingAs($user);

        $project = Project::factory()->create();
        $project->pics()->attach($user);
        $workerA = ProjectWorker::factory()->create(['project_id' => $project->id, 'name' => 'Budi', 'job_title' => 'Tukang']);
        $workerB = ProjectWorker::factory()->create(['project_id' => $project->id, 'name' => 'Joko', 'job_title' => 'Helper']);

        Livewire::test(ReportForm::class)
            ->set('tanggal', '2026-06-16')
            ->set('project_id', (string) $project->id)
            ->set('cuaca', 'Cerah')
            ->set("worker_attendance.{$workerA->id}", 'hadir')
            ->set("worker_attendance.{$workerB->id}", 'setengah_hari')
            ->set('uraian_pekerjaan_lines', ['Pengecoran area timur', 'Perapihan bekisting'])
            ->set('material_rows', [
                ['name' => 'Semen', 'qty' => '10 sak'],
                ['name' => 'Pasir', 'qty' => '2 rit'],
            ])
            ->set('kendala_lines', ['Molen', 'Sekop'])
            ->set('photos', [UploadedFile::fake()->image('laporan.jpg')])
            ->set('catatan', 'Perlu tambahan 2 pekerja')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('daily_reports', [
            'project_id' => $project->id,
            'mandor_pelapor' => 'Slamet',
            'cuaca' => 'Cerah',
            'material' => "Semen - 10 sak\nPasir - 2 rit",
            'kendala' => "Molen\nSekop",
        ]);

        $this->assertDatabaseHas('daily_report_worker_attendances', [
            'worker_name' => 'Budi',
            'status' => 'hadir',
        ]);

        $this->assertDatabaseHas('daily_report_worker_attendances', [
            'worker_name' => 'Joko',
            'status' => 'setengah_hari',
        ]);
    }

    public function test_authenticated_users_can_upload_multiple_report_photos(): void
    {
        Storage::fake('public');
        Role::findOrCreate('pic');

        $user = User::factory()->create(['name' => 'Slamet']);
        $user->assignRole('pic');
        $this->actingAs($user);

        $project = Project::factory()->create();
        $project->pics()->attach($user);
        $worker = ProjectWorker::factory()->create(['project_id' => $project->id]);

        Livewire::test(ReportForm::class)
            ->set('tanggal', '2026-06-16')
            ->set('project_id', (string) $project->id)
            ->set('cuaca', 'Cerah')
            ->set("worker_attendance.{$worker->id}", 'hadir')
            ->set('uraian_pekerjaan_lines', ['Pengecoran area timur'])
            ->set('material_rows', [['name' => 'Semen', 'qty' => '1 sak']])
            ->set('kendala_lines', ['Molen'])
            ->set('photos', [
                UploadedFile::fake()->image('laporan-1.jpg'),
                UploadedFile::fake()->image('laporan-2.jpg'),
            ])
            ->call('save')
            ->assertHasNoErrors();

        $report = DailyReport::query()->latest('id')->firstOrFail();

        $this->assertCount(2, $report->photoUrls());
    }

    public function test_daily_report_can_be_downloaded_as_pdf(): void
    {
        Role::findOrCreate('pic');

        $user = User::factory()->create(['name' => 'Slamet']);
        $user->assignRole('pic');
        $this->actingAs($user);

        $project = Project::factory()->create();
        $project->pics()->attach($user);
        $worker = ProjectWorker::factory()->create(['project_id' => $project->id, 'name' => 'Budi']);

        $report = DailyReport::factory()->create([
            'project_id' => $project->id,
            'tanggal' => '2026-06-16',
            'mandor_pelapor' => 'Slamet',
            'material' => 'Semen - 1 sak',
            'kendala' => 'Molen',
        ]);
        $report->workerAttendances()->create([
            'project_worker_id' => $worker->id,
            'worker_name' => 'Budi',
            'status' => 'hadir',
        ]);

        $this->get(route('reports.pdf', $report))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename=laporan-harian-'.str($project->kode_proyek)->slug().'-2026-06-16.pdf');
    }

    public function test_multiple_photos_can_be_staged_before_save(): void
    {
        Role::findOrCreate('pic');

        $user = User::factory()->create(['name' => 'Slamet']);
        $user->assignRole('pic');
        $this->actingAs($user);

        Livewire::test(ReportForm::class)
            ->set('galleryPhotos', [
                UploadedFile::fake()->image('camera.jpg'),
                UploadedFile::fake()->image('gallery-1.jpg'),
                UploadedFile::fake()->image('gallery-2.jpg'),
            ])
            ->assertCount('photos', 3);
    }

    public function test_report_form_validates_required_line_items(): void
    {
        Role::findOrCreate('pic');

        $user = User::factory()->create();
        $user->assignRole('pic');
        $this->actingAs($user);

        $project = Project::factory()->create();
        $project->pics()->attach($user);
        $worker = ProjectWorker::factory()->create(['project_id' => $project->id]);

        Livewire::test(ReportForm::class)
            ->set('tanggal', '2026-06-16')
            ->set('project_id', (string) $project->id)
            ->set('cuaca', 'Cerah')
            ->set("worker_attendance.{$worker->id}", 'hadir')
            ->set('uraian_pekerjaan_lines', [''])
            ->set('material_rows', [['name' => 'Semen', 'qty' => '1 sak']])
            ->set('kendala_lines', ['Molen'])
            ->call('save')
            ->assertHasErrors(['uraian_pekerjaan_lines.0' => 'required']);
    }

    public function test_reports_can_be_filtered_by_project(): void
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $projectA = Project::factory()->create(['nama_proyek' => 'Proyek Alpha']);
        $projectB = Project::factory()->create(['nama_proyek' => 'Proyek Beta']);

        DailyReport::factory()->create([
            'project_id' => $projectA->id,
            'mandor_pelapor' => 'Mandor Alpha',
        ]);

        DailyReport::factory()->create([
            'project_id' => $projectB->id,
            'mandor_pelapor' => 'Mandor Beta',
        ]);

        Livewire::test(ReportIndex::class)
            ->set('project', (string) $projectA->id)
            ->assertSee('Mandor Alpha')
            ->assertDontSee('Mandor Beta');
    }

    public function test_pic_users_cannot_create_reports_for_unassigned_projects(): void
    {
        Role::findOrCreate('pic');

        $user = User::factory()->create();
        $user->assignRole('pic');
        $this->actingAs($user);

        $project = Project::factory()->create();
        $worker = ProjectWorker::factory()->create(['project_id' => $project->id]);

        Livewire::test(ReportForm::class)
            ->set('tanggal', '2026-06-16')
            ->set('project_id', (string) $project->id)
            ->set('cuaca', 'Cerah')
            ->set("worker_attendance.{$worker->id}", 'hadir')
            ->set('uraian_pekerjaan_lines', ['Pengecoran area timur'])
            ->set('material_rows', [['name' => 'Semen', 'qty' => '1 sak']])
            ->set('kendala_lines', ['Molen'])
            ->call('save')
            ->assertHasErrors(['project_id']);
    }
}
