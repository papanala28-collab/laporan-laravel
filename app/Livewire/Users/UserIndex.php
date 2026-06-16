<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);

        Role::findOrCreate('admin');
        Role::findOrCreate('pic');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updateRole(int $userId, string $role): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
        abort_unless(in_array($role, ['admin', 'pic'], true), 422);

        $user = User::query()->findOrFail($userId);
        $user->syncRoles([$role]);

        session()->flash('status', 'Role user berhasil diperbarui.');
    }

    public function render(): View
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);

        $users = User::query()
            ->with('roles')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.users.user-index', [
            'users' => $users,
        ])->layout('components.layouts.app');
    }
}
