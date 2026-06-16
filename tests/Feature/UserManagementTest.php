<?php

namespace Tests\Feature;

use App\Livewire\Users\UserIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_user_role(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('pic');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();
        $user->assignRole('pic');

        $this->actingAs($admin);

        Livewire::test(UserIndex::class)
            ->call('updateRole', $user->id, 'admin')
            ->assertHasNoErrors();

        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    public function test_pic_cannot_access_user_list(): void
    {
        Role::findOrCreate('pic');

        $user = User::factory()->create();
        $user->assignRole('pic');

        $this->actingAs($user)
            ->get('/users')
            ->assertForbidden();
    }
}
