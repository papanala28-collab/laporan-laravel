<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function delete(Project $project): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
        $project->delete();
        session()->flash('status', 'Proyek berhasil dihapus.');
    }

    public function render(): View
    {
        $projects = Project::query()
            ->with('pics')
            ->when(! auth()->user()?->hasRole('admin'), fn ($query) => $query->whereHas('pics', fn ($pics) => $pics->whereKey(auth()->id())))
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested
                        ->where('kode_proyek', 'like', '%'.$this->search.'%')
                        ->orWhere('nama_proyek', 'like', '%'.$this->search.'%')
                        ->orWhere('lokasi', 'like', '%'.$this->search.'%')
                        ->orWhere('pic', 'like', '%'.$this->search.'%')
                        ->orWhere('klien', 'like', '%'.$this->search.'%')
                        ->orWhereHas('pics', fn ($pics) => $pics->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status_aktif', $this->status === 'aktif'))
            ->latest()
            ->paginate(10);

        return view('livewire.projects.project-index', [
            'projects' => $projects,
        ])->layout('components.layouts.app');
    }
}
