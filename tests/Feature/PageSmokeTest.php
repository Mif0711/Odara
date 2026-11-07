<?php

namespace Tests\Feature;

use App\Models\ChargerItem;
use App\Models\ChargerTransaction;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_pages_render_successfully(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::create(['name' => 'Kamar Smoke', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger Smoke', 'is_active' => true]);

        $this->actingAs($admin);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('admin.rooms.index'))->assertOk();
        $this->get(route('admin.rooms.create'))->assertOk();
        $this->get(route('admin.rooms.edit', $room))->assertOk();
        $this->get(route('admin.charger-items.index'))->assertOk();
        $this->get(route('admin.charger-items.create'))->assertOk();
        $this->get(route('admin.charger-items.edit', $item))->assertOk();
        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.users.create'))->assertOk();
        $this->get(route('admin.users.edit', $admin))->assertOk();
        $this->get(route('admin.reports.shopee'))->assertOk();
        $this->get(route('admin.reports.charger'))->assertOk();

        $this->get(route('shopee.index'))->assertForbidden();
        $this->get(route('shopee.create'))->assertForbidden();
        $this->get(route('charger.index'))->assertForbidden();
        $this->get(route('charger.create'))->assertForbidden();
    }

    public function test_petugas_pages_render_successfully(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Petugas', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger Petugas', 'is_active' => true]);

        $charger = ChargerTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'transaction_date' => now()->toDateString(),
            'pin_number' => '1',
            'customer_name' => 'Pelanggan Charger',
            'item_type' => $item->name,
            'qty' => 2,
            'is_picked_up' => true,
            'notes' => 'Catatan charger',
        ]);

        $this->actingAs($petugas);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('shopee.index'))->assertOk();
        $this->get(route('shopee.create'))->assertOk();
        $this->get(route('charger.index'))->assertOk();
        $this->get(route('charger.create'))->assertOk();
        $this->get(route('charger.edit', $charger))->assertOk();
    }
}
