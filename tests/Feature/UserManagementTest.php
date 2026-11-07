<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_petugas_account_and_new_account_can_login(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Baru',
            'email' => 'petugas.baru@odara.id',
            'role' => User::ROLE_PETUGAS,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'petugas.baru@odara.id',
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'email' => 'petugas.baru@odara.id',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_admin_can_edit_and_deactivate_petugas(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $petugas), [
            'name' => 'Petugas Diperbarui',
            'email' => $petugas->email,
            'role' => User::ROLE_PETUGAS,
            'password' => '',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $petugas->id,
            'name' => 'Petugas Diperbarui',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle', $petugas))
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($petugas->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle', $admin))
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => User::ROLE_PETUGAS,
            'is_active' => '1',
        ])->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_deactivated_account_cannot_login(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->create(['password' => 'password123']);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle', $petugas));

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'email' => $petugas->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_password_can_be_updated(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $petugas), [
            'name' => $petugas->name,
            'email' => $petugas->email,
            'role' => User::ROLE_PETUGAS,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertTrue(Hash::check('new-password-123', $petugas->fresh()->password));
    }
}
