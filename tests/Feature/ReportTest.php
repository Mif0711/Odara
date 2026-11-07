<?php

namespace Tests\Feature;

use App\Models\ChargerItem;
use App\Models\ChargerTransaction;
use App\Models\Room;
use App\Models\ShopeeTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_shopee_and_charger_reports_with_all_petugas_activity(): void
    {
        $admin = User::factory()->admin()->create();
        $petugasA = User::factory()->create(['name' => 'Petugas Alpha']);
        $petugasB = User::factory()->create(['name' => 'Petugas Beta']);
        $room = Room::create(['name' => 'Kamar Laporan', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger Laporan', 'is_active' => true]);

        ShopeeTransaction::create([
            'user_id' => $petugasA->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Pelanggan November A',
            'item_type' => 'Barang A',
            'item_price' => 5000,
            'service_fee' => 3000,
            'is_picked_up' => true,
        ]);

        ShopeeTransaction::create([
            'user_id' => $petugasB->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-08',
            'customer_name' => 'Pelanggan November B',
            'item_type' => 'Barang B',
            'item_price' => 10000,
            'service_fee' => 2000,
            'is_picked_up' => false,
        ]);

        ShopeeTransaction::create([
            'user_id' => $petugasB->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-10-10',
            'customer_name' => 'Pelanggan Oktober',
            'item_type' => 'Barang C',
            'item_price' => 1000,
            'service_fee' => 0,
        ]);

        $this->chargerTransaction($petugasA, $room, $item, 'Charger November A', '2026-11-05', 2, true);
        $this->chargerTransaction($petugasB, $room, $item, 'Charger November B', '2026-11-07', 3, false);
        $this->chargerTransaction($petugasB, $room, $item, 'Charger Oktober', '2026-10-09', 4, false);

        $this->actingAs($admin)
            ->get(route('admin.reports.shopee', ['month' => 11, 'year' => 2026]))
            ->assertOk()
            ->assertSee('Laporan Shopee')
            ->assertSee('Petugas Alpha')
            ->assertSee('Petugas Beta')
            ->assertSee('Rp 8.000')
            ->assertSee('Rp 12.000')
            ->assertSee('Rp 21.000')
            ->assertSee('Pelanggan November A')
            ->assertSee('Pelanggan November B')
            ->assertDontSee('Pelanggan Oktober');

        $this->actingAs($admin)
            ->get(route('admin.reports.charger', ['month' => 11, 'year' => 2026]))
            ->assertOk()
            ->assertSee('Laporan Charger')
            ->assertSee('Petugas Alpha')
            ->assertSee('Petugas Beta')
            ->assertSee('Charger November A')
            ->assertSee('Charger November B')
            ->assertDontSee('Charger Oktober')
            ->assertSee('Total QTY Tahun 2026');
    }

    public function test_report_month_filter_changes_the_period_totals(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Filter Laporan', 'is_active' => true]);

        ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Transaksi November',
            'item_type' => 'Barang',
            'item_price' => 5000,
            'service_fee' => 3000,
        ]);

        ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-10-05',
            'customer_name' => 'Transaksi Oktober',
            'item_type' => 'Barang',
            'item_price' => 1000,
            'service_fee' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.shopee', ['month' => 10, 'year' => 2026]))
            ->assertOk()
            ->assertSee('Transaksi Oktober')
            ->assertDontSee('Transaksi November')
            ->assertSee('Rp 1.000');

        $this->actingAs($admin)
            ->get(route('admin.reports.shopee', ['month' => 11, 'year' => 2026]))
            ->assertOk()
            ->assertSee('Transaksi November')
            ->assertDontSee('Transaksi Oktober')
            ->assertSee('Rp 8.000');
    }

    public function test_petugas_cannot_open_reports(): void
    {
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->get(route('admin.reports.shopee'))->assertForbidden();
        $this->actingAs($petugas)->get(route('admin.reports.charger'))->assertForbidden();
    }

    public function test_shopee_currency_input_accepts_indonesian_thousand_separators(): void
    {
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Rupiah', 'is_active' => true]);

        $this->actingAs($petugas)->post(route('shopee.store'), [
            'transaction_date' => '2026-11-05',
            'customer_name' => 'Pelanggan Rupiah',
            'room_id' => $room->id,
            'item_type' => 'Barang',
            'item_price' => '5.000',
            'service_fee' => '3.000',
        ])->assertRedirect(route('shopee.index'));

        $this->assertDatabaseHas('shopee_transactions', [
            'customer_name' => 'Pelanggan Rupiah',
            'item_price' => 5000,
            'service_fee' => 3000,
        ]);

        $this->actingAs($petugas)
            ->get(route('shopee.index'))
            ->assertOk()
            ->assertSee('Rp 8.000');
    }

    public function test_shopee_transaction_counts_are_not_overridden_by_the_total_accessor(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Hitung', 'is_active' => true]);

        foreach (['2026-11-01', '2026-11-02'] as $date) {
            ShopeeTransaction::create([
                'user_id' => $petugas->id,
                'room_id' => $room->id,
                'transaction_date' => $date,
                'customer_name' => 'Pelanggan Hitung',
                'item_type' => 'Barang',
                'item_price' => 5000,
                'service_fee' => 1000,
            ]);
        }

        $report = $this->actingAs($admin)
            ->get(route('admin.reports.shopee', ['month' => 11, 'year' => 2026]))
            ->assertOk();

        $this->assertSame(2, (int) $report->viewData('monthSummary')->transaction_total);
        $this->assertSame(2, (int) $report->viewData('yearSummary')->transaction_total);
        $this->assertSame(2, (int) $report->viewData('monthlyRows')->get(11)->transaction_total);
        $this->assertSame(2, (int) $report->viewData('officerRows')->first()->transaction_total);

        $index = $this->actingAs($petugas)
            ->get(route('shopee.index'))
            ->assertOk();

        $this->assertSame(2, (int) $index->viewData('summary')->transaction_total);
    }

    public function test_admin_can_export_shopee_and_charger_reports_as_excel(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->create();
        $room = Room::create(['name' => 'Kamar Export', 'is_active' => true]);
        $item = ChargerItem::create(['name' => 'Charger Export', 'is_active' => true]);

        ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-11-05',
            'receipt_number' => 'RESI-EXPORT',
            'customer_name' => 'Pelanggan Export Shopee',
            'item_type' => 'Barang Export',
            'item_price' => 5000,
            'service_fee' => 1000,
            'is_picked_up' => true,
        ]);

        ShopeeTransaction::create([
            'user_id' => $petugas->id,
            'room_id' => $room->id,
            'transaction_date' => '2026-10-05',
            'customer_name' => 'Pelanggan Luar Periode',
            'item_type' => 'Barang Lain',
            'item_price' => 9000,
            'service_fee' => 0,
        ]);

        $this->chargerTransaction($petugas, $room, $item, 'Pelanggan Export Charger', '2026-11-06', 4, false);

        $shopee = $this->actingAs($admin)
            ->get(route('admin.reports.shopee.export', ['month' => 11, 'year' => 2026]))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertDownload('laporan-shopee-2026-11.xlsx');

        $shopeeXlsx = $shopee->streamedContent();

        $this->assertStringStartsWith("PK\x03\x04", $shopeeXlsx);
        $this->assertStringContainsString('xl/worksheets/sheet1.xml', $shopeeXlsx);
        $this->assertStringContainsString('Laporan Shopee', $shopeeXlsx);
        $this->assertStringContainsString('November 2026', $shopeeXlsx);
        $this->assertStringContainsString('Pelanggan Export Shopee', $shopeeXlsx);
        $this->assertStringContainsString('RESI-EXPORT', $shopeeXlsx);
        $this->assertStringContainsString('<v>5000</v>', $shopeeXlsx);
        $this->assertStringContainsString('TOTAL', $shopeeXlsx);
        $this->assertStringNotContainsString('Pelanggan Luar Periode', $shopeeXlsx);

        $charger = $this->actingAs($admin)
            ->get(route('admin.reports.charger.export', ['month' => 11, 'year' => 2026]))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertDownload('laporan-charger-2026-11.xlsx');

        $chargerXlsx = $charger->streamedContent();

        $this->assertStringStartsWith("PK\x03\x04", $chargerXlsx);
        $this->assertStringContainsString('Laporan Charger', $chargerXlsx);
        $this->assertStringContainsString('Pelanggan Export Charger', $chargerXlsx);
        $this->assertStringContainsString('Charger Export', $chargerXlsx);
        $this->assertStringContainsString('TOTAL', $chargerXlsx);
        $this->assertStringContainsString('<v>4</v>', $chargerXlsx);
    }

    public function test_petugas_cannot_export_reports(): void
    {
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->get(route('admin.reports.shopee.export'))->assertForbidden();
        $this->actingAs($petugas)->get(route('admin.reports.charger.export'))->assertForbidden();
    }

    private function chargerTransaction(
        User $user,
        Room $room,
        ChargerItem $item,
        string $customerName,
        string $date,
        int $qty,
        bool $isPickedUp,
    ): ChargerTransaction {
        return ChargerTransaction::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'charger_item_id' => $item->id,
            'transaction_date' => $date,
            'customer_name' => $customerName,
            'item_type' => $item->name,
            'qty' => $qty,
            'is_picked_up' => $isPickedUp,
        ]);
    }
}
