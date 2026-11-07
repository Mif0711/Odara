<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_toggle_room_status(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'name' => 'Kamar VIP',
            'description' => 'Kamar barang bernilai tinggi',
            'is_active' => '1',
        ])->assertRedirect(route('admin.rooms.index'));

        $room = Room::where('name', 'Kamar VIP')->firstOrFail();
        $this->assertTrue($room->is_active);

        $this->actingAs($admin)->put(route('admin.rooms.update', $room), [
            'name' => 'Kamar VIP Baru',
            'description' => 'Diperbarui',
            'is_active' => '1',
        ])->assertRedirect(route('admin.rooms.index'));

        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'name' => 'Kamar VIP Baru']);

        $this->actingAs($admin)
            ->patch(route('admin.rooms.toggle', $room))
            ->assertRedirect(route('admin.rooms.index'));

        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'is_active' => false]);
    }

    public function test_room_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Room::create(['name' => 'Kamar Sama', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'name' => 'Kamar Sama',
            'is_active' => '1',
        ])->assertSessionHasErrors('name');
    }
}
