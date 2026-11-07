<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopee_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->date('transaction_date')->index();
            $table->string('receipt_number', 100)->nullable();
            $table->string('customer_name', 150)->index();
            $table->string('item_type', 150);
            $table->decimal('item_price', 15, 2)->default(0);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopee_transactions');
    }
};
