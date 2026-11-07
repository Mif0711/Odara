<?php

namespace Tests\Feature;

use App\Models\ChargerItem;
use App\Models\ChargerTransaction;
use App\Models\Room;
use App\Models\ShopeeTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_can_create_shopee_transaction_with_manual_customer_and_item_type(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar A', 'is_active' => true]);

        $response = $this->actingAs($petugas)->post(route('shopee.store'), [
            'transaction_date' => '2026-11-05',
            'receipt_number' => 'RESI-001',
            'customer_name' => 'Budi Santoso',
            'room_id' => $room->id,
            'item_type' => 'Pakaian',
            'item_price' => 150000,
            'service_fee' => 10000,
            'is_picked_up' => '1',
            'notes' => 'Barang mudah pecah',
        ]);

        $response->assertRedirect(route('shopee.index'));
        $this->assertDatabaseHas('shopee_transactions', [
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'receipt_number' => 'RESI-001',
            'customer_name' => 'Budi Santoso',
            'item_type' => 'Pakaian',
            'is_picked_up' => true,
        ]);
    }

    public function test_petugas_can_create_charger_transaction_with_master_item(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar B', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger HP', 'is_active' => true]);

        $response = $this->actingAs($petugas)->post(route('charger.store'), [
            'transaction_date' => '2026-11-05',
            'pin_number' => '12',
            'customer_name' => 'Siti Aminah',
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'qty' => 3,
            'is_picked_up' => '1',
            'notes' => 'Titip 3 unit',
        ]);

        $response->assertRedirect(route('charger.index'));
        $this->assertDatabaseHas('charger_transactions', [
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'pin_number' => '12',
            'customer_name' => 'Siti Aminah',
            'item_type' => 'Charger HP',
            'qty' => 3,
            'is_picked_up' => true,
        ]);
    }

    public function test_charger_pin_must_be_between_1_and_30(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar PIN', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger PIN', 'is_active' => true]);

        $payload = [
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Pelanggan PIN',
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'qty' => 1,
        ];

        $this->actingAs($petugas)
            ->post(route('charger.store'), $payload + ['pin_number' => '31'])
            ->assertSessionHasErrors('pin_number');

        $this->actingAs($petugas)
            ->post(route('charger.store'), $payload + ['pin_number' => '0'])
            ->assertSessionHasErrors('pin_number');

        $this->actingAs($petugas)
            ->post(route('charger.store'), $payload)
            ->assertSessionHasErrors('pin_number');

        $this->assertDatabaseCount('charger_transactions', 0);
    }

    public function test_petugas_only_sees_their_own_shopee_transactions(): void
    {
        $petugasA = User::factory()->create();
        $petugasB = User::factory()->create();
        $room = Room::create(['name' => 'Kamar C', 'is_active' => true]);

        ShopeeTransaction::create([
            'user_id' => $petugasA->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Pelanggan A',
            'item_type' => 'Sepatu',
            'item_price' => 10000,
            'service_fee' => 1000,
        ]);

        ShopeeTransaction::create([
            'user_id' => $petugasB->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Pelanggan B',
            'item_type' => 'Tas',
            'item_price' => 20000,
            'service_fee' => 2000,
        ]);

        $this->actingAs($petugasA)
            ->get(route('shopee.index'))
            ->assertOk()
            ->assertSee('Pelanggan A')
            ->assertDontSee('Pelanggan B');
    }

    public function test_petugas_only_sees_their_own_charger_transactions(): void
    {
        $petugasA = User::factory()->create();
        $petugasB = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Charger', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger HP', 'is_active' => true]);

        $this->chargerTransaction($petugasA, $room, $item, 'Charger Milik A');
        $this->chargerTransaction($petugasB, $room, $item, 'Charger Milik B');

        $this->actingAs($petugasA)
            ->get(route('charger.index'))
            ->assertOk()
            ->assertSee('Charger Milik A')
            ->assertDontSee('Charger Milik B');
    }

    public function test_shopee_single_date_filter_uses_database_query(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar E', 'is_active' => true]);

        foreach (['2026-11-01', '2026-11-05', '2026-11-10'] as $date) {
            ShopeeTransaction::create([
                'user_id' => $petugas->id,
                'room_id' => $room->id,
                'transaction_date' => $date,
                'customer_name' => 'Tanggal '.$date,
                'item_type' => 'Barang',
                'item_price' => 1000,
                'service_fee' => 100,
            ]);
        }

        $this->actingAs($petugas)
            ->get(route('shopee.index', ['date' => '2026-11-05']))
            ->assertOk()
            ->assertSee('Tanggal 2026-11-05')
            ->assertSee('05 Nov 2026')
            ->assertDontSee('Tanggal 2026-11-01')
            ->assertDontSee('Tanggal 2026-11-10');
    }

    public function test_shopee_range_date_filter_uses_database_query(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar F', 'is_active' => true]);

        foreach (['2026-11-01', '2026-11-05', '2026-11-10'] as $date) {
            ShopeeTransaction::create([
                'user_id' => $petugas->id,
                'room_id' => $room->id,
                'transaction_date' => $date,
                'customer_name' => 'Rentang '.$date,
                'item_type' => 'Barang',
                'item_price' => 1000,
                'service_fee' => 100,
            ]);
        }

        $this->actingAs($petugas)
            ->get(route('shopee.index', ['start_date' => '2026-11-04', 'end_date' => '2026-11-07']))
            ->assertOk()
            ->assertSee('Rentang 2026-11-05')
            ->assertDontSee('Rentang 2026-11-01')
            ->assertDontSee('Rentang 2026-11-10');
    }

    public function test_charger_date_filter_uses_database_query(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar G', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger', 'is_active' => true]);

        foreach (['2026-11-01', '2026-11-05', '2026-11-10'] as $date) {
            $this->chargerTransaction($petugas, $room, $item, 'Charger '.$date, $date);
        }

        $this->actingAs($petugas)
            ->get(route('charger.index', ['date' => '2026-11-10']))
            ->assertOk()
            ->assertSee('Charger 2026-11-10')
            ->assertDontSee('Charger 2026-11-01')
            ->assertDontSee('Charger 2026-11-05');
    }

    public function test_inactive_room_cannot_be_used_for_new_transaction(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Nonaktif', 'is_active' => false]);

        $this->actingAs($petugas)->post(route('shopee.store'), [
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Tidak Boleh',
            'room_id' => $room->id,
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 100,
        ])->assertSessionHasErrors('room_id');

        $this->assertDatabaseCount('shopee_transactions', 0);
    }

    public function test_inactive_charger_item_cannot_be_used_for_new_transaction(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Item', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Barang Nonaktif', 'is_active' => false]);

        $this->actingAs($petugas)->post(route('charger.store'), [
            'transaction_date' => '2026-11-05',
            'pin_number' => '1',
            'customer_name' => 'Tidak Boleh',
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'qty' => 1,
        ])->assertSessionHasErrors('charger_item_id');

        $this->assertDatabaseCount('charger_transactions', 0);
    }

    public function test_inactive_charger_item_is_hidden_from_new_form_but_kept_on_old_transaction(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Lama', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Barang Lama', 'is_active' => true]);

        $transaction = $this->chargerTransaction($petugas, $room, $item, 'Pelanggan Lama');

        $item->update(['is_active' => false]);

        $this->actingAs($petugas)
            ->get(route('charger.create'))
            ->assertOk()
            ->assertDontSee('Barang Lama');

        $this->actingAs($petugas)
            ->get(route('charger.index'))
            ->assertOk()
            ->assertSee('Barang Lama')
            ->assertSee('Pelanggan Lama');

        $this->actingAs($petugas)
            ->get(route('charger.edit', $transaction))
            ->assertOk()
            ->assertSee('Barang Lama');
    }

    public function test_inactive_room_is_hidden_from_new_transaction_form_but_kept_on_old_transaction(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Lama', 'is_active' => true]);

        ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Pelanggan Lama',
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 100,
        ]);

        $room->update(['is_active' => false]);

        $this->actingAs($petugas)
            ->get(route('shopee.create'))
            ->assertOk()
            ->assertDontSee('Kamar Lama');

        $this->actingAs($petugas)
            ->get(route('shopee.index'))
            ->assertOk()
            ->assertSee('Kamar Lama')
            ->assertSee('Pelanggan Lama');
    }

    public function test_petugas_can_update_and_delete_own_shopee_transaction(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Petugas Only', 'is_active' => true]);

        $transaction = ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Milik Sendiri',
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 100,
        ]);

        $this->actingAs($petugas)
            ->get(route('shopee.edit', $transaction))
            ->assertOk();

        $this->actingAs($petugas)->put(route('shopee.update', $transaction), [
            'transaction_date' => '2026-11-06',
            'customer_name' => 'Nama Baru',
            'room_id' => $room->id,
            'item_type' => 'Barang Baru',
            'item_price' => 2000,
            'service_fee' => 200,
            'is_picked_up' => '1',
        ])->assertRedirect(route('shopee.index'));

        $this->assertDatabaseHas('shopee_transactions', [
            'id' => $transaction->id,
            'customer_name' => 'Nama Baru',
            'item_type' => 'Barang Baru',
            'is_picked_up' => true,
        ]);

        $this->actingAs($petugas)
            ->delete(route('shopee.destroy', $transaction))
            ->assertRedirect(route('shopee.index'));

        $this->assertDatabaseMissing('shopee_transactions', ['id' => $transaction->id]);
    }

    public function test_petugas_cannot_modify_another_petugas_shopee_transaction(): void
    {
        $petugasA = User::factory()->create();
        $petugasB = User::factory()->create();
        $room = Room::create(['name' => 'Kamar H', 'is_active' => true]);

        $transaction = ShopeeTransaction::create([
            'user_id' => $petugasB->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Milik B',
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 100,
        ]);

        $this->actingAs($petugasA)
            ->get(route('shopee.edit', $transaction))
            ->assertForbidden();

        $this->actingAs($petugasA)
            ->delete(route('shopee.destroy', $transaction))
            ->assertForbidden();

        $this->assertDatabaseHas('shopee_transactions', ['id' => $transaction->id]);
    }

    public function test_petugas_can_update_and_delete_own_charger_transaction(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar I', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger A', 'is_active' => true]);
        $newItem = ChargerItem::create(['name' => 'Charger B', 'is_active' => true]);

        $transaction = $this->chargerTransaction($petugas, $room, $item, 'Nama Awal');

        $this->actingAs($petugas)->put(route('charger.update', $transaction), [
            'transaction_date' => '2026-11-06',
            'pin_number' => '2',
            'customer_name' => 'Nama Baru',
            'room_id' => $room->id,
            'charger_item_id' => $newItem->id,
            'qty' => 2,
            'is_picked_up' => '1',
        ])->assertRedirect(route('charger.index'));

        $this->assertDatabaseHas('charger_transactions', [
            'id' => $transaction->id,
            'customer_name' => 'Nama Baru',
            'charger_item_id' => $newItem->id,
            'item_type' => 'Charger B',
            'pin_number' => '2',
            'qty' => 2,
            'is_picked_up' => true,
        ]);

        $this->actingAs($petugas)
            ->delete(route('charger.destroy', $transaction))
            ->assertRedirect(route('charger.index'));

        $this->assertDatabaseMissing('charger_transactions', ['id' => $transaction->id]);
    }

    public function test_petugas_cannot_modify_another_petugas_charger_transaction(): void
    {
        $petugasA = User::factory()->create();
        $petugasB = User::factory()->create();
        $room = Room::create(['name' => 'Kamar J', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger', 'is_active' => true]);

        $transaction = $this->chargerTransaction($petugasB, $room, $item, 'Milik B');

        $this->actingAs($petugasA)
            ->get(route('charger.edit', $transaction))
            ->assertForbidden();

        $this->actingAs($petugasA)
            ->delete(route('charger.destroy', $transaction))
            ->assertForbidden();

        $this->assertDatabaseHas('charger_transactions', ['id' => $transaction->id]);
    }

    public function test_pickup_status_is_shown_in_transaction_lists(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Status', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger', 'is_active' => true]);

        ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Belum Ambil',
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 100,
            'is_picked_up' => false,
        ]);

        $this->chargerTransaction($petugas, $room, $item, 'Sudah Ambil', '2026-11-05', true);

        $this->actingAs($petugas)
            ->get(route('shopee.index'))
            ->assertOk()
            ->assertSee('Belum diambil');

        $this->actingAs($petugas)
            ->get(route('charger.index'))
            ->assertOk()
            ->assertSee('Sudah diambil');
    }

    public function test_pickup_status_can_be_toggled_by_shopee_owner_only(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->create();
        $otherPetugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Pickup', 'is_active' => true]);

        $shopee = ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Pelanggan Pickup',
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 100,
            'is_picked_up' => false,
        ]);

        $this->actingAs($otherPetugas)
            ->patch(route('shopee.pickup', $shopee))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('shopee.pickup', $shopee))
            ->assertForbidden();

        $this->actingAs($petugas)
            ->patch(route('shopee.pickup', $shopee))
            ->assertRedirect();

        $this->assertDatabaseHas('shopee_transactions', [
            'id' => $shopee->id,
            'is_picked_up' => true,
        ]);
    }

    public function test_pickup_status_can_be_toggled_by_charger_owner(): void
    {
        $petugas = User::factory()->create();
        $otherPetugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Pickup Charger', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger', 'is_active' => true]);

        $charger = $this->chargerTransaction($petugas, $room, $item, 'Pelanggan Pickup');

        $this->actingAs($otherPetugas)
            ->patch(route('charger.pickup', $charger))
            ->assertForbidden();

        $this->actingAs($petugas)
            ->patch(route('charger.pickup', $charger))
            ->assertRedirect();

        $this->assertDatabaseHas('charger_transactions', [
            'id' => $charger->id,
            'is_picked_up' => true,
        ]);
    }

    private function chargerTransaction(
        User $user,
        Room $room,
        ChargerItem $item,
        string $customerName,
        string $date = '2026-11-05',
        bool $isPickedUp = false,
    ): ChargerTransaction {
        return ChargerTransaction::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'transaction_date' => $date,
            'pin_number' => '1',
            'customer_name' => $customerName,
            'item_type' => $item->name,
            'qty' => 1,
            'is_picked_up' => $isPickedUp,
        ]);
    }
}
