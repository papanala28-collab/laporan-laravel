<?php

namespace App\Livewire\Projects;

use App\Models\DailyReport;
use App\Models\Project;
use App\Services\ProjectAttendanceMatrix;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectShow extends Component
{
    use WithPagination;

    public Project $project;

    #[Url(except: '')]
    public string $start_date = '';

    #[Url(except: '')]
    public string $end_date = '';

    public function mount(Project $project): void
    {
        $project->load('pics');

        abort_unless(auth()->user()?->hasRole('admin') || $project->pics->contains(auth()->id()), 403);

        $this->project = $project;
        $this->start_date = $this->start_date ?: now()->startOfMonth()->toDateString();
        $this->end_date = $this->end_date ?: now()->toDateString();
    }

    public function updatingStartDate(): void
    {
        $this->resetPage();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
    }

    public function render(ProjectAttendanceMatrix $matrix): View
    {
        $reportsQuery = DailyReport::query()
            ->with('workerAttendances')
            ->where('project_id', $this->project->id)
            ->when($this->start_date !== '', fn ($query) => $query->whereDate('tanggal', '>=', $this->start_date))
            ->when($this->end_date !== '', fn ($query) => $query->whereDate('tanggal', '<=', $this->end_date));

        $attendanceMatrix = $matrix->build($this->project, $this->start_date, $this->end_date);

        $projectPhotos = (clone $reportsQuery)->get(['id', 'photos', 'tanggal'])
            ->flatMap(function ($report) {
                return collect($report->photoUrls())->map(fn ($url) => [
                    'url' => $url,
                    'date' => $report->tanggal,
                    'report_id' => $report->id,
                ]);
            })
            ->all();

        return view('livewire.projects.project-show', [
            'reports' => $reportsQuery
                ->latest('tanggal')
                ->latest('created_at')
                ->paginate(8),
            'projectPhotos' => $projectPhotos,
            'attendanceDates' => $attendanceMatrix['dates'],
            'attendanceRows' => $attendanceMatrix['rows'],
            'attendanceStatuses' => $attendanceMatrix['statuses'],
        ])->layout('components.layouts.app');
    }
}
