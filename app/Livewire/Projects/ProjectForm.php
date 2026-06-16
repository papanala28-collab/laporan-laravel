<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\ProjectWorker;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProjectForm extends Component
{
    public ?Project $project = null;

    public string $kode_proyek = '';

    public string $nama_proyek = '';

    public string $lokasi = '';

    public array $pic_user_ids = [];

    public string $klien = '';

    public bool $status_aktif = true;

    public string $keterangan = '';

    public array $workers = [
        ['id' => null, 'name' => '', 'job_title' => ''],
    ];

    public function mount(?Project $project = null): void
    {
        if (! $project?->exists) {
            return;
        }

        $this->project = $project->load('workers');
        $this->kode_proyek = $project->kode_proyek;
        $this->nama_proyek = $project->nama_proyek;
        $this->lokasi = $project->lokasi;
        $this->pic_user_ids = $project->pics()->pluck('users.id')->map(fn ($id) => (string) $id)->all();
        $this->klien = $project->klien;
        $this->status_aktif = $project->status_aktif;
        $this->keterangan = $project->keterangan ?? '';
        $this->workers = $project->workers
            ->map(fn (ProjectWorker $worker) => [
                'id' => $worker->id,
                'name' => $worker->name,
                'job_title' => $worker->job_title ?? '',
            ])
            ->values()
            ->all() ?: $this->workers;
    }

    public function addWorker(): void
    {
        $this->workers[] = ['id' => null, 'name' => '', 'job_title' => ''];
    }

    public function removeWorker(int $index): void
    {
        if (count($this->workers) <= 1) {
            $this->workers[0] = ['id' => null, 'name' => '', 'job_title' => ''];

            return;
        }

        unset($this->workers[$index]);
        $this->workers = array_values($this->workers);
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);

        $this->workers = collect($this->workers)
            ->map(fn (array $worker) => [
                'id' => $worker['id'] ?? null,
                'name' => trim((string) ($worker['name'] ?? '')),
                'job_title' => trim((string) ($worker['job_title'] ?? '')),
            ])
            ->filter(fn (array $worker) => $worker['name'] !== '' || $worker['job_title'] !== '')
            ->values()
            ->all() ?: [['id' => null, 'name' => '', 'job_title' => '']];

        $validated = $this->validate([
            'kode_proyek' => ['required', 'string', 'max:255', Rule::unique('projects', 'kode_proyek')->ignore($this->project)],
            'nama_proyek' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'pic_user_ids' => ['required', 'array', 'min:1'],
            'pic_user_ids.*' => ['integer', 'exists:users,id'],
            'klien' => ['required', 'string', 'max:255'],
            'status_aktif' => ['required', 'boolean'],
            'keterangan' => ['nullable', 'string'],
            'workers' => ['nullable', 'array'],
            'workers.*.id' => ['nullable', 'integer', 'exists:project_workers,id'],
            'workers.*.name' => ['nullable', 'string', 'max:255', 'required_with:workers.*.job_title'],
            'workers.*.job_title' => ['nullable', 'string', 'max:255'],
        ]);

        $project = $this->project ?? new Project;
        $project->fill([
            'kode_proyek' => $validated['kode_proyek'],
            'nama_proyek' => $validated['nama_proyek'],
            'lokasi' => $validated['lokasi'],
            'pic' => User::query()->whereKey($validated['pic_user_ids'])->pluck('name')->join(', '),
            'klien' => $validated['klien'],
            'status_aktif' => $validated['status_aktif'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);
        $project->save();
        $project->pics()->sync($validated['pic_user_ids']);
        $this->syncWorkers($project, $validated['workers'] ?? []);

        session()->flash('status', $this->project ? 'Proyek berhasil diperbarui.' : 'Proyek berhasil ditambahkan.');

        $this->redirectRoute('projects.index', navigate: true);
    }

    public function render(): View
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);

        return view('livewire.projects.project-form', [
            'picUsers' => User::query()
                ->whereHas('roles', fn ($roles) => $roles->whereIn('name', ['admin', 'pic']))
                ->orderBy('name')
                ->get(),
        ])
            ->layout('components.layouts.app');
    }

    protected function syncWorkers(Project $project, array $workers): void
    {
        $keptWorkerIds = [];

        foreach ($workers as $workerData) {
            if (blank($workerData['name'] ?? null)) {
                continue;
            }

            $worker = $project->workers()->updateOrCreate(
                ['id' => $workerData['id'] ?? null],
                [
                    'name' => $workerData['name'],
                    'job_title' => blank($workerData['job_title'] ?? null) ? null : $workerData['job_title'],
                    'active' => true,
                ]
            );

            $keptWorkerIds[] = $worker->id;
        }

        $project->workers()
            ->when($keptWorkerIds !== [], fn ($query) => $query->whereNotIn('id', $keptWorkerIds))
            ->delete();
    }
}
