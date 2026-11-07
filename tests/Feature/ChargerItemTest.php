<?php

namespace Tests\Feature;

use App\Models\ChargerItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChargerItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_toggle_charger_item(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.charger-items.store'), [
            'name' => 'Charger HP',
            'description' => 'Charger pelanggan',
            'is_active' => '1',
        ])->assertRedirect(route('admin.charger-items.index'));

        $item = ChargerItem::where('name', 'Charger HP')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.charger-items.update', $item), [
            'name' => 'Charger Laptop',
            'description' => 'Charger laptop pelanggan',
        ])->assertRedirect(route('admin.charger-items.index'));

        $this->assertDatabaseHas('charger_items', [
            'id' => $item->id,
            'name' => 'Charger Laptop',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.charger-items.toggle', $item))
            ->assertRedirect(route('admin.charger-items.index'));

        $this->assertDatabaseHas('charger_items', [
            'id' => $item->id,
            'is_active' => true,
        ]);
    }

    public function test_charger_item_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        ChargerItem::create(['name' => 'Charger HP', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.charger-items.store'), [
            'name' => 'Charger HP',
        ])->assertSessionHasErrors('name');

        $this->assertDatabaseCount('charger_items', 1);
    }
}
