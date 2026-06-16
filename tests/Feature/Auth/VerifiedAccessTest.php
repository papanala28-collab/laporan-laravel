<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifiedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_users_are_redirected_to_email_verification_screen(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('verification.notice', absolute: false));
    }
}
