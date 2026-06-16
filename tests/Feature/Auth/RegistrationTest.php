<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('verification.notice', absolute: false));

        $this->assertAuthenticated();
        $this->assertTrue(User::query()->where('email', 'test@example.com')->firstOrFail()->hasRole('admin'));
    }

    public function test_users_after_first_registration_are_assigned_pic_role(): void
    {
        User::factory()->create();

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Second User')
            ->set('email', 'second@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('verification.notice', absolute: false));

        $this->assertTrue(User::query()->where('email', 'second@example.com')->firstOrFail()->hasRole('pic'));
    }
}
