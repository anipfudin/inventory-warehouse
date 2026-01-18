<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number'); // PO atau SO number
            $table->string('reference_type'); // 'PURCHASE_ORDER', 'SALES_ORDER'
            $table->enum('type', ['IN', 'OUT']); // masuk atau keluar
            $table->foreignId('item_id')->constrained('items')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
