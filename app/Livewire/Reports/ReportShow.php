<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ReportShow extends Component
{
    public DailyReport $report;

    public function mount(DailyReport $report): void
    {
        $report->load('project.pics', 'workerAttendances');

        abort_unless(auth()->user()?->hasRole('admin') || $report->project->pics->contains(auth()->id()), 403);

        $this->report = $report;
    }

    public function render(): View
    {
        return view('livewire.reports.report-show')
            ->layout('components.layouts.app');
    }
}
