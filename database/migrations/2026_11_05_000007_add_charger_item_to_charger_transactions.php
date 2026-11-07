<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('charger_transactions', function (Blueprint $table) {
            $table->foreignId('charger_item_id')
                ->nullable()
                ->after('room_id')
                ->constrained('charger_items')
                ->nullOnDelete();
        });

        Schema::table('charger_transactions', function (Blueprint $table) {
            $table->dropColumn(['item_price', 'service_fee']);
        });
    }

    public function down(): void
    {
        Schema::table('charger_transactions', function (Blueprint $table) {
            $table->decimal('item_price', 15, 2)->default(0);
            $table->decimal('service_fee', 15, 2)->default(0);
        });

        Schema::table('charger_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('charger_item_id');
        });
    }
};
