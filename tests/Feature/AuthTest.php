<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_setup_when_database_is_empty(): void
    {
        $this->get('/')->assertRedirect(route('setup'));
        $this->get(route('login'))->assertRedirect(route('setup'));
        $this->get(route('setup'))->assertOk();
    }

    public function test_first_admin_can_be_created_from_setup_page(): void
    {
        $response = $this->post(route('setup.store'), [
            'name' => 'Admin ODARA',
            'email' => 'admin@odara.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'admin@odara.id',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_active_internal_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'petugas@odara.id',
            'password' => 'password123',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_internal_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'nonaktif@odara.id',
            'password' => 'password123',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_with_wrong_password_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'petugas2@odara.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password-salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
