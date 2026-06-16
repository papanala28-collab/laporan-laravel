<?php

namespace App\Livewire;

use App\Models\DailyReport;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $projectScope = Project::query()
            ->when(! auth()->user()?->hasRole('admin'), fn ($query) => $query->whereHas('pics', fn ($pics) => $pics->whereKey(auth()->id())));

        $reportScope = DailyReport::query()
            ->when(! auth()->user()?->hasRole('admin'), fn ($query) => $query->whereHas('project.pics', fn ($pics) => $pics->whereKey(auth()->id())));

        return view('livewire.dashboard', [
            'activeProjectCount' => (clone $projectScope)->where('status_aktif', true)->count(),
            'reportCountToday' => (clone $reportScope)->whereDate('tanggal', today())->count(),
            'latestReports' => $reportScope
                ->with('project')
                ->latest('tanggal')
                ->latest('created_at')
                ->limit(5)
                ->get(),
        ])->layout('components.layouts.app');
    }
}
