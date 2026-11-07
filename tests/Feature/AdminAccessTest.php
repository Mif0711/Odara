<?php

namespace Tests\Feature;

use App\Models\ChargerItem;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_cannot_access_admin_pages(): void
    {
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->get(route('admin.rooms.index'))->assertForbidden();
        $this->actingAs($petugas)->get(route('admin.rooms.create'))->assertForbidden();
        $this->actingAs($petugas)->get(route('admin.charger-items.index'))->assertForbidden();
        $this->actingAs($petugas)->get(route('admin.charger-items.create'))->assertForbidden();
        $this->actingAs($petugas)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($petugas)->get(route('admin.users.create'))->assertForbidden();
    }

    public function test_admin_can_access_admin_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $item = ChargerItem::create(['name' => 'Charger HP', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.rooms.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.charger-items.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.charger-items.edit', $item))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    }

    public function test_admin_cannot_access_transaction_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::create(['name' => 'Kamar Transaksi', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger HP', 'is_active' => true]);

        $this->actingAs($admin)->get(route('shopee.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('shopee.create'))->assertForbidden();
        $this->actingAs($admin)->post(route('shopee.store'), [
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Tidak Boleh',
            'room_id' => $room->id,
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 100,
        ])->assertForbidden();

        $this->actingAs($admin)->get(route('charger.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('charger.create'))->assertForbidden();
        $this->actingAs($admin)->post(route('charger.store'), [
            'transaction_date' => '2026-11-05',
            'pin_number' => '1',
            'customer_name' => 'Tidak Boleh',
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'qty' => 1,
        ])->assertForbidden();

        $this->assertDatabaseCount('shopee_transactions', 0);
        $this->assertDatabaseCount('charger_transactions', 0);
    }

    public function test_petugas_cannot_create_rooms_even_by_posting_directly(): void
    {
        $petugas = User::factory()->create();

        $this->actingAs($petugas)
            ->post(route('admin.rooms.store'), ['name' => 'Kamar Ilegal'])
            ->assertForbidden();

        $this->assertDatabaseMissing('rooms', ['name' => 'Kamar Ilegal']);
    }

    public function test_petugas_cannot_create_charger_items_even_by_posting_directly(): void
    {
        $petugas = User::factory()->create();

        $this->actingAs($petugas)
            ->post(route('admin.charger-items.store'), ['name' => 'Barang Ilegal'])
            ->assertForbidden();

        $this->assertDatabaseMissing('charger_items', ['name' => 'Barang Ilegal']);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_deactivated_user_session_is_invalidated(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_petugas_can_open_transaction_forms_with_active_rooms_and_items(): void
    {
        $petugas = User::factory()->create();
        Room::create(['name' => 'Kamar Aktif', 'is_active' => true]);
        ChargerItem::create(['name' => 'Charger Aktif', 'is_active' => true]);

        $this->actingAs($petugas)->get(route('shopee.create'))->assertOk()->assertSee('Kamar Aktif');
        $this->actingAs($petugas)->get(route('charger.create'))->assertOk()->assertSee('Kamar Aktif')->assertSee('Charger Aktif');
    }
}
