<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use App\Models\DailyReportWorkerAttendance;
use App\Models\Project;
use App\Models\ProjectWorker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ReportForm extends Component
{
    use WithFileUploads;

    protected array $lineFields = [
        'uraian_pekerjaan_lines',
        'kendala_lines',
    ];

    public ?DailyReport $report = null;

    public string $tanggal = '';

    public ?string $start_time = null;

    public ?string $end_time = null;

    public string $project_id = '';

    public string $cuaca = '';

    public string $catatan = '';

    public array $uraian_pekerjaan_lines = [''];

    public array $material_rows = [
        ['name' => '', 'qty' => ''],
    ];

    public array $kendala_lines = [''];

    public array $worker_attendance = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $photos = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $galleryPhotos = [];

    /** @var array<int, string> */
    public array $existingPhotos = [];

    public function mount(?DailyReport $report = null): void
    {
        if (! $report?->exists) {
            $this->tanggal = now()->toDateString();

            return;
        }

        abort_unless($this->canAccessProject($report->project), 403);

        $this->report = $report->load('project.pics', 'workerAttendances');
        $this->tanggal = $report->tanggal->format('Y-m-d');
        $this->start_time = $report->start_time ? \Carbon\Carbon::parse($report->start_time)->format('H:i') : null;
        $this->end_time = $report->end_time ? \Carbon\Carbon::parse($report->end_time)->format('H:i') : null;
        $this->project_id = (string) $report->project_id;
        $this->cuaca = $report->cuaca;
        $this->catatan = $report->catatan ?? '';
        $this->uraian_pekerjaan_lines = $this->explodeLines($report->uraian_pekerjaan);
        $this->material_rows = $this->parseMaterialRows($report->material);
        $this->kendala_lines = $this->explodeLines($report->kendala);
        $this->existingPhotos = $report->photoUrls();
        $this->loadWorkerAttendanceForSelectedProject();
    }

    public function addLine(string $field): void
    {
        abort_unless(in_array($field, $this->lineFields, true), 404);

        $this->{$field}[] = '';
    }

    public function removeLine(string $field, int $index): void
    {
        abort_unless(in_array($field, $this->lineFields, true), 404);

        if (count($this->{$field}) <= 1) {
            $this->{$field}[0] = '';

            return;
        }

        unset($this->{$field}[$index]);
        $this->{$field} = array_values($this->{$field});
    }

    public function addMaterialRow(): void
    {
        $this->material_rows[] = ['name' => '', 'qty' => ''];
    }

    public function removeMaterialRow(int $index): void
    {
        if (count($this->material_rows) <= 1) {
            $this->material_rows[0] = ['name' => '', 'qty' => ''];

            return;
        }

        unset($this->material_rows[$index]);
        $this->material_rows = array_values($this->material_rows);
    }

    public function removeNewPhoto(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function removeExistingPhoto(int $index): void
    {
        unset($this->existingPhotos[$index]);
        $this->existingPhotos = array_values($this->existingPhotos);
    }

    public function updatedGalleryPhotos(): void
    {
        foreach ($this->galleryPhotos as $photo) {
            if ($photo instanceof TemporaryUploadedFile) {
                $this->photos[] = $photo;
            }
        }

        $this->reset('galleryPhotos');
    }

    public function updatedProjectId(): void
    {
        $this->loadWorkerAttendanceForSelectedProject();
    }

    public function save(): void
    {
        foreach ($this->lineFields as $field) {
            $this->{$field} = $this->normalizeLines($this->{$field});
        }

        $this->material_rows = $this->normalizeMaterialRows($this->material_rows);

        $this->photos = collect($this->photos)
            ->filter(fn ($photo) => $photo instanceof TemporaryUploadedFile)
            ->unique(fn (TemporaryUploadedFile $photo) => $photo->getFilename())
            ->values()
            ->all();

        if (filled($this->project_id) && $this->worker_attendance === []) {
            $this->loadWorkerAttendanceForSelectedProject();
        }

        $validated = $this->validate([
            'tanggal' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'project_id' => ['required', 'exists:projects,id'],
            'cuaca' => ['required', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
            'uraian_pekerjaan_lines' => ['required', 'array', 'min:1'],
            'uraian_pekerjaan_lines.*' => ['required', 'string'],
            'worker_attendance' => ['required', 'array', 'min:1'],
            'worker_attendance.*' => ['required', Rule::in(array_keys(DailyReportWorkerAttendance::STATUSES))],
            'material_rows' => ['nullable', 'array'],
            'material_rows.*.name' => ['nullable', 'string', 'max:255'],
            'material_rows.*.qty' => ['nullable', 'string', 'max:255'],
            'kendala_lines' => ['nullable', 'array'],
            'kendala_lines.*' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'max:10240'],
            'galleryPhotos' => ['nullable', 'array'],
            'galleryPhotos.*' => ['image', 'max:10240'],
        ]);

        $project = Project::query()->findOrFail($validated['project_id']);

        if (! $project->status_aktif && $project->id !== $this->report?->project_id) {
            $this->addError('project_id', 'Hanya proyek aktif yang dapat dipilih.');

            return;
        }

        if (! $this->canAccessProject($project)) {
            $this->addError('project_id', 'Kamu bukan PIC untuk proyek ini.');

            return;
        }

        $workers = $this->workersForReport($project, array_keys($validated['worker_attendance']));

        if ($workers->isEmpty()) {
            $this->addError('worker_attendance', 'Tambahkan tenaga kerja di master proyek sebelum membuat laporan.');

            return;
        }

        if ($workers->count() !== count($validated['worker_attendance'])) {
            $this->addError('worker_attendance', 'Data tenaga kerja tidak sesuai dengan proyek yang dipilih.');

            return;
        }

        $attendanceSummary = $workers
            ->map(fn (ProjectWorker $worker) => $worker->name.' - '.DailyReportWorkerAttendance::STATUSES[$validated['worker_attendance'][$worker->id]])
            ->implode(PHP_EOL);

        $oldPhotos = $this->report?->photoUrls() ?? [];

        $storedPhotos = collect($this->photos)
            ->map(fn (TemporaryUploadedFile $photo) => $photo->store('report-photos', 'public'))
            ->all();

        $report = $this->report ?? new DailyReport;
        $report->fill([
            'tanggal' => $validated['tanggal'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'project_id' => $validated['project_id'],
            'mandor_pelapor' => (string) Auth::user()->name,
            'cuaca' => $validated['cuaca'],
            'tenaga_kerja' => $attendanceSummary,
            'uraian_pekerjaan' => $this->implodeLines($validated['uraian_pekerjaan_lines']),
            'material' => $this->implodeMaterialRows($validated['material_rows']),
            'kendala' => $this->implodeLines($validated['kendala_lines']),
            'catatan' => $validated['catatan'] ?? null,
            'photos' => [...$this->existingPhotos, ...$storedPhotos],
        ]);
        $report->save();
        $this->syncWorkerAttendances($report, $workers, $validated['worker_attendance']);

        $removedPhotos = collect($oldPhotos)
            ->diff($report->photoUrls())
            ->all();

        foreach ($removedPhotos as $removedPhoto) {
            Storage::disk('public')->delete($removedPhoto);
        }

        $this->reset('galleryPhotos', 'photos');

        session()->flash('status', $this->report ? 'Laporan berhasil diperbarui.' : 'Laporan berhasil disimpan.');

        $this->redirectRoute('reports.show', $report, navigate: true);
    }

    public function render(): View
    {
        $projects = Project::query()
            ->with('pics')
            ->when($this->report?->project_id, function ($query) {
                $query->where(function ($projectQuery) {
                    $projectQuery->where('status_aktif', true)
                        ->orWhere('id', $this->report->project_id);
                });
            }, fn ($query) => $query->where('status_aktif', true))
            ->when(! auth()->user()?->hasRole('admin'), fn ($query) => $query->whereHas('pics', fn ($pics) => $pics->whereKey(auth()->id())))
            ->orderBy('nama_proyek')
            ->get();

        $attendanceWorkers = blank($this->project_id)
            ? collect()
            : ProjectWorker::query()
                ->where('project_id', $this->project_id)
                ->where('active', true)
                ->orderBy('name')
                ->get();

        return view('livewire.reports.report-form', [
            'projects' => $projects,
            'reporterName' => (string) Auth::user()->name,
            'attendanceWorkers' => $attendanceWorkers,
            'attendanceStatuses' => DailyReportWorkerAttendance::STATUSES,
        ])->layout('components.layouts.app');
    }

    protected function explodeLines(?string $value): array
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();

        return $lines === [] ? [''] : $lines;
    }

    protected function normalizeLines(array $lines): array
    {
        $normalized = collect($lines)
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();

        return $normalized === [] ? [''] : $normalized;
    }

    protected function implodeLines(array $lines): string
    {
        return implode(PHP_EOL, $this->normalizeLines($lines));
    }

    protected function parseMaterialRows(?string $value): array
    {
        $rows = collect(preg_split('/\r\n|\r|\n/', (string) $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->map(function (string $line) {
                if (str_contains($line, ' - ')) {
                    [$name, $qty] = explode(' - ', $line, 2);

                    return ['name' => trim($name), 'qty' => trim($qty)];
                }

                return ['name' => $line, 'qty' => ''];
            })
            ->values()
            ->all();

        return $rows === [] ? [['name' => '', 'qty' => '']] : $rows;
    }

    protected function normalizeMaterialRows(array $rows): array
    {
        $normalized = collect($rows)
            ->map(fn (array $row) => [
                'name' => trim((string) ($row['name'] ?? '')),
                'qty' => trim((string) ($row['qty'] ?? '')),
            ])
            ->filter(fn (array $row) => $row['name'] !== '' || $row['qty'] !== '')
            ->values()
            ->all();

        return $normalized === [] ? [['name' => '', 'qty' => '']] : $normalized;
    }

    protected function implodeMaterialRows(array $rows): string
    {
        return collect($this->normalizeMaterialRows($rows))
            ->filter(fn (array $row) => $row['name'] !== '' || $row['qty'] !== '')
            ->map(fn (array $row) => $row['name'] . ($row['qty'] !== '' ? ' - ' . $row['qty'] : ''))
            ->filter(fn (string $line) => $line !== '' && $line !== ' - ')
            ->implode(PHP_EOL);
    }

    protected function canAccessProject(Project $project): bool
    {
        $user = auth()->user();

        return (bool) ($user?->hasRole('admin') || $project->pics()->whereKey($user?->id)->exists());
    }

    protected function loadWorkerAttendanceForSelectedProject(): void
    {
        if (blank($this->project_id)) {
            $this->worker_attendance = [];

            return;
        }

        $existingAttendances = collect($this->report?->workerAttendances ?? [])
            ->whereNotNull('project_worker_id')
            ->keyBy('project_worker_id');

        $this->worker_attendance = ProjectWorker::query()
            ->where('project_id', $this->project_id)
            ->where('active', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (ProjectWorker $worker) => [
                (string) $worker->id => $existingAttendances->get($worker->id)?->status ?? 'hadir',
            ])
            ->all();
    }

    protected function workersForReport(Project $project, array $workerIds)
    {
        return ProjectWorker::query()
            ->where('project_id', $project->id)
            ->where('active', true)
            ->whereIn('id', $workerIds)
            ->orderBy('name')
            ->get();
    }

    protected function syncWorkerAttendances(DailyReport $report, $workers, array $attendance): void
    {
        $report->workerAttendances()->delete();

        foreach ($workers as $worker) {
            $report->workerAttendances()->create([
                'project_worker_id' => $worker->id,
                'worker_name' => $worker->name,
                'job_title' => $worker->job_title,
                'status' => $attendance[$worker->id],
            ]);
        }
    }
}
