<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shopee_transactions', function (Blueprint $table) {
            $table->boolean('is_picked_up')->default(false)->after('notes')->index();
        });

        Schema::table('charger_transactions', function (Blueprint $table) {
            $table->boolean('is_picked_up')->default(false)->after('notes')->index();
        });
    }

    public function down(): void
    {
        Schema::table('shopee_transactions', function (Blueprint $table) {
            $table->dropColumn('is_picked_up');
        });

        Schema::table('charger_transactions', function (Blueprint $table) {
            $table->dropColumn('is_picked_up');
        });
    }
};
