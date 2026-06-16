<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReportIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $project = '';

    #[Url(except: '')]
    public string $start_date = '';

    #[Url(except: '')]
    public string $end_date = '';

    public function updatingProject(): void
    {
        $this->resetPage();
    }

    public function updatingStartDate(): void
    {
        $this->resetPage();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
    }

    public function delete(DailyReport $report): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
        $report->delete();
        session()->flash('status', 'Laporan berhasil dihapus.');
    }

    public function render(): View
    {
        $reports = DailyReport::query()
            ->with('project.pics')
            ->when(! auth()->user()?->hasRole('admin'), fn ($query) => $query->whereHas('project.pics', fn ($pics) => $pics->whereKey(auth()->id())))
            ->when($this->project !== '', fn ($query) => $query->where('project_id', $this->project))
            ->when($this->start_date !== '', fn ($query) => $query->whereDate('tanggal', '>=', $this->start_date))
            ->when($this->end_date !== '', fn ($query) => $query->whereDate('tanggal', '<=', $this->end_date))
            ->latest('tanggal')
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.reports.report-index', [
            'reports' => $reports,
            'projects' => Project::query()
                ->when(! auth()->user()?->hasRole('admin'), fn ($query) => $query->whereHas('pics', fn ($pics) => $pics->whereKey(auth()->id())))
                ->orderBy('nama_proyek')
                ->get(),
        ])->layout('components.layouts.app');
    }
}
